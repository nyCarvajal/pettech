@props(['title', 'value' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white p-4 shadow-sm']) }}>
    <p class="text-sm font-medium text-slate-500">{{ $title }}</p>
    @if(!is_null($value))
        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $value }}</p>
    @endif
    @if($subtitle)
        <p class="mt-1 text-xs text-slate-500">{{ $subtitle }}</p>
    @endif
    {{ $slot }}
</div>
