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
                                $waktu_pinjam = \Carbon\Carbon::parse($reservation->waktu_pinjam)->format('d-m-Y');
                                $waktu_kembali = \Carbon\Carbon::parse($reservation->waktu_kembali)->format('d-m-Y');
                            @endphp
                            <td>{{ $waktu_pinjam }}</td>
                            <td>{{ $waktu_kembali }}</td>
                            @php
                                if ($reservation->status == "Pending") $class = 'bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                                if ($reservation->status == "In Process") $class = 'bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                                if ($reservation->status == "Completed") $class = 'bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                                if ($reservation->status == "Overdue") $class = 'bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                            @endphp
                            <td><span class="{{ $class }}">{{ $reservation->status }}</span></td>
                            <td>
                            @if (Auth::user()->role_id == 1)
                                <button type="button" onclick="window.location.href='{{ url('reservation/edit', encrypt($reservation->id)) }}'" 
                                    class="bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600 transition ease-in-out duration-200">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                    Edit
                                </button>
                            @endif

                            @if ($reservation->status == "Pending")
                                <form id="delete-form-{{ $reservation->id }}" action="{{ route('reserve.destroy', encrypt($reservation->id)) }}" method="POST" class="inline-block ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDialog('{{ $reservation->id }}')" class=" bg-rose-600 text-white py-2 px-4 rounded hover:bg-rose-700 transition ease-in-out duration-200">
                                        <i class="fa-solid fa-trash"></i>
                                          Delete
                                    </button>
                                </form>                        
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
            // Optional: You can customize DataTables options here
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
