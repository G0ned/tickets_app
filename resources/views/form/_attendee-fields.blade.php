@php($submitLabel ??= 'Registrar Asistente')

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 p-2 sm:p-4">
    @error('error')
        <div class="alert alert-danger">
            {{ $message }}
        </div>
    @enderror
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
        <x-form-error name="identification" />
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
    <div class="text-sm text-justify px-0 sm:px-2">
            <p class="px-4">
                Solicitamos su consentimiento para tratar los datos con las finalidades relacionadas
                a continuación. La base jurídica que legitima el tratamiento es su consentimiento explícito
                y se entenderá prestado a través de la marcación de la casilla correspondiente.
                Podrá retirar su consentimiento en cualquier momento sin que la retirada del mismo para estas
                finalidades condicione la ejecución de la relación establecida.
                Las imágenes obtenidas, a través de las fotos y vídeos, serán utilizadas tanto para publicaciones presentes
                como para publicaciones futuras, siempre que no retire el consentimiento otorgado.
            </p>
        </div>
    <div class="relative flex items-start px-0 sm:px-2">
        <div class="ml-3 text-sm w-full">
            <label for="img_rights_ads">Autorización para publicidad</label>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <select id="img_rights_ads" name="img_rights_ads" class="block w-full sm:w-auto rounded-md border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <option value="" disabled {{ old('img_rights_ads') === null ? 'selected' : '' }}>Elija una opción</option>
                    <option value="0" {{ old('img_rights_ads') === '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('img_rights_ads') === '1' ? 'selected' : '' }}>Sí</option>
                </select>
                <p class="text-gray-500 sm:ml-2 text-justify px-2">
                    Envío de comunicaciones comerciales por medios electrónicos relacionadas con actividades,
                    productos y eventos de DISTRIBUCIONES EUROCOS, S.L.U.
                </p>
            </div>
            <x-form-error name="img_rights_ads" />
        </div>
    </div>

    <div class="relative flex items-start px-0 sm:px-2">
        <div class="flex items-center h-5">
        </div>
        <div class="ml-3 text-sm w-full">
            <label for="img_rights_web" >Autorización comunicaciones</label>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <select id="img_rights_web" name="img_rights_web" class="block w-full sm:w-auto rounded-md border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <option value="" disabled {{ old('img_rights_web') === null ? 'selected' : '' }}>Elija una opción</option>
                    <option value="0" {{ old('img_rights_web') === '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('img_rights_web') === '1' ? 'selected' : '' }}>Sí</option>
                </select>
                <p class="text-gray-500 sm:ml-2 text-justify px-2">Envío por DISTRIBUCIONES EUROCOS, S.L.U. de comunicaciones posteriores al Evento
                    para la realización de encuestas de satisfacción y acciones de seguimiento.</p>
            </div>
            <x-form-error name="img_rights_web" />
        </div>
    </div>

    <div class="relative flex items-start px-0 sm:px-2">
        <div class="flex items-center h-5">
        </div>
        <div class="ml-3 text-sm w-full">
            <label for="img_rights_rss" >Autorización redes sociales</label>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <select id="img_rights_rss" name="img_rights_rss" class="block w-full sm:w-auto rounded-md border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <option value="" disabled {{ old('img_rights_rss') === null ? 'selected' : '' }}>Elija una opción</option>
                    <option value="0" {{ old('img_rights_rss') === '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('img_rights_rss') === '1' ? 'selected' : '' }}>Sí</option>
                </select>
                <p class="text-gray-500 sm:ml-2 text-justify px-2">Captar y publicar su imagen / voz durante el Evento con fines de comunicación y
                    difusión en los canales corporativos de DISTRIBUCIONES EUROCOS, S.L.U. (Página Web, RRSS, entre otros)
                </p>
            </div>
            <x-form-error name="img_rights_rss" />
        </div>
    </div>

    <div class="relative flex items-start px-0 sm:px-2">
        <div class="flex items-center h-5">
        </div>
        <div class="ml-3 text-sm w-full">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <select id="privacy_policy" name="privacy_policy" class="block w-full sm:w-auto rounded-md border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                    <option value="" disabled {{ old('privacy_policy') === null ? 'selected' : '' }}>Elija una opción</option>
                    <option value="0" {{ old('privacy_policy') === '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('privacy_policy') === '1' ? 'selected' : '' }}>Sí</option>
                </select>
                <p class="font-bold sm:ml-2">He leído y acepto la <a href="{{ route('privacy-policy') }}" target="_blank" class="text-blue-500 hover:underline"> Política de Privacidad</a> y el Aviso Legal </p>
            </div>
            <x-form-error name="privacy_policy" />
        </div>
    </div>
<div class="text-sm text-justify mb-4 flex items-start px-2 sm:p-2">
       <strong class="pr-4">Atención:</strong> Para enviar el formulario debe activar las casillas según corresponda.
       Declara haber sido informado sobre la Política de Privacidad y el Aviso Legal, aceptando y consintiendo el
       tratamiento de los datos por DISTRIBUCIONES EUROCOS, S.L.U. en la forma y con las finalidades descritas.
</div>
<div class="max-w-sm mx-auto p-2 sm:p-4">
    <x-form-button>
        {{ $submitLabel }}
    </x-form-button>
</div>
