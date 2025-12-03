<x-app-layout>
    @include('components.swallalert')
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center">Library Book List</h1>
    </x-slot>

    <div class="pb-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="container mx-auto p-6">
            <div class="mb-4 w-5/5 max-w-full mx-auto flex justify-start">
                <button type="submit" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-700">
                    <a href="/books/add">
                        <i class="fa-solid fa-plus"></i>
                    ADD BOOK</a>
                </button> 
            </div>

            <table  id="reservation_table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Book Title</th>
                        <th scope="col">Author</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($books as $book)
                        <tr>
                            <td>{{ $book->id }}</td>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->author }}</td>
                            <td>
                                @can('update-book')
                                    <button type="button" onclick="window.location.href='{{ url('book/edit', $book->id) }}'" 
                                        class="bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600 transition ease-in-out duration-200">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                        Edit
                                        </button>
                                @endcan
                                
                                @can('delete-book')
                                    <form id="delete-form-{{ $book->id }}" action="{{ route('book.destroy', encrypt($book->id)) }}" method="POST" class="inline-block ml-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDialog('{{ $book->id }}')" 
                                        class="bg-rose-600 text-white py-2 px-4 rounded hover:bg-rose-700 transition ease-in-out duration-200">
                                        <i class="fa-solid fa-trash"></i>
                                        Delete
                                        </button>
                                    </form>
                                @endcan
                                
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
    function confirmDialog($id) {
        event.preventDefault();
        
        Swal.fire({
            title: `Apakah Kamu Yakin akan Menghapus Buku Ini ?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: `Ya, Hapus`,
            confirmButtonColor: "#2463eb",
            cancelButtonText: "Batal",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + $id).submit();
            }
        });
    }
</script>

</x-app-layout>