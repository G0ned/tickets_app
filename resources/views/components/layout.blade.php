<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title> @yield('title', 'Home')</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.tailwindcss.com"></script>
      <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </head>
  <body class="flex flex-col min-h-screen">
    <div class="grow">
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

<footer class="bg-gray-800 text-gray-300 w-full py-8 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="flex flex-col items-start">
                <img src="{{ asset('img/logo.png') }}" alt="Company Logo" class="h-12 w-auto mb-4">
            </div>
            <div class="flex flex-col md:items-end space-y-2">
                <h3 class="text-white font-semibold uppercase tracking-wider text-sm mb-2">Contacto</h3>
                <a href="mailto:correoempresarial@mail.test" class="flex items-center hover:text-white transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-teal-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <span>correoempresarial@mail.test</span>
                </a>
                <div class="flex items-center">
                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-teal-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                    <span>+34 900 123 456</span>
                </div>
            </div>

        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-sm text-center">
            &copy; {{ date('Y') }} Compañía. Todos los derechos reservados.
        </div>
        
    </div>
</footer>
  </body>
</html>
