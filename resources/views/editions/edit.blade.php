<x-layout>
    @section('title', 'Edit Edition')
    <x-slot:heading>Editar Edición</x-slot:heading>
    <div class="container px-5 py-10 mx-auto">
        <div class="max-w-3xl mx-auto bg-gray-700 p-8 rounded-xl shadow-xl">
            <form method="POST" action={{ route('editions-edit', $edition->id) }} class="space-y-6">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-2 gap-6"> 
                    <div>
                        <x-form-label for="location">Ubicación</x-form-label>
                        <x-form-input 
                            type="text" 
                            id="location" 
                            name="location" 
                            placeholder="Ubicación" 
                            value="{{$edition->location}}"
                            required />
                        <x-form-error name="location" />
                    </div>
                    <div class="mx-1">
                        <x-form-label for="date">Fecha</x-form-label>
                        <x-form-input 
                            type="date" 
                            id="date" 
                            name="date"
                            value="{{ $edition->date->format('Y-m-d') }}"
                            required />
                    </div>
                    <div class="mx-1">
                        <x-form-label for="time">Hora</x-form-label>
                        <x-form-input 
                            type="time" 
                            id="time" 
                            name="time"
                            value="{{ $edition->date->format('H:i') }}"
                            required />
                        <x-form-error name="time" />
                    </div>
                    <div>
                        <x-form-label for="duration">Duración</x-form-label>
                        <x-form-input
                            type="number"
                            id="duration"
                            name="duration"
                            value="{{$edition->duration}}"
                            required />
                        <x-form-error name="duration" />
                    </div>
                    <div>
                        <x-form-label for="capacity">Aforo</x-form-label>
                        <x-form-input
                            type="number"
                            id="capacity"
                            name="capacity"
                            value="{{$edition->capacity}}"
                            required />
                        <x-form-error name="capacity" />
                    </div>
                </div>

                    <div class="col-span-2 max-w-sm mx-auto">
                        <x-form-button>
                            Guardar Evento
                        </x-form-button>
                    </div>
                </div>
            </form>

            @admin()
            <div class="mt-8 border-t border-gray-500 pt-6">
                <h3 class="text-white font-bold text-lg mb-4">Gestores de esta edición</h3>

                {{-- Lista de gestores actuales --}}
                @if ($edition->managers->isEmpty())
                    <p class="text-gray-400 text-sm mb-6">No hay gestores asignados.</p>
                @else
                    <ul class="divide-y divide-gray-600 rounded-lg overflow-hidden mb-6 border border-gray-500">
                        @foreach ($edition->managers as $manager)
                            <li class="flex items-center justify-between bg-gray-600 px-4 py-3">
                                <span class="text-white font-medium text-sm">{{ $manager->name }} {{ $manager->surname }}</span>
                                <div class="flex items-center gap-2">
                                    @if ($manager->pivot->is_supervisor)
                                        <span class="bg-indigo-600 text-white text-xs font-semibold px-2 py-1 rounded-full">Supervisor</span>
                                    @endif
                                    @if ($manager->pivot->is_doorman)
                                        <span class="bg-emerald-600 text-white text-xs font-semibold px-2 py-1 rounded-full">Portero</span>
                                    @endif
                                    @if ($manager->pivot->invitations_capacity)
                                        <span class="bg-gray-500 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                            Cap. inv.: {{ $manager->pivot->invitations_capacity }}
                                        </span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Formulario para asignar nuevo gestor --}}
                @if ($assignableUsers->isNotEmpty())
                    <div class="bg-gray-600 rounded-xl p-6">
                        <h4 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Asignar nuevo gestor</h4>
                        <form method="POST" action="{{ route('assign-user', $edition->id) }}" class="space-y-5">
                            @csrf

                            <div>
                                <x-form-label for="user_id">Usuario</x-form-label>
                                <select name="user_id" id="user_id" required
                                    class="mt-1 block w-full px-3 py-2 bg-gray-700 text-white border border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">— Selecciona un usuario —</option>
                                    @foreach ($assignableUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                                    @endforeach
                                </select>
                                <x-form-error name="user_id" />
                            </div>

                            <div>
                                <p class="text-gray-300 text-sm font-medium mb-2">Roles</p>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer bg-gray-700 hover:bg-gray-500 text-white text-sm font-medium px-4 py-2 rounded-lg border border-gray-500 transition-colors">
                                        <input type="checkbox" name="is_supervisor" value="1" class="accent-indigo-500 w-4 h-4">
                                        Supervisor
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer bg-gray-700 hover:bg-gray-500 text-white text-sm font-medium px-4 py-2 rounded-lg border border-gray-500 transition-colors">
                                        <input type="checkbox" name="is_doorman" value="1" class="accent-emerald-500 w-4 h-4">
                                        Portero
                                    </label>
                                </div>
                            </div>

                            <div>
                                <x-form-label for="invitations_capacity">Capacidad de invitaciones <span class="text-gray-400 font-normal">(opcional)</span></x-form-label>
                                <x-form-input type="number" name="invitations_capacity" id="invitations_capacity" min="0" placeholder="Sin límite" />
                                <x-form-error name="invitations_capacity" />
                            </div>

                            <x-form-button>Asignar gestor</x-form-button>
                        </form>
                    </div>
                @else
                    <p class="text-gray-400 text-sm">Todos los usuarios ya son gestores de esta edición.</p>
                @endif
            </div>
            @endadmin

            {{--
                Not wrapped in @admin: @admin only checks is_admin, but this page
                itself is already gated to admin-or-organizer in the controller
                (see EditionController::edit()), so anyone who can reach this view
                is already allowed to manage reminders too.
            --}}
            <div class="mt-8 border-t border-gray-500 pt-6">
                <h3 class="text-white font-bold text-lg mb-4">Recordatorios de la edición</h3>

                @if ($edition->reminders->isEmpty())
                    <p class="text-gray-400 text-sm mb-6">No hay recordatorios programados.</p>
                @else
                    <ul class="divide-y divide-gray-600 rounded-lg overflow-hidden mb-6 border border-gray-500">
                        @foreach ($edition->reminders->sortBy('days_before') as $reminder)
                            <li class="flex items-center justify-between gap-2 bg-gray-600 px-4 py-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="bg-indigo-600 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                            {{ $reminder->days_before }} {{ $reminder->days_before == 1 ? 'día' : 'días' }} antes
                                        </span>
                                        @if ($reminder->isSent())
                                            <span class="bg-emerald-600 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                                Enviado el {{ $reminder->sent_at->format('d/m/Y H:i') }}
                                            </span>
                                        @elseif ($reminder->last_error)
                                            <span class="bg-red-600 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                                Error en el último intento
                                            </span>
                                        @else
                                            <span class="bg-gray-500 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                                Pendiente
                                            </span>
                                        @endif
                                    </div>
                                    @if (!$reminder->isSent() && $reminder->last_error)
                                        <p class="text-red-400 text-xs mt-1 break-words">{{ $reminder->last_error }}</p>
                                    @endif
                                </div>
                                <form method="POST"
                                      action="{{ route('edition-reminders-delete', ['edition' => $edition->id, 'reminder' => $reminder->id]) }}"
                                      onsubmit="return confirm('¿Seguro que quieres eliminar este recordatorio?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Eliminar recordatorio"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-red-400 hover:bg-gray-500 transition-colors duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="bg-gray-600 rounded-xl p-6">
                    <h4 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Programar nuevo recordatorio</h4>
                    <form method="POST" action="{{ route('edition-reminders-store', $edition->id) }}" class="space-y-5">
                        @csrf
                        <div>
                            <x-form-label for="days_before">Días de antelación</x-form-label>
                            <x-form-input
                                type="number"
                                id="days_before"
                                name="days_before"
                                min="1"
                                max="365"
                                placeholder="Por ejemplo, 7"
                                value="{{ old('days_before') }}"
                                required />
                            <x-form-error name="days_before" />
                        </div>
                        <x-form-button>Programar recordatorio</x-form-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>