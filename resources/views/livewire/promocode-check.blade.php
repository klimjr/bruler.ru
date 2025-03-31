<div>
    <div class="flex gap-0 md:gap-3">
        <div
            class="w-full flex items-center md:min-w-[315px] h-[40px] md:h-[50px] rounded-[12.5px] border-[1.5px] @if ($error) border-[#D0021B] @endif md:border-2">
            <input wire:model="code" placeholder="Введите промокод" value="{{ $code }}"
                class="promocodeInput bg-transparent w-full rounded-[12.5px] border-none h-[40px] md:h-[50px] @if ($isActive) text-[#757575] @endif @if ($error) text-[#D0021B] @endif"
                type="text" @if ($isActive) disabled @endif>
            <div class="uppercase font-semibold pr-2 md:hidden">
                @if ($isActive)
                    <button onclick="clearInput()" wire:click="resetGlobal">Сбросить</button>
                @else
                    <button wire:click="applyGlobal">Применить</button>
                @endif
            </div>
        </div>
        <div class="hidden md:block">
            @if ($isActive)
                <x-button-black class="min-w-[170px]" onclick="clearInput()"
                    wire:click="resetGlobal">Сбросить</x-button-black>
            @else
                <x-button-black class="min-w-[170px]" wire:click="applyGlobal">Применить</x-button-black>
            @endif
        </div>
    </div>
    @if ($message)
        <span
            class="text-xs @if ($error) text-[#D0021B] @else text-[#757575] @endif">{{ $message }}</span>
    @endif
</div>

<script>
    function clearInput() {
        document.querySelectorAll('.promocodeInput').forEach((element) => {
            element.value = ''
        })
    }
</script>
