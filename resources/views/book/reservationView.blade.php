<x-app-layout>
    @include('components.swallalert')
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center">Library Book Reservation System</h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="container mx-auto p-6">
        
        @if($reserves->isEmpty())
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                <p class="font-bold">No Reservations Found</p>
                <p>There are currently no book reservations available.</p>
            </div>
        @else
            <table  id="reservation_table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>

                        @if(Auth::user()->role_id==1)
                        <th scope="col">Name</th>
                        @endif 

                        <th scope="col">Book Title</th>
                        <th scope="col">Reservation Date</th>
                        <th scope="col">Return Date</th>
                        <th scope="col">Status</th>
                        <th scope="col" style="width:25%">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reserves as $reservation)
                        <tr>
                            <td>{{ $reservation->id }}</td>

                            @if(Auth::user()->role_id==1)
                            <td>{{ $reservation->user->name }}</td>
                            @endif 
                            
                            <td>{{ $reservation->book->title }}</td>

                            @php
                                $waktu_pinjam = $reservation->waktu_pinjam
                                    ? \Carbon\Carbon::parse($reservation->waktu_pinjam)->format('d-m-Y')
                                    : '-';

                                $waktu_kembali = $reservation->waktu_kembali
                                    ? \Carbon\Carbon::parse($reservation->waktu_kembali)->format('d-m-Y')
                                    : '-';
                            @endphp
                            <td>{{ $waktu_pinjam }}</td>
                            <td>{{ $waktu_kembali }}</td>
          
<!-- 
// WAITING = antri (tanggal boleh NULL, quota tidak berubah)  -- yellow
// BORROWED = sedang pinjam (tanggal wajib ada, quota sudah berkurang) -- blue
// RETURNED = sudah balik (quota sudah balik) -- green
// CANCELLED = dibatalkan (buat riwayat) -- red -->

                            @php
                                $class = 'bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded';

                                if ($reservation->status === 'WAITING') {
                                    $class = 'bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded';
                                } elseif ($reservation->status === 'BORROWED') {
                                    $class = 'bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded';
                                } elseif ($reservation->status === 'RETURNED') {
                                    $class = 'bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded';
                                } elseif ($reservation->status === 'CANCELLED') {
                                    $class = 'bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded';
                                } elseif ($reservation->status === 'RESERVED') {
                                    $class = 'bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded';
                                }
                                // if ($reservation->status == "Pending") $class = 'bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                                // if ($reservation->status == "In Process") $class = 'bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                                // if ($reservation->status == "Completed") $class = 'bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                                // if ($reservation->status == "Overdue") $class = 'bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                            @endphp

                            @php
                                $statusText = $reservation->status;
                                if ($reservation->status === 'WAITING') {
                                    $statusText = 'WAITING #' . ($queueByReserveId[$reservation->id] ?? '-');
                                }
                            @endphp

                            <td><span class="{{ $class }}">{{ $statusText }}</span></td>
                        
                            <td class="space-x-2">
                                @if (Auth::user()->role_id == 1)
                                    {{-- ADMIN --}}
                                    <button type="button" onclick="window.location.href='{{ url('reservation/edit', encrypt($reservation->id)) }}'"
                                        class="bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600 transition ease-in-out duration-200">
                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                    </button>
                                @else
                                    {{-- USER --}}
                                    
                                    {{-- BORROWED → RETURN --}}
                                    @if ($reservation->status === 'BORROWED')
                                        <form action="{{ route('reserve.return', encrypt($reservation->id)) }}"
                                            method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                                Return
                                            </button>
                                        </form>
                                    @endif

                                    {{-- === READ PDF BUTTON === --}}
                                    @if (
                                        $reservation->status === 'BORROWED' &&
                                        $reservation->book &&
                                        $reservation->book->pdfs && 
                                        $reservation->book->pdfs->isNotEmpty()
                                    )
                                        <a href="{{ route('books.loadBookmark', $reservation->book->pdfs->first()) }}" target="_blank"
                                        class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                            📖 Read PDF
                                        </a>
                                    @endif

                                    {{-- WAITING → CANCEL --}}
                                    @if ($reservation->status === 'WAITING')
                                        <form action="{{ route('reserve.cancel', encrypt($reservation->id)) }}"
                                            method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                                Cancel Waiting
                                            </button>
                                        </form>
                                    @endif

                                    {{-- RESERVED → CANCEL --}}
                                    @if ($reservation->status === 'RESERVED')
                                        <form action="{{ route('reserve.cancel', encrypt($reservation->id)) }}"
                                            method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#reservation_table').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            scrollX: true,
            autoWidth: false
        });
    });
    </script>

    
<script>
    function confirmDialog(reserve_id) {
        event.preventDefault();
        
        Swal.fire({
            title: `Apakah Kamu Yakin akan Menghapus Data Ini ?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: `Ya, Hapus`,
            confirmButtonColor: "#2463eb",
            cancelButtonText: "Batal",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + reserve_id).submit();
            }
        });
    }
</script>

</x-app-layout>
