<x-layout>
    @section('title', $portfolio->name)
    <x-slot:heading>{{ $portfolio->name }}</x-slot:heading>

    <div class="mb-6 bg-gray-600 rounded-lg p-5">
        <p class="text-sm text-gray-300">Propietario</p>
        <p class="text-lg font-semibold text-white">{{ $portfolio->user->name }} {{ $portfolio->user->surname }}</p>
    </div>

    <h2 class="text-white font-semibold text-base mb-3">Personas asociadas</h2>

    @if ($portfolio->persons->isEmpty())
        <p class="text-gray-300">Este portfolio no tiene personas asociadas.</p>
    @else
        <ul class="divide-y divide-gray-500 bg-gray-600 rounded-lg overflow-hidden">
            @foreach ($portfolio->persons as $person)
                <li class="flex items-center justify-between px-5 py-3">
                    <span class="font-medium text-white">{{ $person->name }} {{ $person->surname }}</span>
                    <span class="text-sm text-gray-300">{{ $person->email }}</span>
                    <span class="text-sm text-gray-300">{{ $person->phone }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</x-layout>
