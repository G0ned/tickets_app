<x-layout>
    <x-slot:heading>Bienvenido a Eventia</x-slot:heading>
    <div>
        <div class="max-w-sm mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-700 rounded-lg shadow-sm p-6 sm:p-8">
                <div class="mb-8">
                    <x-slot:heading>Iniciar Sesión</x-slot:heading>
                </div>

                <form method="POST" action={{ route('login') }} class="space-y-8">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">
                        <x-form-label for="email">Email</x-form-label>
                        <x-form-input
                            type="email"
                            id="email"
                            name="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Email"
                            required/>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <x-form-label for="password">Contraseña</x-form-label>
                        <x-form-input
                            type="password"
                            id="password"
                            name="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="********"
                            requierd/>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <x-form-error name="email"/>
                        <x-form-button>
                            Iniciar Sesión 
                        </x-form-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>


