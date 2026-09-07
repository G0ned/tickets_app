<x-layout :show-contact="true">
    @section('title', 'Plazo de inscripción finalizado')
    <x-slot:heading>Inscripción no disponible</x-slot:heading>

    <div class="max-w-lg mx-auto mt-12">
        <div class="bg-gray-700 rounded-xl shadow-xl p-10 text-center space-y-6">

            <div class="flex items-center justify-center w-20 h-20 bg-yellow-600 rounded-full mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" class="w-10 h-10 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z" />
                </svg>
            </div>

            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-white">El plazo de inscripción ha finalizado</h2>
                <p class="text-gray-300 text-sm">
                    Ya no es posible registrarse en esta edición: el plazo de inscripción terminó el
                    {{ $deadline->format('d/m/Y') }} a las {{ $deadline->format('H:i') }}.
                </p>
                @if ($manager)
                    <p class="text-gray-300 text-sm">
                        Ponte en contacto con tu gestor/a, {{ $manager->name }} {{ $manager->surname }}
                        (<a href="mailto:{{ $manager->email }}" class="text-teal-400 hover:underline">{{ $manager->email }}</a>),
                        para más información.
                    </p>
                @else
                    <p class="text-gray-300 text-sm">
                        Ponte en contacto con
                        <a href="mailto:ec@eurocos.es" class="text-teal-400 hover:underline">ec@eurocos.es</a>
                        para más información.
                    </p>
                @endif
            </div>

            <hr class="border-gray-600">

        </div>
    </div>
</x-layout>
