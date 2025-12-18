<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Reserve;
use App\Models\BookPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    //
    public function list()
    {
        $books = DB::table('books');
        $books = $books->get();
        
        return view('book.list', compact('books'));
    }

    // public function show(){
    //     $books = DB::table('books');
    //     if(request('search')) {
    //         $books->where('title','like','%' . request('search') . '%')
    //         ->orWhere('author','like','%' . request('search') . '%');
    //     }

    //     $books = $books->get();
        
    //     return view('book.index', compact('books'));
    // }

    
    public function show(){
        $booksQuery = DB::table('books');

        if (request('search')) {
            $booksQuery->where('title', 'like', '%' . request('search') . '%')
                ->orWhere('author', 'like', '%' . request('search') . '%');
        }

        $books = $booksQuery->get();
        // Buat hitung data reserve dr user yg login
        $userId = Auth::id();
        $bookIds = $books->pluck('id')->all();

        // Reserve aktif user per book (WAITING / BORROWED)
        $userActiveReserves = Reserve::where('user_id', $userId)
            ->whereIn('book_id', $bookIds)
            ->whereIn('status', ['WAITING', 'BORROWED'])
            ->get()
            ->keyBy('book_id');

        // Semua waiting untuk buku-buku yang sedang ditampilkan (buat hitung nomor antrian)
        $waitingRows = Reserve::whereIn('book_id', $bookIds)
            ->where('status', 'WAITING')
            ->orderBy('book_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'book_id', 'user_id', 'created_at']);

        $queuePosByBookUser = []; // [book_id][user_id] = posisi antrian
        $waitingCountByBook = []; // [book_id] = total waiting
        $counter = [];

        foreach ($waitingRows as $row) {
            $bid = $row->book_id;
            $counter[$bid] = ($counter[$bid] ?? 0) + 1;

            $queuePosByBookUser[$bid][$row->user_id] = $counter[$bid];
            $waitingCountByBook[$bid] = $counter[$bid];
        }

        return view('book.index', compact('books'))
            ->with('userActiveReserves', $userActiveReserves)
            ->with('queuePosByBookUser', $queuePosByBookUser)
            ->with('waitingCountByBook', $waitingCountByBook);
    }


    // public function detail($id, Book $books){
    //     $book = $books->where('id', decrypt($id))->firstOrFail();
    //     return view('book.detail', compact('book'));
    // }

    public function detail($id, Book $books)
{
    $bookId = decrypt($id);
    $book = $books->where('id', $bookId)->firstOrFail();

    $userId = \Auth::id();

    // reserve aktif user utk buku ini (kalau ada)
    $myReserve = Reserve::where('user_id', $userId)
        ->where('book_id', $bookId)
        ->whereIn('status', ['WAITING', 'BORROWED'])
        ->first();

    // hitung nomor antrian kalau dia waiting
    $myQueue = null;
    if ($myReserve && $myReserve->status === 'WAITING') {
        $myQueue = Reserve::where('book_id', $bookId)
            ->where('status', 'WAITING')
            ->where(function ($q) use ($myReserve) {
                $q->where('created_at', '<', $myReserve->created_at)
                  ->orWhere(function ($q2) use ($myReserve) {
                      $q2->where('created_at', '=', $myReserve->created_at)
                         ->where('id', '<=', $myReserve->id);
                  });
            })
            ->count();
    }

    return view('book.detail', compact('book', 'myReserve', 'myQueue'));
}


    public function destroy($id)
    {
        $book = Book::findOrFail(decrypt($id));
        $book->delete();
        Session::flash('title', 'Hapus Buku Berhasil!');
        Session::flash('message', '');
        Session::flash('icon', 'success');
        return redirect()->back();
    }

    public function edit(Book $book){
        $genres = Genre::all();
        return view('book.form', ['book' => $book, 'genres' => $genres]);
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'pages' => 'required|integer|min:1',
            'quota' => 'required|integer|min:1',
            'description' => 'required|string',
            'summary' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'title.string' => 'Judul buku harus berupa teks.',
            'title.max' => 'Judul buku maksimal 255 karakter.',
            
            'genre_id.required' => 'Genre buku wajib dipilih.',
            'genre_id.exists' => 'Genre yang dipilih tidak valid.',
            
            'year.required' => 'Tahun penerbitan wajib diisi.',
            'year.digits' => 'Tahun penerbitan harus terdiri dari 4 digit.',
            
            'author.required' => 'Penulis buku wajib diisi.',
            'author.string' => 'Penulis buku harus berupa teks.',
            'author.max' => 'Nama penulis maksimal 255 karakter.',
            
            'publisher.required' => 'Penerbit buku wajib diisi.',
            'publisher.string' => 'Penerbit buku harus berupa teks.',
            'publisher.max' => 'Nama penerbit maksimal 255 karakter.',
            
            'pages.required' => 'Jumlah halaman wajib diisi.',
            'pages.integer' => 'Jumlah halaman harus berupa angka.',
            'pages.min' => 'Jumlah halaman minimal 1.',
            
            'quota.required' => 'Kuota buku wajib diisi.',
            'quota.integer' => 'Kuota buku harus berupa angka.',
            'quota.min' => 'Kuota buku minimal 1.',
            
            'description.required' => 'Deskripsi buku wajib diisi.',
            'description.string' => 'Deskripsi buku harus berupa teks.',
            
            'summary.required' => 'Ringkasan buku wajib diisi.',
            'summary.string' => 'Ringkasan buku harus berupa teks.',
            
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $imagePath = $book->image;

        if ($request->hasFile('image')) {
            if ($book->image && file_exists(public_path($book->image))) {
                unlink(public_path($book->image));
            }

            $originalFileName = $request->image->getClientOriginalName();
            $safeFileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalFileName);

            $imagePath = 'images/books/' . $safeFileName;

            $request->image->move(public_path('images/books'), $imagePath);
        }

        $book->update([
            'title' => $request->title,
            'genre_id' => $request->genre_id,
            'year' => $request->year,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'pages' => $request->pages,
            'quota' => $request->quota,
            'description' => $request->description,
            'summary' => $request->summary,
            'image' => $imagePath,
        ]);

        Session::flash('title', 'Buku Berhasil Diperbarui!');
        Session::flash('message', 'Perubahan buku telah disimpan.');
        Session::flash('icon', 'success');

        return redirect()->route('book.list');
    }
    public function add(){
        $genres=Genre::all();
        return view('book.add',['genres' => $genres]);
    }

    public function submit(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'pages' => 'required|integer|min:1',
            'quota' => 'required|integer|min:1',
            'description' => 'required|string',
            'summary' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'pdf' => 'nullable|file|mimes:pdf|max:10240',
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'title.string' => 'Judul buku harus berupa teks.',
            'title.max' => 'Judul buku maksimal 255 karakter.',
            
            'genre_id.required' => 'Genre buku wajib dipilih.',
            'genre_id.exists' => 'Genre yang dipilih tidak valid.',
            
            'year.required' => 'Tahun penerbitan wajib diisi.',
            'year.digits' => 'Tahun penerbitan harus terdiri dari 4 digit.',
            
            'author.required' => 'Penulis buku wajib diisi.',
            'author.string' => 'Penulis buku harus berupa teks.',
            'author.max' => 'Nama penulis maksimal 255 karakter.',
            
            'publisher.required' => 'Penerbit buku wajib diisi.',
            'publisher.string' => 'Penerbit buku harus berupa teks.',
            'publisher.max' => 'Nama penerbit maksimal 255 karakter.',
            
            'pages.required' => 'Jumlah halaman wajib diisi.',
            'pages.integer' => 'Jumlah halaman harus berupa angka.',
            'pages.min' => 'Jumlah halaman minimal 1.',
            
            'quota.required' => 'Kuota buku wajib diisi.',
            'quota.integer' => 'Kuota buku harus berupa angka.',
            'quota.min' => 'Kuota buku minimal 1.',
            
            'description.required' => 'Deskripsi buku wajib diisi.',
            'description.string' => 'Deskripsi buku harus berupa teks.',
            
            'summary.required' => 'Ringkasan buku wajib diisi.',
            'summary.string' => 'Ringkasan buku harus berupa teks.',
            
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            
            'pdf.mimes' => 'File harus berformat PDF.',
            'pdf.max' => 'Ukuran file PDF maksimal 10MB.',
        ]);


        if ($request->hasFile('image')) {

            $originalFileName = $request->image->getClientOriginalName();
            $safeFileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalFileName);

            $imagePath = 'images/books/' . $safeFileName;

            $request->image->move(public_path('images/books'), $imagePath);
        }

        try{
            $book = Book::create([
                'title' => $request->title,
                'genre_id' => $request->genre_id,
                'year' => $request->year,
                'author' => $request->author,
                'publisher' => $request->publisher,
                'pages' => $request->pages,
                'quota' => $request->quota,
                'description' => $request->description,
                'summary' => $request->summary,
                'image' => $imagePath,
            ]);

            if ($request->hasFile('pdf')) {
                $path = $request->file('pdf')->store('books/pdf', 'public');
                BookPdf::create([
                    'book_id' => $book->id,
                    'pdf' => $path
                ]);
            }

            Session::flash('title', 'Book Berhasil Diinput!');
            Session::flash('message', '');
            Session::flash('icon', 'success');
            return redirect('/book')->with('success', 'Book berhasil Diinput');

        }catch(\Exception $e) {
            Session::flash('title', 'Book Gagal Diinput!');
            Session::flash('message', '');
            Session::flash('icon', 'error');
            return back()->withErrors($request)->withInput();
        }
        
    }

    public function uploadPdf(Request $request, Book $book)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240'
        ], [
            'pdf.required' => 'File PDF wajib diupload.',
            'pdf.mimes' => 'File harus berformat PDF.',
            'pdf.max' => 'Ukuran file maksimal 10MB.',
        ]);

        if ($request->hasFile('pdf')) {
            $file = $request->file('pdf');
            $path = $file->store('books/pdf', 'public');
            
            BookPdf::create([
                'book_id' => $book->id,
                'pdf' => $path
            ]);

            Session::flash('title', 'PDF Berhasil Diupload!');
            Session::flash('message', 'File PDF telah disimpan.');
            Session::flash('icon', 'success');
        }

        return redirect()->back();
    }

    public function viewPdf(BookPdf $bookPdf)
    {
        // Cek apakah user punya akses (misal, cek reservasi)
        $user = auth()->user();
        $hasAccess = $bookPdf->book->reserves()->where('user_id', $user->id)->where('status', 'BORROWED')->exists();
        
        if (!$hasAccess && $user->role_id != 1) { // Admin bisa akses semua
            abort(403, 'Unauthorized');
        }

        $path = Storage::disk('public')->path($bookPdf->pdf);
        
        if (!Storage::disk('public')->exists($bookPdf->pdf)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    public function pdfViewer(BookPdf $bookPdf)
    {
        // Cek akses sama seperti viewPdf
        $user = auth()->user();
        $hasAccess = $bookPdf->book->reserves()->where('user_id', $user->id)->where('status', 'BORROWED')->exists();
        
        if (!$hasAccess && $user->role_id != 1) {
            abort(403, 'Unauthorized');
        }

        // Kirim URL PDF ke view
        $pdfUrl = route('books.viewPdf', $bookPdf->id);
        return view('book.pdfViewer', compact('pdfUrl'));
    }
}
