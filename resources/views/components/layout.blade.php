<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title> @yield('title', 'Home')</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.tailwindcss.com"></script>
      <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </head>
  <body class="flex flex-col min-h-screen">
    <div class="grow">
      <nav class="bg-teal-800" x-data="{ open: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
              <div class="shrink-0">
                <img src="{{asset('img/logo.png')}}" alt="Logo Eurocos"  class="h-14 w-auto">
              </div>
              {{-- Links izquierda (escritorio) --}}
              <div class="hidden md:block"> 
                <div class="ml-4 flex items-baseline space-x-4">
                    @auth
                      <x-nav_link href="{{ url('/events/index') }}" :active="request()->is('events')">Eventos</x-nav_link>
                    @endauth
                    @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->isDoorman()))
                      <x-nav_link href="{{ url('/checkin') }}" :active="request()->is('checkin')">Escanear Entradas</x-nav_link>
                    @endif
                    @admin()
                      <x-nav_link href="{{ url('/user-list') }}" :active="request()->is('user-list')">Usuarios</x-nav_link>
                      <x-nav_link href="{{ url('/contacts') }}" :active="request()->is('contacts')">Contactos</x-nav_link>
                    @endadmin
                </div>
              </div>
            </div>

            {{-- Links derecha (escritorio) --}}
            <div class="hidden md:flex items-center space-x-2">
              @auth
                <x-nav_link href="{{ url('/dashboard') }}" :active="request()->is('dashboard')">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                  </svg>
                </x-nav_link>
                <form method="POST" action="/logout" class="inline">
                  @csrf
                  <button type="submit" class="text-white hover:bg-teal-700 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                </form>
              @endauth
            </div>

            {{-- Botón hamburguesa (móvil) --}}
            <div class="md:hidden">
              <button @click="open = !open" class="text-white hover:bg-teal-700 p-2 rounded-md focus:outline-none">
                <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

          </div>
        </div>

        {{-- Menú desplegable (móvil) --}}
        <div x-show="open" @click.outside="open = false" class="md:hidden bg-teal-900 px-4 pb-4 space-y-1">
          @auth
            <a href="{{ url('/events/index') }}" class="block text-white hover:bg-teal-700 px-3 py-2 rounded-md text-base font-medium">Eventos</a>
          @endauth
          @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->isDoorman()))
            <a href="{{ url('/checkin') }}" class="block text-white hover:bg-teal-700 px-3 py-2 rounded-md text-base font-medium">Escanear Entradas</a>
          @endif
          @admin()
            <a href="{{ url('/dashboard') }}" class="block text-white hover:bg-teal-700 px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
          @endadmin
          @auth
            <form method="POST" action="/logout">
              @csrf
              <button type="submit" class="w-full text-left text-white hover:bg-teal-700 px-3 py-2 rounded-md text-base font-medium">Logout</button>
            </form>
          @endauth
        </div>
      </nav>
      <main>
      <header class="bg-white shadow-sm flex items-center justify-between">
        <div class="mx-auto max-w-7xl px-2 py-6 sm:px-6 lg:px-8">
          <h1 class="text-xl text-center tracking-tight text-gray-900">{{ $heading }}</h1>
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
          <div class="mx-auto max-w-7xl px-2 py-6 sm:px-6 lg:px-8">
            {{ $slot }}
          </div>
      </main>
    </div>

<footer class="bg-gray-800 text-gray-300 w-full py-8 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="flex flex-col items-start">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Eurocos" class="h-12 w-auto mb-4">
            </div>
            <div class="flex flex-col md:items-end space-y-2">
                <h3 class="text-white font-semibold uppercase tracking-wider text-sm mb-2">Contacto</h3>
                <a href="mailto:correoempresarial@mail.test" class="flex items-center hover:text-white transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-teal-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <span>ec@eurocos.es</span>
                </a>
                <div class="flex items-center">
                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-teal-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                    <span>+34 922 32 80 49</span>
                </div>

                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="" stroke-width="1.5" stroke="currentColor" class="w-6 h-7 mr-2 text-teal-400">
                    <path strike-linecap="round" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/>
                  </svg>
                <span class="ml-1">Carretera General Las Arenas, nº3 Polígono Industrial Piedra Redonda, Puerto de la Cruz</span>
                </div>
            </div>

        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-sm text-center">
            &copy; {{ date('Y') }} Eurocos.
        </div>
        
    </div>
</footer>
  </body>
</html>