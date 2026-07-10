<x-layout>
    @section('title', 'Carteras de clientes')
    <x-slot:heading>Todas las carteras de clientes</x-slot:heading>
    @if($portfolio_list->isEmpty())
        <div class="bg-gray-700 rounded-lg p-10 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-10 text-gray-400 mx-auto mb-3">
                <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <p class="text-gray-300 text-sm">No hay carteras de clientes.</p>
        </div>
    @else
    <div class="bg-gray-700 rounded-xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-600 bg-gray-600">
                            <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Id</th>
                            <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Nombre</th>
                            <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Comercial</th>
                            <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Numero de clientes en la cartera</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        @foreach ($portfolio_list as $portfolio)
                            <tr class="hover:bg-gray-600 transition-colors duration-150">
                                <td class="px-4 py-3 text-white whitespace-nowrap font-medium">{{ $portfolio->id }}</td>
                                <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $portfolio->name }}</td>
                                <td class="px-4 py-3 text-gray-300 text-center whitespace-nowrap">{{ $portfolio->user->name }}</td>
                                <td class="px-4 py-3 text-gray-300 text-center whitespace-nowrap">{{ $portfolio->persons->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layout>