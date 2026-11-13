<x-layout>
    @section('title', 'Reactivar edición')
    <x-slot:heading>Reactivar edición</x-slot:heading>
    <div class="container px-5 py-10 mx-auto">
        <div class="max-w-3xl mx-auto mb-4">
            <x-button href="{{ route('events-show', $edition->event_id) }}">← Volver al evento</x-button>
        </div>
        <div class="max-w-3xl mx-auto bg-gray-700 p-8 rounded-xl shadow-xl">
            <div class="mb-6">
                <p class="text-white font-semibold text-base">{{ $edition->event->name ?? '(evento eliminado)' }}</p>
                <p class="text-gray-400 text-xs mt-2">
                    La edición se reactivará al pulsar «Reactivar edición». Puedes cambiar la fecha y la hora
                    o conservar las originales, que ya están cargadas. Al reactivar se archivan las inscripciones previas.
                </p>
            </div>

            <form method="POST" action="{{ route('editions-restore', $edition->id) }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <x-form-label for="location">Ubicación</x-form-label>
                        <x-form-input type="text" id="location" name="location" placeholder="Ubicación"
                            value="{{ old('location', $edition->location) }}" required />
                        <x-form-error name="location" />
                    </div>
                    <div>
                        <x-form-label for="duration">Duración</x-form-label>
                        <x-form-input type="number" id="duration" name="duration" step="any"
                            value="{{ old('duration', $edition->duration) }}" required />
                        <x-form-error name="duration" />
                    </div>
                    <div class="mx-1">
                        <x-form-label for="date">Fecha</x-form-label>
                        <x-form-input type="date" id="date" name="date"
                            value="{{ old('date', $edition->date->format('Y-m-d')) }}" required />
                        <x-form-error name="date" />
                    </div>
                    <div class="mx-1">
                        <x-form-label for="time">Hora</x-form-label>
                        <x-form-input type="time" id="time" name="time"
                            value="{{ old('time', $edition->date->format('H:i')) }}" required />
                        <x-form-error name="time" />
                    </div>
                </div>

                <div class="border-t border-gray-500 pt-6">
                    <x-form-label>Plazo límite de inscripción (opcional)</x-form-label>
                    <p class="text-gray-400 text-xs mb-3">
                        Si cambias la fecha, revisa también este plazo. Déjalo en blanco para no limitar el plazo.
                    </p>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="mx-1">
                            <x-form-label for="registration_deadline_date">Fecha límite</x-form-label>
                            <x-form-input type="date" id="registration_deadline_date" name="registration_deadline_date"
                                value="{{ old('registration_deadline_date', $edition->registration_deadline?->format('Y-m-d')) }}" />
                            <x-form-error name="registration_deadline_date" />
                        </div>
                        <div class="mx-1">
                            <x-form-label for="registration_deadline_time">Hora límite</x-form-label>
                            <x-form-input type="time" id="registration_deadline_time" name="registration_deadline_time"
                                value="{{ old('registration_deadline_time', $edition->registration_deadline?->format('H:i')) }}" />
                            <x-form-error name="registration_deadline_time" />
                        </div>
                    </div>
                </div>

                <div class="max-w-sm mx-auto">
                    <x-form-button>Reactivar edición</x-form-button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
