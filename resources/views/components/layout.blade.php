<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>{{ $title ?? 'Home'}}</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.tailwindcss.com"></script>
      <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </head>
  <body>
    <div class="min-h-full">
      <nav class="bg-teal-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
              <div class="shrink-0">
                <img class="size-8" src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company">
              </div>
              <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-4">
                    <x-nav_link href="/" :active="request()->is('/') ">Inicio</x-nav_link>
                    @guest
                    <x-nav_link href="/attendees/create" :active="request()->is('attendees') ">Registro</x-nav_link>
                    @endguest
                    @auth
                      <x-nav_link href="/events" :active="request()->is('events')">Eventos</x-nav_link>
                    @endauth
                    @role('admin')
                      <x-nav_link href="/events/checkin" :active="request()->is('events/checkin')">Escanear Entradas</x-nav_link>
                    @endrole
                </div>
              </div>
            </div>
            <div class="hidden md:block">
                <div class="relative ml-3">
                  <div>
                    @guest
                      <x-nav_link href="/login" :active="request()->is('login')">Login</x-nav_link>
                    @endguest
                  </div>
                  <div>
                    @auth
                      <x-nav_link href="/attendee/dashboard" :active="request()->is('/atendee/dashboard')">
                        <svg class="h-5 w-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                      </x-nav_link>
                      <form method="POST" action="/logout" class="inline">
                        @csrf
                        <button type="submit" class="text-white hover:bg-teal-700 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                      </form>
                    @endauth
                  </div>
                </div>
            </div>
          </div>
        </div>
      </nav>
      <main>
      <header class="bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 sm:flex sm:justify-between">
          <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $heading }}</h1>
        </div>
      </header>
        @if (session()->has('success'))
              <div x-data="{ show: true }"
                  x-init="setTimeout(() => show = false, 4000)"
                  x-show="show"
                  class="flex mx-auto bg-green-500 text-white py-2 px-4 rounded-xl mt-6 justify-content-center w-50">
                  <p>{{ session('success') }}</p>
              </div>
          @elseif (session()->has('error'))
            <div x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 4000)"
                x-show="show"
                class="flex mx-auto bg-red-500 text-white py-2 px-4 rounded-xl mt-6 justify-content-center w-50">
                <p>{{ session('error') }}</p>
            </div>
        @endif
          <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            {{ $slot }}
          </div>
      </main>
    </div>
    <footer class="bg-gray-700 fixed bottom-0 w-full">
        <div class="text-white flex items-center justify-between">
          <img class="w-content" src="{{ asset('img/logo.png')}}" height="100" width="100" alt="Logo de la compañía"></img>
          <div>
            <div class="flex items-center justify-between">
              <img src="{{ asset('img/mail.png')}}" height="20", width="20" alt="Símbolo correo" class="mr-1"></img>
              <p>Correo electrónico: correoempresarial@mail.test</p>
            </div>
            <p>Dirección:</p>
            <p>Teléfono:</p>
            <p>Dónde encontrarnos</p>
          </div>
        </div>
    </footer>
  </body>
</html>
