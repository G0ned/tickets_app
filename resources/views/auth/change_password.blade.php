<x-layout>
    @section('title', 'Cambiar contraseña')
    <x-slot:heading>Cambiar contraseña</x-slot:heading>
    <div>
        <div class="max-w-sm mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-700 rounded-lg shadow-sm p-6 sm:p-8">
                <form method="POST" action="{{ route('account-password-update') }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-6">
                        <x-form-label for="current_password">Contraseña actual</x-form-label>
                        <x-form-input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            required />
                        <x-form-error name="current_password" />
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <x-form-label for="password">Nueva contraseña</x-form-label>
                        <x-form-input
                            type="password"
                            id="password"
                            name="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Mínimo 8 caracteres"
                            required />
                        <x-form-error name="password" />
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <x-form-label for="password_confirmation">Confirmar nueva contraseña</x-form-label>
                        <x-form-input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Repite la contraseña"
                            required />
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <x-form-button>
                            Guardar contraseña
                        </x-form-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
