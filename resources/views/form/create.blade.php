<x-layout>
    @section('title', 'Formulario de registro')
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
                            <label for="id_type">Tipo de Identificación</label>
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
                            <label for="identification">Número de Identificación</label>
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
                            <label for="firstname">Nombre</label>
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
                            <label for="surname">Apellido/s</label>
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
                            <label for="email">E-mail</label>
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
                        
                        <div>
                            <label for="phone">Nº Teléfono</label>
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
                            <label for="zip_code">Código Postal</label>
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
                    <hr class="my-6 border-gray-300" />
                    <div class="space-y-6 mt-8">
                        <div class="text-sm text-justify">
                                <p>
                                    Solicitamos su consentimiento para tratar los datos con las finalidades relacionadas 
                                    a continuación. La base jurídica que legitima el tratamiento es su consentimiento explícito 
                                    y se entenderá prestado a través de la marcación de la casilla correspondiente. 
                                    Podrá retirar su consentimiento en cualquier momento sin que la retirada del mismo para estas 
                                    finalidades condicione la ejecución de la relación establecida. 
                                    Las imágenes obtenidas, a través de las fotos y vídeos, serán utilizadas tanto para publicaciones presentes 
                                    como para publicaciones futuras, siempre que no retire el consentimiento otorgado.
                                </p>
                            </div>
                        <div class="relative flex items-start">
                            <div class="ml-3 text-sm">
                                <label for="img_rights_ads">Autorización para publicidad</label>
                                <div class="flex items-center">
                                    <select id="img_rights_ads" name="img_rights_ads" value="{{ old('img_rights_ads') }}" class="block rounded-md border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="0" selected>No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                    <p class="text-gray-500 ml-2 text-justify">
                                        Envío de comunicaciones comerciales por medios electrónicos relacionadas con actividades, 
                                        productos y eventos de DISTRIBUCIONES EUROCOS, S.L.U.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="img_rights_web" >Autorización comunicaciones</label>
                                <div class="flex items-center">
                                    <select id="img_rights_web" name="img_rights_web" value="{{ old('img_rights_web') }}" class="block rounded-md border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="0" selected>No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                    <p class="text-gray-500 ml-2 text-justify">Envío por DISTRIBUCIONES EUROCOS, S.L.U. de comunicaciones posteriores al Evento 
                                        para la realización de encuestas de satisfacción y acciones de seguimiento.</p>
                                </div>                              
                            </div>
                        </div>

                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="img_rights_rss" >Autorización redes sociales</label>
                                <div class="flex items-center">
                                    <select id="img_rights_rss" name="img_rights_rss" value="{{ old('img_rights_rss') }}" class="block rounded-md border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="0" selected>No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                    <p class="text-gray-500 ml-2 text-justify">Captar y publicar su imagen / voz durante el Evento con fines de comunicación y 
                                        difusión en los canales corporativos de DISTRIBUCIONES EUROCOS, S.L.U. (Página Web, RRSS, entre otros)
                                    </p>
                                </div> 
                            </div>
                        </div>
                        
                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                            </div>
                            <div class="ml-3 text-sm">
                                <div class="flex items-center">
                                    <select id="privacy_policy" name="privacy_policy" value="{{ old('privacy_policy') }}" class="block rounded-md border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                        <option value="0" selected>No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                    <p class="font-bold ml-2">He leído y acepto la <a href="{{ route('privacy-policy') }}" target="_blank" class="text-blue-500 hover:underline"> Política de Privacidad</a> y el Aviso Legal </p>
                                </div>
                                <x-form-error name="privacy_policy" />
                            </div>
                        </div>
                    <div class="text-sm text-justify mb-4 flex items-start pt-6">
                           <strong class="pr-4">Atención:</strong> Para enviar el formulario debe activar las casillas según corresponda. 
                           Declara haber sido informado sobre la Política de Privacidad y el Aviso Legal, aceptando y consintiendo el 
                           tratamiento de los datos por DISTRIBUCIONES EUROCOS, S.L.U. en la forma y con las finalidades descritas.
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