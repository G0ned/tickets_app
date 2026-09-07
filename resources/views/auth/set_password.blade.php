<x-layout>
    @section('title', 'Configurar contraseña')
    <div>
        <div class="max-w-sm mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-700 rounded-lg shadow-sm p-6 sm:p-8">
                <div class="mb-8">
                    <x-slot:heading>Configura tu contraseña</x-slot:heading>
                    <p class="text-gray-300 text-sm mt-2">{{ $email }}</p>
                </div>

                <form method="POST" action="{{ route('set-password-store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="grid grid-cols-1 gap-6">
                        <x-form-label for="password">Contraseña</x-form-label>
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
                        <x-form-label for="password_confirmation">Confirmar contraseña</x-form-label>
                        <x-form-input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Repite la contraseña"
                            required />
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <x-form-error name="email"/>
                        <x-form-button>
                            Guardar contraseña
                        </x-form-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
