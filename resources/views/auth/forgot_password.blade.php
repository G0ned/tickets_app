<x-layout>
    @section('title', '¿Olvidaste tu contraseña?')
    <div>
        <div class="max-w-sm mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-700 rounded-lg shadow-sm p-6 sm:p-8">
                <div class="mb-8">
                    <x-slot:heading>¿Olvidaste tu contraseña?</x-slot:heading>
                    <p class="text-gray-300 text-sm mt-2">
                        Escribe el email de tu cuenta y te enviaremos un enlace para restablecer tu contraseña.
                    </p>
                </div>

                <form method="POST" action="{{ route('forgot-password-store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">
                        <x-form-label for="email">Email</x-form-label>
                        <x-form-input
                            type="email"
                            id="email"
                            name="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Email"
                            value="{{ old('email') }}"
                            required />
                        <x-form-error name="email" />
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <x-form-button>
                            Enviar enlace
                        </x-form-button>
                    </div>
                </form>

                <p class="text-center text-sm text-gray-400 mt-6">
                    <a href="{{ route('home') }}" class="text-teal-400 hover:text-teal-300">Volver al inicio de sesión</a>
                </p>
            </div>
        </div>
    </div>
</x-layout>
