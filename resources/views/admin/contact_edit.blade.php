<x-layout>
    @section('title', 'Editar contacto')
    <x-slot:heading>Editar contacto</x-slot:heading>

    <div class="max-w-xl mx-auto space-y-6">
        <div>
            <x-button href="{{ route('contacts-index') }}">← Volver</x-button>
        </div>

        <div class="bg-gray-700 rounded-xl shadow-xl p-8">
            <form method="POST" action="{{ route('contacts-update', $person->id) }}" class="space-y-5">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <x-form-label for="name">Nombre</x-form-label>
                        <x-form-input type="text" id="name" name="name"
                                      value="{{ old('name', $person->name) }}" placeholder="Nombre" required />
                        <x-form-error name="name" />
                    </div>
                    <div>
                        <x-form-label for="surname">Apellidos</x-form-label>
                        <x-form-input type="text" id="surname" name="surname"
                                      value="{{ old('surname', $person->surname) }}" placeholder="Apellidos" required />
                        <x-form-error name="surname" />
                    </div>
                </div>

                <div>
                    <x-form-label for="email">Correo electrónico</x-form-label>
                    <x-form-input type="email" id="email" name="email"
                                  value="{{ old('email', $person->email) }}" placeholder="correo@ejemplo.com" required />
                    <x-form-error name="email" />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <x-form-label for="phone">Teléfono</x-form-label>
                        <x-form-input type="text" id="phone" name="phone"
                                      value="{{ old('phone', $person->phone) }}" placeholder="Teléfono" required />
                        <x-form-error name="phone" />
                    </div>
                    <div>
                        <x-form-label for="zip_code">Código postal</x-form-label>
                        <x-form-input type="text" id="zip_code" name="zip_code" inputmode="numeric" maxlength="5"
                                      value="{{ old('zip_code', $person->zip_code) }}" placeholder="00000" />
                        <x-form-error name="zip_code" />
                    </div>
                </div>

                <div class="pt-2">
                    <x-form-button>Guardar cambios</x-form-button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
