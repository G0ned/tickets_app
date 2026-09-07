@if ($edition->pivot->cancelled_at)
    <span class="inline-flex items-center gap-1 bg-red-900/60 text-red-300 text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap">
        Cancelada · {{ \Illuminate\Support\Carbon::parse($edition->pivot->cancelled_at)->format('d/m/Y') }}
    </span>
@elseif ($edition->pivot->attendance)
    <span class="inline-flex items-center gap-1 bg-emerald-600 text-white text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap">
        Asistió
    </span>
@else
    <span class="inline-flex items-center gap-1 bg-gray-500 text-white text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap">
        Registrado
    </span>
@endif
