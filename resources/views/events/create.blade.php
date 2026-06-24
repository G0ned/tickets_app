<x-layout>
    @section('title', 'Create Event')
    <x-slot:heading>Crear Evento</x-slot:heading>
    <div class="container px-5 py-10 mx-auto">
        <div class="max-w-3xl mx-auto bg-gray-700 p-8 rounded-xl shadow-xl">
            <form method="POST" action={{ route('events-store') }} enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="flex flex-col gap-6">
                    <div class="flex items-center">
                        <div>
                            <x-form-label for="name">Nombre</x-form-label>
                            <x-form-input 
                                type="text" 
                                id="name" 
                                name="name" 
                                placeholder="Nombre" 
                                value="{{ old('name') }}"
                                required />
                            <x-form-error name="name" />
                        </div>
                        <div class="mx-4">
                            <x-form-label for="poster">Póster</x-form-label>
                            <x-form-input 
                                type="file" 
                                id="poster" 
                                name="poster"
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
                            <select id="public" name="public" value={{old('public')}} 
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
                        class="w-full brounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </textarea>
                        <x-form-error name="desc" />
                    </div>

                    <div>
                        <x-form-label></x-form-label>
                    </div>

                    <div class="col-span-2 max-w-sm mx-auto">
                        <x-form-button>
                            Guardar Evento
                        </x-form-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layout>