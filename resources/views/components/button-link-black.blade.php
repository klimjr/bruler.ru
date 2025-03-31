<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'text-sm text-center bg-black text-white h-[50px] w-full rounded-xl flex items-center justify-center',
    ]) }}>
    {{ $slot }}
</a>
