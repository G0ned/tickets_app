<x-layout>
    @section('title', 'Mostrar')
    <x-slot:heading>Detalles del Evento</x-slot:heading>
    <x-button href="/events">Volver a Eventos</x-button>
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-gray-500 mb-4">{{ $event->name }}</h2>
            <img src="{{ asset('img/poster/Bobobo_Theatre.jpg')}}" height="100" width="100" alt="Poster del evento Bobobo Theatre">
        </div>
        <div class="space-y-4">
            <div class="flex items">
                <strong class="w-20 text-gray-700">Fecha:</strong>
                <span class="text-gray-900">{{ $event->date->format('d/m/Y') }}</span>
                <strong class="w-20 text-gray-700 ml-5">Hora:</strong>
                <span class="text-gray-900">{{ $event->date->format('H:i') }}</span>
            </div>
            <div class="flex items">
                <strong class="w-32 text-gray-700">Localización:</strong>
                <span class="text-gray-900">{{ $event->location }}</span>
                <strong class="w-32 text-gray-700 ml-10">Aforo restante:</strong>
                <span class="text-gray-900">{{ $event->capacity}}</span>
                @role('admin')
                <strong class="w-32 text-gray-700 ml-10">Asistentes:</strong>
                <span class="text-gray-900">{{ $event->number_of_attendees }}</span>
                @endrole
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
            @elseif(!$event->is_active)
                <h3 class="inline ml-2 text-red-600 font-semibold">Inactivo</h3>
                @role('admin')
                <button type="submit" class="ml-4 bg-teal-800 text-white px-4 py-2 rounded hover:bg-green-600">Activar Evento</button>
                @endrole
            @endif
        </form>

        @role('attendee')
        <div>
                @if($event->isSignedUp(auth()->user()->identification))
                    <form action="/events/signup/cancel/{{ $event->id }}/" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-800">Cancelar Inscripción</button> 
                    </form>
                @elseif($event->capacity <= 0)
                    <span>{{$event->isSignedUp(auth()->user()->identification)}}</span>
                    <span class="text-gray-500 mt-6 block">El evento no admite más inscripciones. Lamentamos las molestias.</span>
                @else
                    <form action="/events/signup/{{ $event->id }}/" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-800">Inscribirse al Evento</button> 
                    </form>
                @endif
        </div>
        @endrole
        <div class="flex items-center justify-between mt-6">
        @role('admin')
            @if($event->user_id === auth()->user()->identification)
                <x-button href="/events/{{ $event->id }}/edit">Editar Evento</x-button>
                <form action="/events" method="POST" class="mt-6">
                    @csrf
                    @method("DELETE")
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-800">Eliminar Evento</button>
                </form>
            @else
                <span class="text-gray-500">No tienes permisos para editar o eliminar este evento.</span>
            @endif
        
        </div>
    </div>
    <hr class="my-10 w-full">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4 text-center">Lista de Asistentes</h2>
        <table class="overflow-y-auto table-auto w-full mt-10 border-collapse border border-gray-300">
            <tr>
                <th class="border px-4 py-2">ID Asistente</th>
                <th class="border px-4 py-2">Nombre</th>
                <th class="border px-4 py-2">Apellido</th>
            </tr>
            @foreach ($event->assistants as $attendee)
                <tr>
                    <td class="border px-4 py-2">{{ $attendee->user_id }}</td>
                    <td class="border px-4 py-2">{{ $attendee->user->firstname }}</td>
                    <td class="border px-4 py-2">{{ $attendee->user->surname }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    @endrole
</x-layout>