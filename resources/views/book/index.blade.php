<x-app-layout>
    @include('components.swallalert')
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center">Library Book Collections</h1>
    </x-slot>

    
    <form class="max-w-md mx-auto mt-8" action="" method="GET">   
        <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
            </div>
            <input type="search" id="search" name="search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search" required />
            <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
        </div>
    </form>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-6">
                @foreach ($books as $book)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden relative">
                        <a href="{{route('book.detail', encrypt($book->id))}}"  > 
                            <div class="flex items-center justify-center py-4 bg-gray-50">
                                <img src="{{ asset($book->image) }}" alt="Book Cover" class="w-33 h-48 rounded shadow-md object-cover">
                            </div>
                        </a>
                        <hr class="w-full border-gray-300">
                        <div class="p-4 pb-16">
                            <h2 class="text-xl font-semibold">{{$book->title}}</h2>
                            <p class="text-gray-700">Author: {{$book->author}}</p>
                            <p class="text-gray-600 mt-2">{{$book->summary}}</p>
                            <div class="absolute bottom-4 right-4 flex items-center space-x-4">
                                <!-- Book Quota -->
                                <span class="text-white bg-yellow-500 text-sm px-3 py-1 rounded-full">
                                    Quota: {{$book->quota}}
                                </span>
                                <!-- Reserve Button -->
                                @if ($book->quota == 0)
                                    <button disabled
                                        class="bg-red-600 text-white py-2 px-4 rounded">
                                        Not Available
                                    </button>
                                @elseif($book->quota > 0)
                                    <button onclick="window.location='{{ url( '/reserve/'.encrypt($book->id)) }}'" 
                                        class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition ease-in-out duration-200">
                                        Reserve
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        </div>
    </div>
</x-app-layout>
