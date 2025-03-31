@props([
'active' => false,
'href' => null,
'disabled' => false,
'square' => false,
'size' => 'sm',
])
{{-- ToDo size lg, md, sm --}}

@if ($href)
    <!-- Если передан href, рендерим ссылку -->
    <a
        href="{{ $href }}"
        {{ $attributes->class([
                "btn-outlined",
                $size,
                ($active ? "active" : ''),
                ($square ? "square" : ''),
                ($disabled ? "disabled" : '')
            ])->merge([
                'type' => 'button',
            ]) }}
        {{ $disabled ? 'disabled' : '' }}
    >
        {{ $slot }}
    </a>
@else
    <!-- Если href не передан, рендерим кнопку -->
    <button {{ $attributes->class([
                "btn-outlined",
                $size,
                ($active ? "active" : ''),
                ($square ? "square" : ''),
                ($disabled ? "disabled" : '')
        ])->merge([
            'type' => 'button',
        ]) }}
        {{ $disabled ? 'disabled' : '' }}
    >
        {{ $slot }}
    </button>
@endif


