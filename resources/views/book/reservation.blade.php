

<x-app-layout>
    @include('components.swallalert')

    <x-slot name="header">
        <h1 class="text-2xl font-bold">Reserve Book</h1>
    </x-slot>

    <div class="max-w-xl mx-auto py-12">
        <div class="bg-white rounded-lg shadow-md p-6">

            <h2 class="text-lg font-semibold mb-4">
                {{ $book }}
            </h2>

            <form action="{{ route('reserve.submit') }}" method="POST">
                @csrf

                <input type="hidden" name="book_id" value="{{ $id }}">

                {{-- Start Date --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">
                        Start Borrow Date
                    </label>
                    <input
                        type="date"
                        name="waktu_pinjam"
                        value="{{ old('waktu_pinjam') }}"
                        min="{{ date('Y-m-d') }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                    @error('waktu_pinjam')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info --}}
                <div class="mb-4 text-sm text-gray-600 space-y-1">
                    <p>📅 Duration: <strong>2 weeks</strong> (automatically calculated)</p>
                    <p>⏳ If the slot is full on the selected date, you will be placed on the waiting list.</p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end gap-3 mt-6">
                    <button
                        type="button"
                        onclick="history.back()"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Confirm Reservation
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
