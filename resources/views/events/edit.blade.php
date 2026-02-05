<x-layout>
    @section('title', 'Edit Event')
    <x-slot:heading>Editar Evento</x-slot:heading>
    <div class="container px-5 py-10 mx-auto">
        <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg">
            <form method="POST" action="/events/{{ $event->id }}" class="space-y-6">
                @csrf
                @method("PUT")
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <x-form-label for="name">Nombre</x-form-label>
                        <x-form-input 
                            type="text" 
                            id="name" 
                            name="name" 
                            placeholder="Nombre" 
                            value="{{ $event->name }}"
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
                            value="{{ $event->location}}"
                            required />
                        <x-form-error name="location" />
                    </div>

                    <div>
                        <x-form-label for="date">Fecha del evento</x-form-label>
                        <x-form-input 
                            type="date" 
                            id="date" 
                            name="date"
                            value="{{ $event->date->format('Y-m-d') }}"
                            min="{{ now()->format('d-m-Y') }}"
                            required />
                        <x-form-error name="date" />
                    </div>

                    <div>
                        <x-form-label for="capacity">Aforo máximo</x-form-label>
                        <x-form-input
                            type="number"
                            id="capacity"
                            name="capacity"
                            value="{{ $event->capacity }}"
                            required       
                        />
                    </div>

                    <div class="col-span-2 max-w-sm mx-auto">
                        <x-form-button>
                            Editar Evento
                        </x-form-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layout>