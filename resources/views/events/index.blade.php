<x-layout>
    @section('title', 'Eventos')
    <x-slot:heading>Eventos Activos</x-slot:heading>
    @admin()
        <div class="mb-6">
            <x-button href="{{ route('events-create') }}">Crear Evento</x-button>
        </div>
    @endadmin
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($events as $event)
            <a href='{{ route('events-show', $event->id) }}', class="block">
                <div class="bg-gray-600 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
                    <h3 class="text-lg font-semibold text-white mb-2">{{ $event->name }}</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center text-sm text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            {{ $event->description }}
                        </div>

                        <div class="flex items-center text-sm text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            {{ $event->createdBy->name }}
                        </div>

                        <div class="flex items-center text-sm text-white">
                           @if ($event->public)
                                <span class="ml-2 text-white font-bold">Evento público</span>
                            @else
                                <span class="ml-2 text-white font-bold">Evento privado</span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</x-layout>