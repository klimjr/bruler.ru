<div class="relative">
    <div
        class="w-full rounded-2xl border border-grey-200 bg-white h-12 px-4 py-1 text-sm relative focus-within:border-[1px] focus-within:border-black {{ $errors->has($name) ? 'border-red' : '' }}">
        <input
            id="{{ $name }}"
            type="{{ $type }}"
            wire:model.lazy="{{ $name }}"
            class="peer w-full text-sm bg-transparent border-[0px] p-0 translate-y-[18px] placeholder:text-transparent outline-none"
            placeholder=""
            @isset($disabled)
            disabled
            @endisset
            {{ $type == 'tel' ? 'data-phone-pattern' : ''}}
        />

        <label
            for="{{ $name }}"
            class="absolute top-0 pointer-events-none text-color-111 left-0 ml-4 translate-y-1 text-xs duration-100 ease-linear peer-placeholder-shown:translate-y-4 peer-focus:translate-y-1">
            {{ $label }}
        </label>
    </div>

    @if (!$errors->has($name))
        <span class="absolute left-4 bottom-[-18px] text-color-111 text-[11px]">{{ $caption }}</span>
    @endif

    @error($name)
    <span class="absolute left-4 bottom-[-18px] text-red text-[11px]">{{ $message }}</span>
    @enderror
</div>
