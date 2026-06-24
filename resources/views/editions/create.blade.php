<x-layout>
    @section('title', 'Create Edition')
    <x-slot:heading>Crear Edición</x-slot:heading>
    <div class="container px-5 py-10 mx-auto">
        <div class="max-w-3xl mx-auto bg-gray-700 p-8 rounded-xl shadow-xl">
            <form method="POST" action={{ route('editions-store', ['event' => $event]) }} class="space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-6"> 
                    <div>
                        <x-form-label for="location">Ubicación</x-form-label>
                        <x-form-input 
                            type="text" 
                            id="location" 
                            name="location" 
                            placeholder="Ubicación" 
                            value="{{ old('location') }}"
                            required />
                        <x-form-error name="location" />
                    </div>
                    <div class="mx-1">
                        <x-form-label for="date">Fecha</x-form-label>
                        <x-form-input 
                            type="date" 
                            id="date" 
                            name="date"
                            value="{{old('date')}}"
                            required />
                    </div>
                    <div class="mx-1">
                        <x-form-label for="time">Hora</x-form-label>
                        <x-form-input 
                            type="time" 
                            id="time" 
                            name="time"
                            value="{{ old('time') }}"
                            required />
                        <x-form-error name="time" />
                    </div>
                    <div>
                        <x-form-label for="duration">Duración</x-form-label>
                        <x-form-input
                            type="number"
                            id="duration"
                            name="duration"
                            value="{{old('duration')}}"
                            required />
                        <x-form-error name="duration" />
                    </div>
                    <div>
                        <x-form-label for="capacity">Aforo</x-form-label>
                        <x-form-input
                            type="number"
                            id="capacity"
                            name="capacity"
                            value="{{old('capacity')}}"
                            required />
                        <x-form-error name="capacity" />
                    </div>
                </div>

                    <div class="col-span-2 max-w-sm mx-auto">
                        <x-form-button>
                            Guardar Evento
                        </x-form-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layout>