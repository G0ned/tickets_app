<x-layout>
    @section('title', 'Supervisión de invitaciones')
    <x-slot:heading>Supervisión de invitaciones</x-slot:heading>

    @if ($editions->isEmpty())
        <p class="text-gray-400 text-sm">No supervisas ninguna edición actualmente.</p>
    @endif

    @foreach ($editions as $item)
        @php($edition = $item['edition'])
        <div class="mb-10 bg-gray-700 rounded-xl shadow-xl p-6">
            <h3 class="text-white font-bold text-lg mb-1">{{ $edition->event->name }}</h3>
            <p class="text-gray-400 text-sm mb-4">{{ $edition->date->format('d-m-Y H:i') }} &middot; {{ $edition->location }}</p>

            @if ($item['managers']->isEmpty())
                <p class="text-gray-400 text-sm">Esta edición no tiene gestores asignados.</p>
            @else
                <div class="space-y-4">
                    @foreach ($item['managers'] as $entry)
                        <div class="bg-gray-600 rounded-lg p-4">
                            <div class="flex items-center justify-between flex-wrap gap-3">
                                <div>
                                    <p class="text-white font-medium">{{ $entry['manager']->name }} {{ $entry['manager']->surname }}</p>
                                    <p class="text-gray-300 text-xs">{{ $entry['committed'] }} invitaciones comprometidas</p>
                                </div>

                                <form method="POST" action="{{ route('supervised-manager-capacity-update', [$edition->id, $entry['manager']->id]) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label class="text-gray-300 text-xs" for="capacity-{{ $edition->id }}-{{ $entry['manager']->id }}">Cap. inv.</label>
                                    <input type="number" min="{{ $entry['committed'] }}" name="invitations_capacity"
                                           id="capacity-{{ $edition->id }}-{{ $entry['manager']->id }}"
                                           value="{{ $entry['capacity'] }}"
                                           placeholder="Sin límite"
                                           {{ $entry['locked'] ? 'disabled' : '' }}
                                           class="w-24 px-2 py-1 bg-gray-700 text-white border border-gray-500 rounded-md text-sm disabled:opacity-50" />
                                    @unless ($entry['locked'])
                                        <button type="submit" class="text-indigo-400 hover:text-indigo-300 text-xs font-semibold">Guardar</button>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Bloqueado (todas enviadas)</span>
                                    @endunless
                                </form>
                            </div>

                            @error('invitations_capacity', "capacity-{$edition->id}-{$entry['manager']->id}")
                                <p class="text-red-400 text-xs mt-2 text-right">{{ $message }}</p>
                            @enderror

                            @if ($entry['lists']->isNotEmpty())
                                <ul class="mt-3 divide-y divide-gray-500 border-t border-gray-500 pt-2">
                                    @foreach ($entry['lists'] as $list)
                                        <li class="flex items-center justify-between py-2 text-sm">
                                            <a href="{{ route('invitation-list-show', $list->id) }}" class="text-white hover:text-indigo-300">
                                                {{ $list->name }}
                                            </a>
                                            <span class="text-xs {{ $list->isSent() ? 'text-emerald-400' : 'text-gray-400' }}">
                                                {{ $list->isSent() ? 'Enviada' : 'Borrador' }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-3 text-gray-400 text-xs">Sin listas de invitaciones todavía.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</x-layout>
