<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>About</title>
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
                  <x-nav_link href="/attendees/create" :active="request()->is('attendees') ">Registro</x-nav_link>
                <x-nav_link href="/events" :active="request()->is('events')">Eventos</x-nav_link>
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
                  <x-nav_link href="/admin/dashboard" :active="request()->is('admin/dashboard')">
                    <svg class="h-5 w-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3m0 0l-7 7m7-7l7 7"></path>
                    </svg>
                  </x-nav_link> 
                  <x-nav_link href="/logout" :active="request()->is('logout')">Logout</x-nav_link>
                @endauth
              </div>
            </div>
        </div>
      </div>
    </div>
  </nav>
   @if (session()->has('success'))
        <div x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 4000)"
            x-show="show"
            class="fixed bg-green-500 text-white py-2 px-4 rounded-xl bottom-3 right-3 text-sm"
        >
            <p>{{ session('success') }}</p>
        </div>
    @endif
  <header class="bg-white shadow-sm">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 sm:flex sm:justify-between">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $heading}}</h1>
      
    </div>
  </header>
  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      {{ $slot }}
    </div>
  </main>
</div>
</body>