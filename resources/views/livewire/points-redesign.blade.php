<div class="mb-2 pb-2 border-b border-grey-200">
    @if (!$useBonus && ($bonus == null || $bonus == 0))
        <div class="flex justify-between">
            <div>
                <span class="mr-1">Всего бонусов:</span>
                <span>{{ Auth::user()->points }}</span>
            </div>
            <div>
                <x-link
                    wire:click="applyBonus"
                    active
                >
                    Списать
                </x-link>
            </div>
        </div>
    @else
        <div class="flex justify-between">
            <div>
                <span class="mr-1">Списанные бонусы:</span>
                <span>{{ $bonus }}</span>
            </div>
            <div>
                <x-link
                    wire:click="resetBonus"
                    active
                >
                    Отменить
                </x-link>
            </div>
        </div>

        @error('bonus')
            <p class="mt-2 text-sm text-red">{{ $message }}</p>
        @enderror
    @endif

    @if ($haveSaleProducts)
        <p class="text-xs md:text-sm text-grey-300 mt-1">
            Недоступно списание бонусных баллов на товары из категории Sale
        </p>
    @endif
</div>
