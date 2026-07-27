<x-layout :show-contact="true">
    @section('title', 'Registro por invitación')
    <x-slot:title>Registro por invitación</x-slot:title>
    <div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8">
                <div class="mb-8">
                    <x-slot:heading>Registro por invitación</x-slot:heading>
                </div>

                <diV class="max-w-2xl mx-auto px-2">
                    <div class="bg-gray-700 p-8 rounded-xl shadow-xl">
                    <div class="flex gap-8">
                    <div class="w-48 shrink-0">
                        @if($event->poster_path)
                            <img
                                src="{{ Storage::url($event->poster_path) }}"
                                alt="Poster del evento {{ $event->name }}"
                                class="w-full h-48 object-cover rounded-lg text-white shadow-md"
                            >
                        @else
                            <div class="w-full h-48 bg-gray-600 rounded-lg flex items-center justify-center">
                                <span class="text-gray-400 text-sm">Sin cartel</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 space-y-4">
                        <h2 class="text-2xl font-bold text-white">{{ $event->name }}</h2>

                        <div>
                            <span class="text-gray-400 text-sm uppercase tracking-wide">Edición</span>
                            <p class="text-white mt-1">
                                {{ $edition->date->format('d/m/Y') }}
                                · {{ $edition->date->format('H:i') }} h
                                · {{ $edition->location }}
                            </p>
                        </div>
                        <hr class="bg-white my-6">
                    </div>
            </div>
        </div>
                <form method="POST" action="{{ route('invitation-registration-store', $token) }}" class="space-y-8 rounded-sm shadow-lg">
                    @csrf
                    <div class="px-2 sm:px-4">
                        <label for="verification_code" class="text-white">Código de verificación</label>
                        <x-form-input
                            type="text"
                            id="verification_code"
                            name="verification_code"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Código recibido por email"
                            value="{{ old('verification_code') }}"
                            required/>
                    </div>
                    @include('form._attendee-fields', ['submitLabel' => 'Completar inscripción'])
                </form>
            </div>
        </div>
    </div>
</x-layout>
