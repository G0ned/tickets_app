{{-- Códigos de verificación de una persona en una lista. Espera $codes (colección de VerificationCode). --}}
@if ($codes->isEmpty())
    <span class="text-gray-500 text-xs">—</span>
@else
    <ul class="space-y-1">
        @foreach ($codes as $code)
            <li class="flex items-center gap-2">
                <span class="font-mono text-white tracking-wider">{{ $code->code }}</span>
                @if ($code->isUsed())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-900/60 text-emerald-300 text-xs font-semibold"
                          title="Usado el {{ $code->used_at->format('d/m/Y H:i') }}">Usado</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-900/60 text-amber-300 text-xs font-semibold">Pendiente</span>
                @endif
            </li>
        @endforeach
    </ul>
@endif
