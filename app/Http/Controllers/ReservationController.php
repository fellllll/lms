<?php

namespace App\Http\Controllers;

use App\Models\Reserve;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ReservationController extends Controller
{
// ===================== RESERVATION STATUS =====================
//
// WAITING
// - User masuk antrian (FIFO)
// - Quota TIDAK berubah
// - Menunggu slot tersedia pada tanggal yang dipilih
// - Color: Yellow
//
// BORROWED
// - Buku sedang dipinjam (aktif)
// - Quota SUDAH berkurang
// - waktu_pinjam & waktu_kembali WAJIB ada (2 minggu)
// - Bisa RETURN
// - Color: Blue
//
// RESERVED
// - Booking untuk tanggal future
// - Buku BELUM dipinjam
// - Quota TIDAK berkurang
// - Akan jadi BORROWED saat tanggal mulai tercapai
// - Bisa CANCEL
// - Color: Purple
//
// RETURNED
// - Buku sudah dikembalikan
// - Quota SUDAH dikembalikan
// - Status final (riwayat)
// - Color: Green
//
// CANCELLED
// - Reservasi dibatalkan oleh user/admin
// - Jika sebelumnya BORROWED → quota dikembalikan
// - Status final (riwayat)
// - Color: Red
// ===============================================================



    public function show($id){
        $book = DB::table('books')->where('id',decrypt($id))->get()->value('title');
        
        $name = Auth::user()->name;

        return view('book.reservation', compact('book', 'id', 'name'));
    }

    // public function view()
    // {
    //     $reserves = DB::table('reserves')->where('user_id', Auth::user()->id)->get();

    //     if (Auth::user()->role_id==1){
    //         $reserves = Reserve::all();
    //     }else{
    //         $reserves = Reserve::where('user_id', Auth::user()->id)->get();
    //     }
    //     return view('book.reservationView', compact('reserves'));
    // }

    public function view(){
        if (Auth::user()->role_id == 1) {
            $reserves = Reserve::with(['user', 'book'])->get();
        } else {
            $reserves = Reserve::with(['user', 'book'])
                ->where('user_id', Auth::id())
                ->get();
        }

        // ===== Queue number map untuk WAITING (per reserve_id) =====
        $queueByReserveId = [];

        $bookIds = $reserves->pluck('book_id')->unique()->values()->all();

        if (!empty($bookIds)) {
            $waitingRows = Reserve::whereIn('book_id', $bookIds)
                ->where('status', 'WAITING')
                ->orderBy('book_id')
                ->orderBy('created_at')
                ->orderBy('id')
                ->get(['id', 'book_id']);

            $counter = [];
            foreach ($waitingRows as $row) {
                $bid = $row->book_id;
                $counter[$bid] = ($counter[$bid] ?? 0) + 1;
                $queueByReserveId[$row->id] = $counter[$bid]; // reserve_id -> posisi
            }
        }

        return view('book.reservationView', compact('reserves', 'queueByReserveId'));
    }

    // public function returnBook($id){
    //     $reserveId = decrypt($id);

    //     DB::transaction(function () use ($reserveId) {
    //         $reserve = Reserve::with('book')->lockForUpdate()->findOrFail($reserveId);

    //         // user biasa cuma boleh return miliknya
    //         if (Auth::user()->role_id != 1 && $reserve->user_id != Auth::id()) {
    //             abort(403);
    //         }

    //         if ($reserve->status !== 'BORROWED') {
    //             return;
    //         }

    //         $book = $reserve->book;

    //         // 1) mark returned
    //         $reserve->status = 'RETURNED';
    //         $reserve->save();

    //         // 2) quota++
    //         $book->quota = $book->quota + 1;
    //         $book->save();

    //         // 3) promote FIFO waiting (auto-convert)
    //         $next = Reserve::where('book_id', $book->id)
    //             ->where('status', 'WAITING')
    //             ->orderBy('created_at')
    //             ->orderBy('id')
    //             ->lockForUpdate()
    //             ->first();

    //         if ($next && $book->quota > 0) {
    //             $next->status = 'BORROWED';
    //             $next->waktu_pinjam = now()->toDateString();
    //             $next->waktu_kembali = now()->addWeeks(2)->toDateString();
    //             $next->save();

    //             $book->quota = $book->quota - 1;
    //             $book->save();
    //         }
    //     });

    //     return back()->with('success', 'Buku berhasil dikembalikan.');
    // }

    
    public function returnBook($id){
        $reserveId = decrypt($id);

        DB::transaction(function () use ($reserveId) {
            $reserve = Reserve::with('book')->lockForUpdate()->findOrFail($reserveId);

            if (Auth::user()->role_id != 1 && $reserve->user_id != Auth::id()) {
                abort(403);
            }

            if ($reserve->status !== 'BORROWED') {
                return;
            }

            $bookId = $reserve->book_id;

            // 1) RETURNED
            $reserve->status = 'RETURNED';
            $reserve->save();

            // 2) quota++
            $bookRow = DB::table('books')->where('id', $bookId)->lockForUpdate()->first();
            DB::table('books')->where('id', $bookId)->update([
                'quota' => (int)$bookRow->quota + 1
            ]);

            // 3) promote FIFO waiting
            $this->promoteWaitingForBook($bookId);
        });

        return back()->with('success', 'Buku berhasil dikembalikan.');
    }

    public function cancelWaiting($id)
    {
        $reserveId = decrypt($id);

        $reserve = Reserve::findOrFail($reserveId);

        // user biasa cuma boleh cancel miliknya
        if (Auth::user()->role_id != 1 && $reserve->user_id != Auth::id()) {
            abort(403);
        }

        if ($reserve->status !== 'WAITING') {
            return back()->withErrors(['status' => 'Hanya waiting yang bisa dicancel.']);
        }

        $reserve->status = 'CANCELLED';
        $reserve->waktu_pinjam = null;
        $reserve->waktu_kembali = null;
        $reserve->save();

        return back()->with('success', 'Waiting list berhasil dibatalkan.');
    }


    public function destroy($id)
    {
        $book = DB::table("reserves")->where('id',decrypt($id))->get()->value('book_id');
        
        $quota = DB::table("books")->where('id',$book)->first();

        $update_quota = DB::table("books")->where('id',$book)
            ->update([
                'quota' => $quota->quota + 1,
            ]);

        $reserve = Reserve::findOrFail(decrypt($id));
        $reserve->delete();
        Session::flash('title', 'Hapus Data Berhasil!');
        Session::flash('message', '');
        Session::flash('icon', 'success');
        return redirect()->route('reserve.view');
    }

    // public function submit(Request $request){
        
    //     $validatedata = $request->validate([
    //         // 'waktu_pinjam'=>'required|date|after_or_equal:today',
    //         // 'waktu_kembali' => 'required|date|after:waktu_pinjam', 
    //         'book_id' => 'required', 
    //     ], [
    //         // 'waktu_pinjam.required'=>'Reservation Date Harus diisi',
    //         // 'waktu_kembali.required'=>'Return Date Harus diisi',
    //         // 'waktu_kembali.after' => 'Return Date harus setelah Reservation Date',
    //         // 'waktu_pinjam.after_or_equal' => 'Reservation Date sudah lewat',
    //     ]);

    //     $quota = DB::table("books")->where('id',decrypt($validatedata['book_id']))->first();

    //     $waktu_pinjam = \Carbon\Carbon::parse($validatedata['waktu_pinjam']);
    //     $waktu_kembali = \Carbon\Carbon::parse($validatedata['waktu_kembali']);

    //     if($waktu_pinjam->diffInDays($waktu_kembali) > 7){
    //         return back()->withErrors(
    //             ['waktu_kembali' => 'Return Date harus dalam rentang maksimal satu minggu setelah Reservation Date.']);
    //     };

    //     try{
    //         $reserve = Reserve::create([
    //             'user_id' => Auth::user()->id,
    //             'book_id' => decrypt($validatedata['book_id']),
    //             'status' => "Pending",
    //             'waktu_pinjam' => $validatedata['waktu_pinjam'],
    //             'waktu_kembali' => $validatedata['waktu_kembali'],
    //         ]);

    //         $update_quota = DB::table("books")->where('id',decrypt($validatedata['book_id']))
    //         ->update([
    //             'quota' => $quota->quota - 1,
    //         ]);

    //         Session::flash('title', 'Reserve Berhasil!');
    //         Session::flash('message', '');
    //         Session::flash('icon', 'success');
    //         return redirect('/book')->with('success', 'Reservation berhasil Diinput');

    //     }catch(\Exception $e) {
    //         Session::flash('title', 'Reserve Gagal!');
    //         Session::flash('message', '');
    //         Session::flash('icon', 'error');
    //         return back();
    //     }
        
    // }

    public function submit(Request $request){
    // dd('SUBMIT HIT', $request->all());

    $validatedata = $request->validate([
        'book_id'     => 'required',
        'waktu_pinjam'=> 'required|date|after_or_equal:today',
    ], [
        'waktu_pinjam.required' => 'Start Borrow Date harus diisi',
        'waktu_pinjam.after_or_equal' => 'Start Borrow Date tidak boleh kurang dari hari ini',
    ]);

    $bookId = decrypt($validatedata['book_id']);
    $userId = Auth::id();

    // CEK DOUBLE (nggak boleh dobel aktif)
    $existing = Reserve::where('user_id', $userId)
        ->where('book_id', $bookId)
        ->whereIn('status', ['WAITING', 'RESERVED', 'BORROWED'])
        ->first();

    if ($existing) {
        return back()->withErrors([
            'book_id' => 'Kamu sudah memiliki reservasi/antrean/peminjaman aktif untuk buku ini.'
        ]);
    }

    $start = Carbon::parse($validatedata['waktu_pinjam'])->startOfDay();
    $end   = (clone $start)->addWeeks(2)->toDateString();
    $startDate = $start->toDateString();
    $today = now()->toDateString();

    try {
        DB::transaction(function () use ($bookId, $userId, $startDate, $end, $today) {

            // lock row buku biar gak race condition
            $bookRow = DB::table('books')->where('id', $bookId)->lockForUpdate()->first();

            // total copy = quota available sekarang + jumlah BORROWED aktif hari ini
            $activeBorrowedToday = Reserve::where('book_id', $bookId)
                ->where('status', 'BORROWED')
                ->whereDate('waktu_pinjam', '<=', $today)
                ->whereDate('waktu_kembali', '>=', $today)
                ->count();

            $totalCopies = (int)$bookRow->quota + (int)$activeBorrowedToday;

            // hitung slot terpakai pada range tanggal yg dipilih (BORROWED + RESERVED)
            $overlapCount = Reserve::where('book_id', $bookId)
                ->whereIn('status', ['BORROWED', 'RESERVED'])
                ->whereDate('waktu_pinjam', '<=', $end)
                ->whereDate('waktu_kembali', '>=', $startDate)
                ->count();

            $slotAvailable = $overlapCount < $totalCopies;

            // ===== CASE 1: Slot tersedia =====
            if ($slotAvailable) {

                // kalau start = hari ini -> BORROWED + quota--
                if ($startDate === $today) {

                    // quota harus > 0 untuk pinjam sekarang (copy fisik tersedia sekarang)
                    if ((int)$bookRow->quota <= 0) {
                        // walau slotAvailable (karena data), tetap amanin pinjam hari ini
                        Reserve::create([
                            'user_id'        => $userId,
                            'book_id'        => $bookId,
                            'status'         => 'WAITING',
                            'waktu_pinjam'   => $startDate,
                            'waktu_kembali'  => $end,
                        ]);
                        return;
                    }

                    Reserve::create([
                        'user_id'        => $userId,
                        'book_id'        => $bookId,
                        'status'         => 'BORROWED',
                        'waktu_pinjam'   => $today,
                        'waktu_kembali'  => now()->addWeeks(2)->toDateString(),
                    ]);

                    DB::table('books')->where('id', $bookId)->update([
                        'quota' => (int)$bookRow->quota - 1,
                    ]);

                } else {
                    // start future -> RESERVED (tidak mengubah quota)
                    Reserve::create([
                        'user_id'        => $userId,
                        'book_id'        => $bookId,
                        'status'         => 'RESERVED',
                        'waktu_pinjam'   => $startDate,
                        'waktu_kembali'  => $end,
                    ]);
                }

                return;
            }

            // ===== CASE 2: Slot penuh -> WAITING =====
            Reserve::create([
                'user_id'        => $userId,
                'book_id'        => $bookId,
                'status'         => 'WAITING',
                'waktu_pinjam'   => $startDate, // simpan tanggal yg diminta
                'waktu_kembali'  => $end,
            ]);
        });

        // Flash message (di luar transaction biar aman)
        if ($startDate === now()->toDateString()) {
            Session::flash('title', 'Peminjaman Berhasil!');
            Session::flash('icon', 'success');
            return redirect('/book')->with('success', 'Buku berhasil dipinjam (2 minggu).');
        }

        // cek apakah jadi RESERVED atau WAITING (ambil latest milik user)
        $latest = Reserve::where('user_id', Auth::id())
            ->where('book_id', $bookId)
            ->orderByDesc('id')
            ->first();

        if ($latest && $latest->status === 'RESERVED') {
            Session::flash('title', 'Reservasi Berhasil!');
            Session::flash('icon', 'success');
            return redirect('/book')->with('success', 'Buku berhasil di-reserve untuk tanggal ' . $startDate . '.');
        }

        // WAITING: hitung nomor antrian (per book + tanggal start)
        $queueNumber = Reserve::where('book_id', $bookId)
            ->where('status', 'WAITING')
            ->whereDate('waktu_pinjam', $startDate)
            ->count();

        Session::flash('title', 'Masuk Waiting List');
        Session::flash('icon', 'info');
        return redirect('/book')->with(
            'info',
            'Slot penuh pada tanggal tersebut. Kamu masuk waiting list. Nomor antrian kamu: ' . $queueNumber
        );

    } catch (\Exception $e) {
        Session::flash('title', 'Reserve Gagal!');
        Session::flash('icon', 'error');
        return back();
    }
}

    public function edit($id){
        $reserve=Reserve::findOrFail(decrypt($id));
        return view('book.reservationEdit', ['reservation' => $reserve]);
    }

    
    public function update(Request $request, $id)
    {
        $validatedata = $request->validate([
            'waktu_pinjam'=>'required|date|',
            'waktu_kembali' => 'required|date|after:waktu_pinjam',
            'status'=>'required'
        ], [
            'waktu_pinjam.required'=>'Reservation Date Harus diisi',
            'waktu_kembali.required'=>'Return Date Harus diisi',
            'waktu_kembali.after' => 'Return Date harus setelah Reservation Date',
            'status.required'=>'Status Harus diisi',
        ]);

        $waktu_pinjam = \Carbon\Carbon::parse($validatedata['waktu_pinjam']);
        $waktu_kembali = \Carbon\Carbon::parse($validatedata['waktu_kembali']);

        if($waktu_pinjam->diffInDays($waktu_kembali) > 7){
            return back()->withErrors(
                ['waktu_kembali' => 'Return Date harus dalam rentang maksimal satu minggu setelah Reservation Date.']);
        };

        $reserve = Reserve::findOrFail(decrypt($id));
        $reserve->update([
            'status' => $validatedata['status'],
            'waktu_pinjam' => $validatedata['waktu_pinjam'],
            'waktu_kembali' => $validatedata['waktu_kembali']
        ]);

        Session::flash('title', 'Reservation Berhasil Diperbarui!');
        Session::flash('message', 'Perubahan Reservasi Buku telah disimpan.');
        Session::flash('icon', 'success');

        return redirect()->route('reserve.view');
    }

    private function promoteWaitingForBook(int $bookId): void{
        $today = now()->toDateString();

        $bookRow = DB::table('books')->where('id', $bookId)->lockForUpdate()->first();
        if (!$bookRow) return;

        // Kalau quota sekarang 0, nggak bisa promote jadi BORROWED hari ini.
        // Tapi bisa promote jadi RESERVED untuk tanggal future (tidak butuh quota decrement).
        $next = Reserve::where('book_id', $bookId)
            ->where('status', 'WAITING')
            ->orderBy('created_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->first();

        if (!$next) return;

        $startDate = $next->waktu_pinjam; // sudah disimpan saat waiting
        if (!$startDate) return;

        if ($startDate === $today) {
            // promote -> BORROWED butuh quota > 0
            if ((int)$bookRow->quota <= 0) return;

            $next->status = 'BORROWED';
            $next->waktu_pinjam = $today;
            $next->waktu_kembali = now()->addWeeks(2)->toDateString();
            $next->save();

            DB::table('books')->where('id', $bookId)->update([
                'quota' => (int)$bookRow->quota - 1
            ]);
            return;
        }

        // future -> RESERVED (tidak mengubah quota)
        $next->status = 'RESERVED';
        // waktu_pinjam & waktu_kembali sudah ada, tetap
        $next->save();
    }

    public function cancel($id){
        $reserveId = decrypt($id);

        DB::transaction(function () use ($reserveId) {
            $reserve = Reserve::lockForUpdate()->findOrFail($reserveId);

            if (Auth::user()->role_id != 1 && $reserve->user_id != Auth::id()) {
                abort(403);
            }

            // hanya status aktif yang boleh dicancel
            if (!in_array($reserve->status, ['WAITING', 'RESERVED', 'BORROWED'])) {
                return;
            }

            $bookId = $reserve->book_id;

            // Cancel WAITING / RESERVED: quota tidak berubah
            if (in_array($reserve->status, ['WAITING', 'RESERVED'])) {
                $reserve->status = 'CANCELLED';
                $reserve->save();
                return;
            }

            // Cancel BORROWED: efek sama dengan return
            if ($reserve->status === 'BORROWED') {
                $reserve->status = 'CANCELLED';
                $reserve->save();

                // quota++
                $bookRow = DB::table('books')->where('id', $bookId)->lockForUpdate()->first();
                DB::table('books')->where('id', $bookId)->update([
                    'quota' => (int)$bookRow->quota + 1
                ]);

                // promote FIFO waiting
                $this->promoteWaitingForBook($bookId);
            }
        });

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }

}
