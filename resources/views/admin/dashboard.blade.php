<x-layout>
    <x-slot:heading>Eventos creados por {{ $user->firstname }}</x-slot:heading>
    <div class= 'grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3'>
        @foreach ($events as $event )
        <a href="{{ route('events.show', $event->id) }}" class="block">
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $event->name }}</h3>
                <div class="space-y-3 mt-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $event->date->format('d/m/Y') }}
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $event->location }}
                    </div>

                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{ $event->capacity }}
                    </div>

                    <div class="flex items-center text-sm text-gray-600">
                        Estado: @if ($event->is_active)
                            <span class="ml-2 text-green-600 font-semibold">Activo</span>
                        @else
                            <span class="ml-2 text-red-600 font-semibold">Inactivo</span>
                        @endif
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</x-layout>