<div class="px-2.5 md:px-0 w-full mt-3" x-data="{
    isDisabled: false,
    showError: false,
    createOrder() {
        this.isDisabled = true
        this.showError = true
        setTimeout(() => {
            this.isDisabled = false
            this.showError = false
        }, 2000)
    }
}" x-init="isDisabled = {{ !is_null(session()->get('cart')) && $totalCart >= 1 && count(session()->get('cart')) >= 1 ? 'true' : 'false' }} === false">

    {{-- @if ($productCount == 2)
        <div class="flex rounded-[12px] items-center gap-x-2 bg-white md:bg-[#F2F2F4] px-[8px] md:px-[12px] py-[12px] md:py-[16px] mb-5">
            <div class="h-[40px]">
                <x-icons.present />
            </div>
            <p class="font-medium text-sm md:text-base leading-[19.12px] md:leading-[21.86px]">Добавьте 3-ю вещь в корзину и получите одну
                из них в подарок</p>
        </div>
    @endif --}}

    <div class="h3 !text-[20px] md:!text-[22px] text-primary font-medium mb-2">
        Ваш заказ
    </div>

    @if (!$certInCart)
        @if (Auth::check() && $totalCart > 4000)
            <livewire:points :totalCart="$totalCart" />
        @endif

        <livewire:promocode-check />
    @endif

    @if ($totalWithoutDiscountSum != 0 && $totalCart != $totalWithoutDiscountSum)
        <div class="flex justify-between mt-4 md:mt-12">
            <div class="text-[#757575] text !text-[16px] md:!text-[20px]">Сумма:</div>
            <div class="price-small !text-[20px]">{{ $totalWithoutDiscountSum }} ₽</div>
        </div>
    @endif
    {{-- @if ($totalDiscountSum != 0)
        <div class="flex justify-between mt-2">
            <div class="text-[#757575] text !text-[16px] md:!text-[20px]">Скидка:</div>
            <div class="price-small !text-[20px] !text-[#D0021B]">-{{ $totalDiscountSum }} ₽</div>
        </div>
    @endif --}}
    @if ($saleBruler != 0)
        <div class="flex justify-between mt-2">
            <div class="text-[#757575] text !text-[16px] md:!text-[20px]">Sale Bruler:</div>
            <div class="price-small !text-[20px] !text-[#D0021B]">-{{ $saleBruler }} ₽</div>
        </div>
    @endif
    @if ($onePlusOneSale != 0)
        <div class="flex justify-between mt-2">
            <div class="text-[#757575] text !text-[16px] md:!text-[20px]">1+1 = 3:</div>
            <div class="price-small !text-[20px] !text-[#D0021B]">-{{ $onePlusOneSale }} ₽</div>
        </div>
    @endif
    @if ($saleBonusPromoCert != 0)
        <div class="flex justify-between mt-2">
            <div class="text-[#757575] text !text-[16px] md:!text-[20px]">Баллы/Промокод:</div>
            <div class="price-small !text-[20px] !text-[#D0021B]">-{{ $saleBonusPromoCert }} ₽</div>
        </div>
    @endif
    <div
        class="flex border-t-[2px] border-[#757575] pt-3 justify-between @if ($totalWithoutDiscountSum != 0 && $totalCart != $totalWithoutDiscountSum && $totalDiscountSum != 0) mt-2 @else mt-4 md:mt-12 @endif">
        <div class="text-[#757575] text !text-[16px] md:!text-[20px]">Итого:</div>
        <div class="price-small !text-[20px]">{{ $totalCart }} ₽</div>
    </div>
    <div wire:key="{{ rand() }}" class="w-full text-center my-6">
        <div x-show="!isDisabled" :style="{ opacity: !isDisabled ? '100' : '0' }" class="opacity-0">
            <x-button-black @click="createOrder()" wire:click="createOrder" class="">
                Оформить заказ
            </x-button-black>
        </div>
        <div x-show="isDisabled" :style="{ opacity: isDisabled ? '100' : '0' }" class="opacity-0">
            <x-button-black disabled class="">
                Оформить заказ
            </x-button-black>
        </div>
    </div>
    @if ($showCertAndProductError)
        <div :style="{ opacity: showError ? '100' : '0' }"
            class="fixed bottom-0 opacity-0 right-0 bg-red z-40 text-white p-2 rounded-tl-lg">Нельзя заказывать
            сертификат и товар одновременно!</div>
    @endif

    @if ($showSetAndProductError)
        <div :style="{ opacity: showError ? '100' : '0' }"
            class="fixed bottom-0 opacity-0 right-0 bg-red z-40 text-white p-2 rounded-tl-lg">Нельзя заказывать
            набор и товар одновременно!</div>
    @endif
</div>
