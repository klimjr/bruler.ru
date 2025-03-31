@props([
'active' => false,
'href' => null,
'disabled' => false,
'square' => false,
'size' => 'lg',
])
{{-- ToDo size lg, md, sm --}}

@if ($href)
    <!-- Если передан href, рендерим ссылку -->
    <a
        href="{{ $href }}"
        {{ $attributes->class([
                "btn-black",
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
                "btn-black",
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


