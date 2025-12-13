<x-app-layout>
    @include('components.swallalert')
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-center">Library Book Reservation System</h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <section>
                <h2 class="text-xl font-semibold mb-2">Reserve a Book</h2>

                <form id="reservation-form" action="{{ route('reserve.submit') }}" method="POST" class="bg-white p-4 rounded-md shadow-md">
                @csrf
                    <label for="book-title" class="block mb-1">Book Title:</label>
                    <input value="{{ $book }}" type="text" name="book-title" readonly class="w-full p-2 border border-gray-300 rounded mb-4">

                    <input value="{{ $id }}" type="hidden" name="book_id">

                    <label for="user-name" class="block mb-1">Your Name:</label>
                    <input value="{{ $name }}" type="text" name="user-name" readonly class="w-full p-2 border border-gray-300 rounded mb-4">

                    <label for="waktu_pinjam" class="block mb-1">Reservation Date:</label>
                    <input type="date" id="waktu_pinjam" name="waktu_pinjam" required class="w-full p-2 border border-gray-300 rounded mb-4">

                    <x-input-error :messages="$errors->get('waktu_pinjam')" class="mb-2" />
                    
                    <label for="waktu_kembali" class="block mb-1">Return Date:</label>
                    <input type="date" id="waktu_kembali" name="waktu_kembali" readonly required class="w-full p-2 border border-gray-300 rounded mb-4 bg-gray-100">

                    <x-input-error :messages="$errors->get('waktu_kembali')" class="mb-2" />
                    
                    <div class="flex justify-center space-x-4 mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-3 rounded hover:bg-blue-700 text-base">Book</button>
                        <button type="submit" onclick="history.back();" class="bg-red-600 text-white px-4 py-3 rounded hover:bg-red-700 text-base">Cancel</button>
                    </div>
                </form>

            </section>
        </div>
    </div>

    <script>
        document.getElementById('waktu_pinjam').addEventListener('change', function () {
            const pinjam = new Date(this.value);
            pinjam.setDate(pinjam.getDate() + 14);

            const yyyy = pinjam.getFullYear();
            const mm = String(pinjam.getMonth() + 1).padStart(2, '0');
            const dd = String(pinjam.getDate()).padStart(2, '0');

            document.getElementById('waktu_kembali').value = `${yyyy}-${mm}-${dd}`;
        });
    </script>
</x-app-layout>
