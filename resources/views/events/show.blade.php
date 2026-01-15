<x-layout>
    @section('title', 'Mostrar')
    <x-slot:heading>Detalles del Evento</x-slot:heading>
    <div class="flex items-center justify-between mt-6">
        <x-button href="/events">Volver a Eventos</x-button>
        @role('admin')
            <x-button href="/events/{{ $event->id }}/edit">Editar Evento</x-button>
        @endrole
    </div>
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-gray-500 mb-4">{{ $event->name }}</h2>
            <img src="{{ asset('img/poster/Bobobo_Theatre.jpg')}}" height="100" width="100" alt="Poster del evento Bobobo Theatre">
        </div>
        <div class="space-y-4">
            <div class="flex items">
                <strong class="w-32 text-gray-700">Fecha:</strong>
                <span class="text-gray-900">{{ $event->date->format('d/m/Y') }}</span>
            </div>
            <div class="flex items">
                <strong class="w-32 text-gray-700">Localización:</strong>
                <span class="text-gray-900">{{ $event->location }}</span>
            </div>
        </div>

        <form action="/events/{{ $event->id }}/activate" method="POST" class="mt-6">
            @csrf
            <label for="is_active" class="ml-2 text-gray-700 font-semibold">Estado:</label>
            @if($event->is_active)
                <span class="inline ml-2 text-green-600 font-semibold">Activo</span>  
                @role('admin')
                <button type="submit" class="ml-4 bg-teal-800 text-white px-4 py-2 rounded hover:bg-red-600">Desactivar Evento</button>
                @endrole
            @else
                <h3 class="inline ml-2 text-red-600 font-semibold">Inactivo</h3>
                @role('admin')
                <button type="submit" class="ml-4 bg-teal-800 text-white px-4 py-2 rounded hover:bg-green-600">Activar Evento</button>
                @endrole
            @endif
        </form>
        <div>
                    <form action="/events/signup/{{ $event->id }}/" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-800">Inscribirse al Evento</button> 
                    </form>
        </div>
    </div>
</x-layout>