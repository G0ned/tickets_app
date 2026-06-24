<x-layout>
    @section('title', 'Panel de administración')
    <x-slot:heading>Panel de administración</x-slot:heading>
    <h1 class="mb-6">
        Welcome to the dashboard {{ $user->name }}
    </h1>

    <x-button href="{{ route('portfolios-index', $user->id) }}">Mis Portfolios</x-button>
</x-layout>