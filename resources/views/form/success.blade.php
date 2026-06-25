<x-layout>
    @section('title', 'Registro completado')
    <x-slot:heading>Registro de Asistentes</x-slot:heading>

    <div class="max-w-lg mx-auto mt-12">
        <div class="bg-gray-700 rounded-xl shadow-xl p-10 text-center space-y-6">

            {{-- Success icon --}}
            <div class="flex items-center justify-center w-20 h-20 bg-teal-700 rounded-full mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" class="w-10 h-10 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>

            {{-- Heading --}}
            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-white">¡Registro completado!</h2>
                <p class="text-gray-300 text-sm">
                    Tu inscripción ha sido procesada correctamente. Recibirás un correo electrónico
                    de confirmación con los detalles de tu asistencia.
                </p>
            </div>

            {{-- Divider --}}
            <hr class="border-gray-600">

            {{-- Info note --}}
            <p class="text-gray-400 text-xs">
                Si no recibes el correo en los próximos minutos, revisa tu carpeta de spam.
            </p>

            {{-- Back button --}}
            <div>
                <a href="{{ route('home') }}"
                   class="inline-block bg-teal-700 hover:bg-teal-600 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors duration-150">
                    Volver al inicio
                </a>
            </div>

        </div>
    </div>
</x-layout>
