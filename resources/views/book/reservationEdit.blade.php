<x-app-layout>
    @include('components.swallalert')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <section>
                <form action="{{ route('reservation.update', encrypt($reservation->id)) }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded-md shadow-md">
                    @csrf
                    @method('put')

                    <input type="hidden" value="{{ $reservation->id }}">
                    
                    <!-- Title -->
                    <label for="title" class="block mb-1">Book Title:</label>
                    <input value="{{ old('title', $reservation->book->title) }}" type="text" name="title" id="title" readonly 
                        class="w-full p-2 border border-gray-300 rounded mb-4">

                    <!-- User Name -->
                    <label for="title" class="block mb-1">User Name:</label>
                    <input value="{{ old('name', $reservation->user->name) }}" type="text" name="name" id="name" readonly 
                        class="w-full p-2 border border-gray-300 rounded mb-4">
                    
                    <!-- Status Input -->
                    <label for="status" class="block mb-1">Status:</label>
                    
                    <select name="status" id="status" class="w-full p-2 border border-gray-300 rounded mb-4">    
                        <option value="{{ old('status', $reservation->status)}}">
                            {{ old('status', $reservation->status) }}
                        </option>       
                        @php
                        $status = ['Pending', 'In Process', 'Completed', 'Overdue'];
                        @endphp     

                        @foreach($status as $s)
                            @if ($s != $reservation->status)
                                <option value="{{$s}}">{{$s}}</option>
                            @endif
                        @endforeach

                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mb-2" />
                    
                    <!-- Reservation Date Input -->
                    <label for="waktu_pinjam" class="block mb-1">Reservation Date:</label>
                    <input type="date" name="waktu_pinjam" value="{{ old('waktu_pinjam', $reservation->waktu_pinjam) }}" required class="w-full p-2 border border-gray-300 rounded mb-4">

                    <x-input-error :messages="$errors->get('waktu_pinjam')" class="mb-2" />
                    
                    <!-- Return Date Input -->
                    <label for="waktu_kembali" class="block mb-1">Return Date:</label>
                    <input type="date" name="waktu_kembali" value="{{ old('waktu_kembali', $reservation->waktu_kembali) }}" required class="w-full p-2 border border-gray-300 rounded mb-4">

                    <x-input-error :messages="$errors->get('waktu_kembali')" class="mb-2" />
                    
                    <!-- Submit Buttons -->
                    <div class="flex justify-center space-x-4 mt-4">
                        <!-- Tombol Save -->
                        <button type="submit" class="bg-blue-600 text-white px-4 py-3 rounded hover:bg-blue-700 text-base">
                            Save
                        </button>

                        <!-- Tombol Cancel -->
                        <a href="{{ route('reserve.view') }}" class="bg-red-600 text-white px-4 py-3 rounded hover:bg-red-700 text-base">
                            Cancel
                        </a>
                    </div>
                </form>

            </section>
        </div>
    </div>
</x-app-layout>