@props(['name']) <!-- props define una propiedad llamada name que se para al componente y sirve para identificar el campo del formulario -->

@error($name)
    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
@enderror