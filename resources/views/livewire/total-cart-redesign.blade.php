<div class="bg-grey-100 p-6"
     x-data="{
        isDisabled: false,
        showError: false,
        isPromoCodeInput: @js(session()->get('isActive') ?? false),
        togglePromoCodeInput() {
            this.isPromoCodeInput = !this.isPromoCodeInput
        },
        createOrder() {
            this.isDisabled = true
            this.showError = true
            setTimeout(() => {
                this.isDisabled = false
                this.showError = false
            }, 2000)
        }
     }"
     x-init="isDisabled = {{ !is_null(session()->get('cart')) && $totalCart >= 1 && count(session()->get('cart')) >= 1 ? 'true' : 'false' }} === false">

    @if (!$certInCart)
        @if (Auth::check())
            @if ($totalCart > 4000)
                <livewire:points-redesign :totalCart="$totalCart"/>
            @endif
        @else
            <div class="pb-2 border-b border-grey-200">
                <x-link href="{{ route('profile') }}" active>
                    Авторизуйтесь
                </x-link>
                <span class="ml-1">чтобы списать и накопить бонусы</span>
            </div>
        @endif

        <div class="pb-2 mt-2">
            <button
                    type="button"
                    class="flex items-center cursor-pointer"
                    @click="togglePromoCodeInput"
            >
                <span class="inline-block mr-1">Ввести промокод</span>
                <div x-bind:class="isPromoCodeInput ? 'transform scale-y-[-1] transition-transform duration-300' : 'transform scale-y-100 transition-transform duration-300'">
                    <x-icons.arrow-down-angle/>
                </div>
            </button>
            <div
                    x-show="isPromoCodeInput"
                    x-collapse
                    class="overflow-hidden"
            >
                <div class="pt-3 pb-4 w-full">
                    <div class="relative">
                        <div class="flex gap-2">
                            <div
                                    x-data
                                    class="relative w-full bg-white border border-grey-200  rounded-2xl h-12 outline-none px-4 py-1 text-sm focus-within:border focus-within:border-black @if($error) border-red @endif"
                                    @click="$refs.refPromocodeInputId.focus()"
                            >
                                <input
                                        x-ref="refPromocodeInputId"
                                        id="promocodeInputId"
                                        wire:model="code"
                                        value="{{ $code }}"
                                        class="peer w-full text-sm bg-transparent border-[0px] p-0 translate-y-[18px] placeholder:text-transparent outline-none"
                                        type="text"
                                        placeholder=""
                                        data-pattern-english-number
                                        @if ($isActive) disabled @endif
                                >
                                <label
                                        for="promocodeInputId"
                                        class="absolute top-0 pointer-events-none text-color-111 left-0 ml-4 translate-y-1 text-xs duration-100 ease-linear peer-placeholder-shown:translate-y-4 peer-focus:translate-y-1">
                                    Введите промокод
                                </label>
                            </div>

                            <div class="">
                                @if ($isActive)
                                    <x-button-black
                                            class="w-full"
                                            wire:click="resetCode"
                                    >Сбросить
                                    </x-button-black>
                                @else
                                    <x-button-black
                                            class="w-full"
                                            wire:click="applyCode"
                                    >Применить
                                    </x-button-black>
                                @endif
                            </div>
                        </div>

                        @if ($message)
                            <span class="absolute top-[100%] left-4 text-[11px] @if ($error) text-red @else text-color-111 @endif">{{ $message }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif


    <div class="flex justify-between pb-2 pt-2 border-t border-grey-200">
        <div>Стоимость:</div>
        <div>{{ $totalWithoutDiscountSum }} ₽</div>
    </div>

    @if ($saleBruler != 0)
        <div class="flex justify-between pb-2 mt-2">
            <div>Sale Bruler:</div>
            <div class="text-red">-{{ $saleBruler }} ₽</div>
        </div>
    @endif
    @if ($onePlusOneSale != 0)
        <div class="flex justify-between pb-2 mt-2">
            <div>1+1 = 3:</div>
            <div class="text-red">-{{ $onePlusOneSale }} ₽</div>
        </div>
    @endif
    @if ($saleBonusPromoCert != 0)
        <div class="flex justify-between pb-2 mt-2">
            <div>Баллы/Промокод:</div>
            <div class="text-red">-{{ $saleBonusPromoCert }} ₽</div>
        </div>
    @endif

    <div class="flex justify-between pt-2 mt-2 border-t border-grey-200">
        <div>Итого:</div>
        <div>{{ $totalCart }} ₽</div>
    </div>

    <div wire:key="{{ rand() }}" class="w-full text-center mt-4 md:mt-6">
        <x-button-black
                class="w-full"
                x-bind:disable="isDisabled"
                x-on:click="isDisabled && createOrder"
                wire:click="createOrder"
        >
            Оформить заказ
        </x-button-black>
    </div>

    @if ($showCertAndProductError)
        <div :style="{ opacity: showError ? '100' : '0' }"
             class="fixed bottom-0 opacity-0 right-0 bg-red z-40 text-white p-2 rounded-tl-lg">Нельзя заказывать
            сертификат и товар одновременно!
        </div>
    @endif

    @if ($showSetAndProductError)
        <div :style="{ opacity: showError ? '100' : '0' }"
             class="fixed bottom-0 opacity-0 right-0 bg-red z-40 text-white p-2 rounded-tl-lg">Нельзя заказывать
            набор и товар одновременно!
        </div>
    @endif
</div>
