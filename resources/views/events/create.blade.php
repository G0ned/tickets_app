<x-layout>
    @section('title', 'Create Event')
    <x-slot:heading>Crear Evento</x-slot:heading>
    <div class="container px-5 py-10 mx-auto">
        <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg">
            <form method="POST" action="/events" class="space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <x-form-label for="name">Nombre</x-form-label>
                        <x-form-input 
                            type="text" 
                            id="name" 
                            name="name" 
                            placeholder="Nombre" 
                            value="{{ old('name') }}"
                            required />
                        <x-form-error name="name" />
                    </div>

                    <div>
                        <x-form-label for="location">Localización</x-form-label>
                        <x-form-input
                            type="text" 
                            id="location" 
                            name="location"
                            placeholder="Sta. Cruz de Tenerife"
                            value="{{ old('location') }}"
                            required />
                        <x-form-error name="location" />
                    </div>

                    <div>
                        <x-form-label for="date">Fecha del evento</x-form-label>
                        <x-form-input 
                            type="date" 
                            id="date" 
                            name="date"
                            value="{{ old('date') }}"
                            min="{{ now()->format('d-m-Y') }}"
                            required />
                        <x-form-error name="date" />
                    </div>

                     <div>
                        <x-form-label for="time">Hora del evento</x-form-label>
                        <x-form-input 
                            type="time" 
                            id="time" 
                            name="time"
                            value="{{ old('time') }}"
                            required />
                        <x-form-error name="time" />
                    </div>

                    <div>
                        <x-form-label for="capacity">Aforo máximo</x-form-label>
                        <x-form-input
                            type="number"
                            id="capacity"
                            name="capacity"
                            value="{{ old('capacity') }}"
                            required       
                        />
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