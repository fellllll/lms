<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold">{{$book->title}}</h1>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <div class="flex-shrink-0 flex justify-center items-center p-8">
                    <img src="{{ asset($book->image) }}" alt="Book Cover" class="w-48 h-72 rounded shadow-md object-cover">
                </div>
                <div class="p-6">
                    <h2 class="text-xl font-semibold">Details</h2>
                    <p class="text-gray-700 mt-2"><strong>Author:</strong> {{$book->author}}</p>
                    <p class="text-gray-700"><strong>Publisher:</strong> {{$book->publisher}}</p>
                    <p class="text-gray-700"><strong>Year:</strong> {{ $book->year}}</p>
                    <p class="text-gray-700"><strong>Pages:</strong> {{ $book->pages}}</p>
                    <p class="text-gray-700"><strong>Genre:</strong> {{ $book->genre->name ?? 'N/A' }}</p>
                    <p class="text-gray-700"><strong>Quota:</strong> {{ $book->quota}}</p>
                    <h3 class="text-lg font-semibold mt-4">Description</h3>
                    <p class="text-gray-600">{{ $book->description }}</p>
                    <h3 class="text-lg font-semibold mt-4">Summary</h3>
                    <p class="text-gray-600">{{ $book->summary }}</p>
                </div>
            </div>
            <hr class="border-1 border-gray-200">
            <div class="p-4 bg-gray-50 text-right">
                <button type="button" onclick="window.location.href='{{ url('/reserve/' . encrypt($book->id)) }}'" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition ease-in-out duration-200">
                    Reserve this book
                </button>
            </div>
        </div>
    </div>
    
    
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