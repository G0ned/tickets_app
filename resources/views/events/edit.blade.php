<x-layout>
    @section('title', 'Edit event')
    <x-slot:heading>Crear Evento</x-slot:heading>
    <div class="container px-5 py-10 mx-auto">
        <div class="max-w-3xl mx-auto bg-gray-700 p-8 rounded-xl shadow-xl">
            <form method="POST" action={{ route('events-update', $event->id) }} enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')
                <div class="flex flex-col gap-6">
                    <div class="w-48 shrink-0 items-center">
                        <img src="{{ Storage::url($event->poster_path) }}" class="w-full h-48 object-cover rounded-lg shadow-md itens-center justify-between">
                    </div>
                    <div class="flex items-center">
                        <div class="mx-2">
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
                        <div class="mx-4">
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
                        <div class="mx-4">
                            <x-form-label for="date">Público</x-form-label>
                            <select id="public" name="public" value="{{$event->public}}" 
                                    class="
                                        block rounded-lg w-auto border-gray-900 
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

                    <div class="col-span-2 max-w-sm mx-auto my-2">
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
                        <li class="flex items-center justify-between bg-gray-600 px-4 py-3">
                            <span class="text-white font-medium text-sm">{{ $user->name }} {{ $user->surname }}</span>
                            <div class="flex items-center gap-2">
                                    <span class="bg-indigo-600 text-white text-xs font-semibold px-2 py-1 rounded-full">Organizador</span>
                                    @admin()
                                        <form method="POST" action="{{ route('remove-organizer', [$event->id, $user->id]) }}"
                                            onsubmit="return confirm('¿Quitar a {{ $user->name }} {{ $user->surname }} como organizador de este evento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Quitar organizador"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-red-400 hover:bg-gray-500 transition-colors duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endadmin
                            </div>
                        </li>
                    @endif
                    @endforeach
                </ul>
            </div>
            @admin()
            <div class="bg-gray-600 rounded-xl p-6">
                <h4 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Asignar nuevo organizador</h4>
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
                <p class="text-gray-400 text-sm mb-3">Los porteros solo pueden acceder al escáner de entradas de este evento.</p>
                @if ($event->doormen->isEmpty())
                    <p class="text-gray-400 text-sm mb-6">No hay porteros asignados.</p>
                @else
                    <ul class="divide-y divide-gray-600 rounded-lg overflow-hidden mb-6 border border-gray-500">
                        @foreach ($event->doormen as $doorman)
                            <li class="flex items-center justify-between bg-gray-600 px-4 py-3">
                                <span class="text-white font-medium text-sm">{{ $doorman->name }} {{ $doorman->surname }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="bg-emerald-600 text-white text-xs font-semibold px-2 py-1 rounded-full">Portero</span>
                                    @admin()
                                        <form method="POST" action="{{ route('remove-doorman', [$event->id, $doorman->id]) }}"
                                            onsubmit="return confirm('¿Quitar a {{ $doorman->name }} {{ $doorman->surname }} como portero de este evento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Quitar portero"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-red-400 hover:bg-gray-500 transition-colors duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endadmin
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