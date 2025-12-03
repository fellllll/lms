<x-app-layout>
    <div class="section1 relative h-screen overflow-hidden">
        <img src="{{ asset('images/library.png') }}" alt="Foto Gereja" class="absolute inset-0 w-full h-full object-cover z-10">
        <div class="absolute inset-0 bg-black opacity-70 z-20"></div>
        <div class="relative py-12 z-30">
            <div class="max-w-sm md:max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-30  text-white">
            <p class="text-4xl text-center font-bold">Welcome to the library,</p>
            <p class="text-4xl text-center font-bold pt-2">Your Gateway to Endless Discovery!</p>
        </div>
    </div>

    <div class="section2 py-20 bg-gray-100">
        <div class="text-center text-gray-900">
            <p class="text-4xl font-bold">Opening Hours</p>
        </div>
        <!-- Hours -->
        <div class="mt-8 space-y-8 max-w-lg mx-auto">
            <!-- Weekdays -->
            <div class="md:flex justify-between items-center border-b border-gray-300 pb-4">
                <p class="text-center md:text-left text-2xl font-semibold">Weekdays</p>
                <div class="text-center md:text-right">
                    <p class="text-2xl font-semibold">07.30 - 21.00</p>
                    <p class="text-sm text-gray-600">Western Indonesia Time (UTC+7)</p>
                </div>
            </div>

            <!-- Weekend -->
            <div class="md:flex justify-between items-center border-b border-gray-300 pb-4">
                <p class="text-center md:text-left text-2xl font-semibold">Weekend</p>
                <div class="text-center md:text-right">
                    <p class="text-2xl font-semibold">08.00 - 17.00</p>
                    <p class="text-sm text-gray-600">Western Indonesia Time (UTC+7)</p>
                </div>
            </div>

            <!-- National Holiday -->
            <div class="md:flex justify-between items-center">
                <p class="text-center md:text-left text-2xl font-semibold">National Holiday</p>
                <div class="text-center md:text-right">
                    <p class="text-2xl font-semibold">CLOSED</p>
                </div>
            </div>
        </div>
    </div>

    <div class="section3 py-20 bg-black">
        <div class="text-center text-white mb-8">
            <p class="text-4xl font-bold">Contact Us</p>
        </div>
        
        <!-- Contact Details -->
        <div class="flex flex-col md:flex-row justify-center items-center mt-8 max-w-4xl mx-auto md:space-x-28 space-y-8 md:space-y-0 text-white">
            
            <!-- Email -->
            <div class="flex flex-col md:flex-row justify-center items-center space-y-4 w-full md:w-auto">
                <!-- Email Icon from Font Awesome -->
                <i class="fas fa-envelope fa-2x m-4 md:fa-3x w-8 h-8 text-white"></i>
                <div class="text-center md:text-left">
                    <p class="text-2xl font-semibold">Email Us</p>
                    <p class="text-xl">contact@library.com</p>
                </div>
            </div>

            <!-- Phone -->
            <div class="flex flex-col md:flex-row justify-center items-center space-y-4 w-full md:w-auto">
                <!-- Phone Icon from Font Awesome -->
                <i class="fas fa-phone-alt fa-2x m-4 md:fa-3x w-8 h-8 text-white"></i>
                <div class="text-center md:text-left">
                    <p class="text-2xl font-semibold">Call Us</p>
                    <p class="text-xl">+123-456-7890</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
