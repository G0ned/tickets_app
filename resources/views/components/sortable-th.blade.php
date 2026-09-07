@props(['column', 'sort', 'direction', 'align' => 'left'])
@php
    $isSorted = $sort === $column;
    $nextDirection = $isSorted && $direction === 'asc' ? 'desc' : 'asc';
    $alignClass = $align === 'center' ? 'text-center' : 'text-left';
@endphp
<th {{ $attributes->merge(['class' => "px-4 py-3 $alignClass text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap"]) }}>
    <a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection]) }}"
       class="inline-flex items-center gap-1 hover:text-white transition-colors duration-150">
        {{ $slot }}
        <span class="text-teal-400 {{ $isSorted ? '' : 'invisible' }}">{{ $direction === 'asc' ? '↑' : '↓' }}</span>
    </a>
</th>
