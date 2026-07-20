<x-layout>
    @section('title', 'Edit event')
    <x-slot:heading>Crear Evento</x-slot:heading>
    <div class="container px-4 sm:px-5 py-6 sm:py-10 mx-auto">
        <div class="max-w-3xl mx-auto bg-gray-700 p-4 sm:p-8 rounded-xl shadow-xl">
            <form method="POST" action={{ route('events-update', $event->id) }} enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')
                <div class="flex flex-col gap-6">
                    <div class="w-full sm:w-48 sm:shrink-0 mx-auto sm:mx-0">
                        <img src="{{ Storage::url($event->poster_path) }}" class="w-full h-48 object-cover rounded-lg shadow-md">
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1 min-w-0">
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
                        <div class="flex-1 min-w-0">
                            <x-form-label for="poster">Póster</x-form-label>
                            <x-form-input
                                type="file"
                                id="poster_path"
                                name="poster_path"
                                accept="image/*"
                                class="w-full bg-teal-800 text-white border border-gray-300 rounded-lg px-4 py-2
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:bg-gray-700 file:text-white
                                hover:file:bg-blue-600
                                cursor-pointer" />
                        </div>
                        <div class="sm:w-32 sm:shrink-0">
                            <x-form-label for="date">Público</x-form-label>
                            <select id="public" name="public" value="{{$event->public}}"
                                    class="
                                        block rounded-lg w-full border-gray-900
                                        shadow-lg focus:border-blue-500 focus:ring-blue-500
                                        sm:text-sm required">
                                <option selected value=0> No </option>
                                <option value=1> Sí </option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <x-form-label for="desc">Descripción</x-form-label>
                        <textarea
                        name="desc"
                        id="desc"
                        rows="4"
                        class="w-full brounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $event->description }}</textarea>
                        <x-form-error name="desc" />
                    </div>

                    <div class="w-full max-w-sm mx-auto my-2">
                        <x-form-button>
                            Guardar Evento
                        </x-form-button>
                    </div>
                </div>
            </form>

            <div class="mt-6">
                <x-form-label>Organizadores</x-form-label>
                <ul class="divide-y divide-gray-600 rounded-lg overflow-hidden mb-6 border border-gray-500">
                    @foreach($users as $user)
                    @if($event->organizers->contains($user))
                        <li class="flex items-center justify-between flex-wrap gap-2 bg-gray-600 px-4 py-3">
                            <span class="text-white font-medium text-sm break-words">{{ $user->name }} {{ $user->surname }}</span>
                            <div class="flex items-center gap-2">
                                    <span class="bg-indigo-600 text-white text-xs font-semibold px-2 py-1 rounded-full">Organizador</span>
                            </div>
                        </li>
                    @endif
                    @endforeach
                </ul>
            </div>
            @admin()
            <div class="bg-gray-600 rounded-xl p-6">
                <h4 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Asignar nuevo gestor</h4>
                <form method="POST" action="{{ route('assign-organizer', $event->id) }}" class="space-y-5">
                    @csrf
                    <div class="mb-2">
                        <x-form-label for="user_id">Usuario</x-form-label>
                        <select name="user_id" id="user_id" required
                            class="mt-1 block w-full px-3 py-2 bg-gray-700 text-white border border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">— Selecciona un usuario —</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                            @endforeach
                        </select>
                        <x-form-error name="user_id" />
                    </div>
                    <div class="col-span-2 max-w-sm mx-auto">
                        <x-form-button>Asignar organizador</x-form-button>
                    </div>
                </form>
            </div>
            @endadmin

            <div class="mt-8 border-t border-gray-500 pt-6">
                <x-form-label>Porteros</x-form-label>
                <p class="text-gray-400 text-sm mb-3">Los porteros solo tienen acceso al escáner de entradas, no a la gestión del evento.</p>
                @if ($event->doormen->isEmpty())
                    <p class="text-gray-400 text-sm mb-6">No hay porteros asignados.</p>
                @else
                    <ul class="divide-y divide-gray-600 rounded-lg overflow-hidden mb-6 border border-gray-500">
                        @foreach ($event->doormen as $doorman)
                            <li class="flex items-center justify-between flex-wrap gap-2 bg-gray-600 px-4 py-3">
                                <span class="text-white font-medium text-sm break-words">{{ $doorman->name }} {{ $doorman->surname }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="bg-emerald-600 text-white text-xs font-semibold px-2 py-1 rounded-full">Portero</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($canManageDoormen)
                    <div class="bg-gray-600 rounded-xl p-6">
                        <h4 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Asignar nuevo portero</h4>
                        <form method="POST" action="{{ route('assign-doorman', $event->id) }}" class="space-y-5">
                            @csrf
                            <div class="mb-2">
                                <x-form-label for="doorman_user_id">Usuario</x-form-label>
                                <select name="user_id" id="doorman_user_id" required
                                    class="mt-1 block w-full px-3 py-2 bg-gray-700 text-white border border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">— Selecciona un usuario —</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                                    @endforeach
                                </select>
                                <x-form-error name="user_id" />
                            </div>
                            <div class="col-span-2 max-w-sm mx-auto">
                                <x-form-button>Asignar portero</x-form-button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>