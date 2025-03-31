@props([
'active' => false,
'href' => '',
])

@if ($href)
<a
    href="{{ $href }}"
    {{ $attributes->class([
        'relative inline-block transition duration-300',
        'hover:after:w-[30%] hover:after:opacity-100',
        'active:after:w-[100%] hover:after:opacity-100',
        'after:absolute after:bottom-[3px] after:left-0 after:h-[1px] after:bg-current after:transition-all after:duration-300 after:w-0',
        $active ? 'after:w-full after:opacity-100' : '',
        ])
    }}
>
    {{ $slot }}
</a>

@else
    <button
        {{ $attributes->class([
            'relative inline-block transition duration-300',
            'hover:after:w-[30%] hover:after:opacity-100',
            'active:after:w-[100%] hover:after:opacity-100',
            'after:absolute after:bottom-[-1px] after:left-0 after:h-[1px] after:bg-current after:transition-all after:duration-300 after:w-0',
            $active ? 'after:w-full after:opacity-100' : '',
            ])->merge([
            'type' => 'button',
        ]) }}
    >
        {{ $slot }}
    </button>
@endif
