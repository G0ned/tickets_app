<x-layout :show-contact="true">
    @section('title', 'Cancelar inscripción')
    <x-slot:heading>Cancelar inscripción</x-slot:heading>

    <div class="max-w-lg mx-auto mt-12">
        <div class="bg-gray-700 rounded-xl shadow-xl p-10 text-center space-y-6">

            {{-- Warning icon --}}
            <div class="flex items-center justify-center w-20 h-20 bg-yellow-600 rounded-full mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" class="w-10 h-10 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>

            {{-- Heading --}}
            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-white">¿Cancelar tu inscripción?</h2>
                <p class="text-gray-300 text-sm">
                    Estás a punto de cancelar la inscripción de
                    <strong>{{ $attendee->name }} {{ $attendee->surname }}</strong> a
                    <strong>{{ $edition->event->name }}</strong>
                    ({{ $edition->date->format('d/m/Y') }} · {{ $edition->date->format('H:i') }} h).
                    Esta acción no se puede deshacer y tu entrada dejará de ser válida.
                </p>
            </div>

            <form method="POST" action="{{ route('attendee-cancel-store', $token) }}">
                @csrf
                <button type="submit"
                        class="w-full bg-red-700 hover:bg-red-600 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors duration-150">
                    Sí, cancelar mi inscripción
                </button>
            </form>

            <hr class="border-gray-600">

        </div>
    </div>
</x-layout>
