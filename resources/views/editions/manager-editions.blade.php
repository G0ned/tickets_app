<x-layout>
 @section('title', 'Ediciones')
    <x-slot:heading>Ediciones gestionadas</x-slot:heading>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($editions as $edition)
            <a href="{{ route('edition-invitation-list', $edition->id) }}" class="block">
                <div class="bg-gray-600 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                    <div class="aspect-video w-full overflow-hidden">
                        <img src="{{ Storage::url($edition->event->poster_path) }}" alt="Póster del evento" class="w-full h-full object-cover">
                    </div>

                    <div class="p-6 space-y-3">
                        <h3 class="text-lg font-semibold text-white mb-2">{{ $edition->event->name }}</h3>

                        <div class="flex items-center gap-2 text-sm text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            {{ $edition->date->format('d-m-Y') }}
                        </div>

                        <div class="flex items-center gap-2 text-sm text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ $edition->date->format('H:i') }}
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</x-layout>