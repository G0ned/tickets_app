<x-layout>
    <x-slot:heading>Perfil</x-slot:heading>
    <h2>{{ $user->firstname }}</h2>
    <div class= 'grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3'>
        @foreach ($events as $event )
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
            </div>
        </div>
        @endforeach
    </div>
</x-layout>