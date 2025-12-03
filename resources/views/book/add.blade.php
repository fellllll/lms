<x-app-layout>
    @include('components.swallalert')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <section>
                <form action="{{ route('book.submit') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded-md shadow-md">
                    @csrf
                    @method('post')

                    <!-- Title Input -->
                    <label for="title" class="block mb-1">Title:</label>
                    <input type="text" name="title" id="title" required 
                        class="w-full p-2 border border-gray-300 rounded mb-4">
                    <x-input-error :messages="$errors->get('title')" class="mb-2" />

                    <!-- Image Upload -->
                    <label for="image" class="block mb-1">Upload Image:</label>
                    <input type="file" name="image" id="image" class="w-full p-2 border border-gray-300 rounded mb-4" accept="image/*">

                    <x-input-error :messages="$errors->get('image')" class="mb-2" />

                    <!-- Genre Selection -->
                    <label for="genre_id" class="block mb-1">Genre:</label>
                    <select name="genre_id" id="genre_id" class="w-full p-2 border border-gray-300 rounded mb-4">
                        <option value="" disabled selected>Select a genre</option>
                        @foreach ($genres as $genre)
                            <option value="{{ $genre->id }}">
                                {{ $genre->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('genre_id')" class="mb-2" />


                    <!-- Year Input -->
                    <label for="year" class="block mb-1">Year:</label>
                    <input type="text" name="year" id="year" required 
                        pattern="\d{4}" title="Please enter a valid year (e.g., 2024)" 
                        class="w-full p-2 border border-gray-300 rounded mb-4" placeholder="YYYY">
                    <x-input-error :messages="$errors->get('year')" class="mb-2" />

                    <!-- Author Input -->
                    <label for="author" class="block mb-1">Author:</label>
                    <input type="text" name="author" id="author"required 
                        class="w-full p-2 border border-gray-300 rounded mb-4" placeholder="Author's name">
                    <x-input-error :messages="$errors->get('author')" class="mb-2" />

                    <!-- Publisher Input -->
                    <label for="publisher" class="block mb-1">Publisher:</label>
                    <input type="text" name="publisher" id="publisher" required 
                        class="w-full p-2 border border-gray-300 rounded mb-4" placeholder="Publisher's name">
                    <x-input-error :messages="$errors->get('publisher')" class="mb-2" />

                    <!-- Pages Input -->
                    <label for="pages" class="block mb-1">Pages:</label>
                    <input type="number" name="pages" id="pages" required 
                        class="w-full p-2 border border-gray-300 rounded mb-4" placeholder="Number of pages">
                    <x-input-error :messages="$errors->get('pages')" class="mb-2" />

                    <!-- Quota Input -->
                    <label for="quota" class="block mb-1">Quota:</label>
                    <input type="number" name="quota" id="quota" required 
                        class="w-full p-2 border border-gray-300 rounded mb-4" placeholder="Number of available quota">
                    <x-input-error :messages="$errors->get('quota')" class="mb-2" />

                    <!-- Description Input -->
                    <label for="description" class="block mb-1">Description:</label>
                    <textarea name="description" id="description" rows="4" required 
                        class="w-full p-2 border border-gray-300 rounded mb-4 h-32" placeholder="Enter a description..."></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mb-2"/>

                    <!-- Summary Input -->
                    <label for="summary" class="block mb-1">Summary:</label>
                    <textarea name="summary" id="summary" rows="4" required 
                        class="w-full p-2 border border-gray-300 rounded mb-4 h-32" placeholder="Enter a summary..."></textarea>
                    <x-input-error :messages="$errors->get('summary')" class="mb-2" />

                    <!-- Submit Buttons -->
                    <div class="flex justify-center space-x-4 mt-4">
                        <!-- Tombol Save -->
                        <button type="submit" class="bg-blue-600 text-white px-4 py-3 rounded hover:bg-blue-700 text-base">
                            Save
                        </button>

                        <!-- Tombol Cancel -->
                        <a href="{{ route('book.list') }}" class="bg-red-600 text-white px-4 py-3 rounded hover:bg-red-700 text-base">
                            Cancel
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>