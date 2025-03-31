<div {{ $attributes->merge(['class' => 'bg-grey-100 p-5 md:p-6 rounded-2xl']) }}>
    @isset($header)
        <div class="mb-4">
            {{ $header }}
        </div>
    @endisset

    <div>
        {{ $slot }}
    </div>
</div>
