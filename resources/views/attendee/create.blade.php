<x-layout>
    <x-slot:title>Registro de Asistentes</x-slot:title>
    <div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8">
                <div class="mb-8">
                    <x-slot:heading>Registro de Asistentes</x-slot:heading>
                </div>

                <form method="POST" action="/attendees" class="space-y-8">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        <div>
                            <x-f-assistant-label for="id_type">Tipo de Identificación</x-f-assistant-label>
                            <select
                                id="id_type"
                                name="id_type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                required
                            >
                                <option value="" disabled selected>Seleccione tipo</option>
                                <option value="NIF" {{ old('id_type') == 'NIF' ? 'selected' : '' }}>NIF</option>
                                <option value="NIE" {{ old('id_type') == 'NIE' ? 'selected' : '' }}>NIE</option>
                            </select>
                            <x-form-error name="id_type" />
                        </div>

                        <div>
                            <x-f-assistant-label for="identification">Número de Identificación</x-f-assistant-label>
                            <x-form-input
                                type="text"
                                id="identification"
                                name="identification"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="00000000X"
                                required/>
                            <x-form-error name="id" />
                        </div>

                        <div>
                            <x-f-assistant-label for="firstname">Nombre</x-f-assistant-label>
                            <x-form-input
                                type="text"
                                id="firstname"
                                name="firstname"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Nombre"
                                value="{{ old('firstname') }}"
                                required/>
                            <x-form-error name="firstname" />
                        </div>

                        <div>
                            <x-f-assistant-label for="surname">Apellido/s</x-f-assistant-label>
                            <x-form-input
                                type="text"
                                id="surname"
                                name="surname"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Apellido/s"
                                value="{{ old('surname') }}"
                                required/>
                            <x-form-error name="surname" />
                        </div>

                        <div>
                            <x-f-assistant-label for="email">E-mail</x-f-assistant-label>
                            <x-form-input
                                type="email"
                                id="email"
                                name="email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="correo@gmail.com"
                                value="{{ old('email') }}"
                                required/>
                            <x-form-error name="email" />
                        </div>
                        
                        <div class="col-span-2">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                 <!-- <div>
                                    <x-f-assistant-label for="password">Contraseña</x-f-assistant-label>
                                    <x-form-input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="********"
                                        required/>
                                    <x-form-error name="password" /> 
                                 </div-->

                                <!-- <div>
                                    <x-f-assistant-label for="password_confirmation">Confirm. contraseña</x-f-assistant-label>
                                    <x-form-input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="********"
                                        required/>
                                    <x-form-error name="password_confirmation" />
                                </div-->
                            </div>
                        </div>
                        <div>
                            <x-f-assistant-label for="phone">Nº Teléfono</x-f-assistant-label>
                            <x-form-input
                                type="text"
                                id="phone"
                                name="phone"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="000 000 000"
                                value="{{ old('phone') }}"
                                required/>
                            <x-form-error name="phone" />
                        </div>

                        <div>
                            <x-f-assistant-label for="zip_code">Código Postal</x-f-assistant-label>
                            <x-form-input
                                type="text"
                                id="zip_code"
                                name="zip_code"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="00000"
                                value="{{ old('zip_code') }}"
                                required/>
                            <x-form-error name="zip_code" />
                        </div>
                    </div>

                    <div class="space-y-6 mt-8">
                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <x-form-input type="hidden" name="img_rights_ads" value="0"/>
                                <x-checkbox-input type="checkbox" id="img_rights_ads" name="img_rights_ads" value="1" required/>
                            </div>
                            <div class="ml-3 text-sm">
                                <x-f-assistant-label for="img_rights_ads">Autorización para publicidad</x-f-assistant-label>
                                <p class="text-gray-500">Autorizo a publicar mi imagen/voz por DISTRIBUCIONES EUROCOS, S.L.U. en Piezas Audiovisuales o Gráficas, Medios Escritos y Digitales, Televisión, Cine, Vallas Publicitarias Pancartas y Carteles Publicitarios para ser utilizados en Cursos de Formación / Talleres / Entregar a Peluquerías con promociones de productos que distribuya DISTRIBUCIONES EUROCOS, S.L.U. con fines comerciales y de publicidad de productos y marcas.</p>
                            </div>
                        </div>

                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <x-form-input type="hidden" name="img_rights_web" value="0"/>
                                <x-checkbox-input type="checkbox" id="img_rights_web" name="img_rights_web" value="1" required/>
                            </div>
                            <div class="ml-3 text-sm">
                                <x-f-assistant-label for="img_rights_web" >Autorización web</x-f-assistant-label>
                                <p class="text-gray-500">Autorizo a publicar mi imagen/voz por DISTRIBUCIONES EUROCOS, S.L.U. en su Página Web.</p>
                            </div>
                        </div>

                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <x-form-input type="hidden" name="img_rights_rss" value="0"/>
                                <x-checkbox-input type="checkbox" id="img_rights_rss" name="img_rights_rss" value="1" required/>
                            </div>
                            <div class="ml-3 text-sm">
                                <x-f-assistant-label for="img_rights_rss" >Autorización redes sociales</x-f-assistant-label>
                                <p class="text-gray-500">Autorizo a publicar mi imagen/voz por DISTRIBUCIONES EUROCOS, S.L.U. en redes sociales.</p>
                            </div>
                        </div>

                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <x-form-input type="hidden" name="privacy_policy" value="0"/>
                                <x-checkbox-input type="checkbox" id="privacy_policy" name="privacy_policy" value="1" required/>
                            </div>
                            <div class="ml-3 text-sm">
                                <x-f-assistant-label for="privacy_policy" >Política de privacidad</x-f-assistant-label>
                                <p class="text-gray-500">Autorizo la transferencia internacional de datos según el Marco de Privacidad de Datos UE-EEUU (Data Privacy Framework). Decisión de Ejecución de la Comisión de 10.7.2023, de conformidad con el Reglamento (UE) 2016/679 del Parlamento Europeo y del Consejo sobre el Nivel Adecuado de Protección de Datos Personales Bajo la Privacidad de Datos UE-EE.UU (https://www.dataprivacyframework.gov/s/participant-search/participantdetail?id=a2zt0000000GnywAAC&status=Active).</p>
                            </div>
                        </div>
                    </div>

                    <div class="max-w-sm mx-auto pt-6">
                        <x-form-button>
                            Registrar Asistente 
                        </x-form-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>