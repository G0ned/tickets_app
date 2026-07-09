<x-layout>
    @section('title', 'Editar usuario')
    <x-slot:heading>Editar usuario</x-slot:heading>

    <div class="max-w-xl mx-auto">
        <div class="bg-gray-700 rounded-xl shadow-xl p-8">
            <form method="POST" action="{{ route('user-update', $user->id) }}" class="space-y-5">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <x-form-label for="name">Nombre</x-form-label>
                        <x-form-input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ $user->name }}"
                            placeholder="Nombre"
                            required />
                        <x-form-error name="name" />
                    </div>

                    <div>
                        <x-form-label for="surname">Apellidos</x-form-label>
                        <x-form-input
                            type="text"
                            id="surname"
                            name="surname"
                            value="{{ $user->surname }}"
                            placeholder="Apellidos"
                            required />
                        <x-form-error name="surname" />
                    </div>
                </div>

                <div>
                    <x-form-label for="email">Correo electrónico</x-form-label>
                    <x-form-input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ $user->email }}"
                        placeholder="correo@ejemplo.com"
                        required />
                    <x-form-error name="email" />
                </div>

                <div class="border-t border-gray-600 pt-5 space-y-3">
                    <p class="text-sm font-medium text-gray-300">Roles</p>

                    <label class="flex items-center gap-3 cursor-pointer select-none
                                  bg-gray-600 hover:bg-gray-500 px-4 py-3 rounded-lg
                                  border border-gray-500 transition-colors duration-150">
                        <input type="checkbox"
                               name="is_admin"
                               value="1"
                               {{ $user->is_admin ? 'checked' : '' }}
                               class="w-4 h-4 accent-indigo-500">
                        <div>
                            <p class="text-white text-sm font-semibold">Administrador</p>
                            <p class="text-gray-400 text-xs">Acceso completo: crear eventos, gestionar usuarios y ediciones.</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer select-none
                                  bg-gray-600 hover:bg-gray-500 px-4 py-3 rounded-lg
                                  border border-gray-500 transition-colors duration-150">
                        <input type="checkbox"
                               name="is_supervisor"
                               value="1"
                               {{ $user->is_supervisor ? 'checked' : '' }}
                               class="w-4 h-4 accent-teal-500">
                        <div>
                            <p class="text-white text-sm font-semibold">Supervisor</p>
                            <p class="text-gray-400 text-xs">Puede supervisar ediciones y gestionar listas de invitados.</p>
                        </div>
                    </label>
                </div>

                <div class="pt-2">
                    <x-form-button>Guardar cambios</x-form-button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
