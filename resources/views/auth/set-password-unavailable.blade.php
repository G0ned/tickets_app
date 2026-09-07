<x-layout>
    @section('title', 'Enlace no disponible')
    <x-slot:heading>Configurar contraseña</x-slot:heading>

    <div class="max-w-lg mx-auto mt-12">
        <div class="bg-gray-700 rounded-xl shadow-xl p-10 text-center space-y-6">

            <div class="flex items-center justify-center w-20 h-20 bg-yellow-600 rounded-full mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" class="w-10 h-10 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
            </div>

            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-white">Enlace no disponible</h2>
                <p class="text-gray-300 text-sm">
                    Este enlace para configurar tu contraseña no es válido, ya se ha utilizado, o ha caducado.
                    Pide a un administrador que te vuelva a crear la cuenta.
                </p>
            </div>

            <hr class="border-gray-600">

        </div>
    </div>
</x-layout>
