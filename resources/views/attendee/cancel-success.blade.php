<x-layout :show-contact="true">
    @section('title', 'Inscripción cancelada')
    <x-slot:heading>Inscripción cancelada</x-slot:heading>

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
                <h2 class="text-2xl font-bold text-white">Tu inscripción ha sido cancelada</h2>
                <p class="text-gray-300 text-sm">
                    Se ha cancelado tu inscripción a <strong>{{ $edition->event->name }}</strong>.
                    Tu entrada ya no es válida. Puedes cerrar esta página.
                </p>
            </div>

            <hr class="border-gray-600">

        </div>
    </div>
</x-layout>
