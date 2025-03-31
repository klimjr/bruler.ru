<div class="mb-[30px]">
    @if (!$useBonus && ($bonus == null || $bonus == 0))
        <div class="flex justify-between items-center">
            <div class="flex gap-x-2 items-center">
                <p class="text-[14px] text-[#757575] font-medium">Ваши баллы:</p>
                <div class="text-[20px] font-semibold">
                    {{ Auth::user()->points }}
                </div>
            </div>
            <x-button-black sm class="max-w-[140px]" wire:click="applyGlobal">Списать
                баллы</x-button-black>
        </div>
    @else
        <div class="flex justify-between items-center">
            <div class="flex gap-x-2 items-center">
                <p class="text-[14px] text-[#757575] font-medium">Списанные баллы:</p>
                <div class="text-[20px] font-semibold">
                    {{ $bonus }}
                </div>
            </div>
            <x-button-black sm class="max-w-[140px] "
                wire:click="resetGlobal">Отменить</x-button-black>
        </div>

        @error('bonus')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    @endif

    @if ($haveSaleProducts)
        <p class="text-xs md:text-sm text-[#757575] mt-1">Недоступно списание бонусных
            баллов на
            товары из категории Sale</p>
    @endif
</div>
