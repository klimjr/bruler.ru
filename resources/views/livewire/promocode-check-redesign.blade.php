<div class="relative">
    <div class="relative w-full border border-grey-200 rounded-2xl h-12 outline-none px-4 py-1 text-sm @error('email') border-red @enderror{{ $isActive ? ' bg-grey-100 focus-within:border focus-within:border-black' : ' bg-white' }}">
        <input
            id="promocodeInput"
            wire:model.lazy="code"
            value="{{ $code }}"
            class="peer w-full text-sm bg-transparent border-[0px] p-0 pr-8 translate-y-[18px] placeholder:text-transparent outline-none"
            type="text"
            placeholder=""
            data-pattern-english-number
            @if ($isActive) disabled @endif
        >
        <label
            for="promo"
            class="absolute top-0 pointer-events-none text-color-111 left-0 ml-4 translate-y-1 text-xs duration-100 ease-linear peer-placeholder-shown:translate-y-4 peer-focus:translate-y-1">
            Введите промокод
        </label>

        @if ($code)
            <button class="absolute right-4 top-1/2 -translate-y-1/2 text-color-111" wire:click="resetGlobal">
                <x-icons.close />
            </button>
        @endif
    </div>

    @if ($message)
        <span class="absolute top-[100%] left-4 text-[11px] mt-1 @if ($error) text-red @else text-color-111 @endif">{{ $message }}</span>
    @endif
</div>
