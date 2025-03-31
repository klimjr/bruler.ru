<div x-data="orderSubmit">
    <div class="w-full bg-grey-100 rounded-2xl flex flex-col overflow-hidden">
        {{--                Товары в корзине--}}
        <div class="px-5 py-4 md:px-6 md:py-6">
            <div class="space-y-4">
                @foreach($products as $product)
                    <x-order.cart-item :product="$product"/>
                @endforeach
            </div>
        </div>

        {{--                Параметры заказа--}}
        <div class="px-5 py-4 md:px-6">
            <p class="text-[18px] font-bold mb-4">Заказ</p>
            @if($this->orderType == \App\Models\Product::TYPE_PRODUCT)
                <div>
                    @if($countWithoutSale > 0)
                        {{--                Бонусы--}}
                        <x-order.bonuses :points="$points" :totalCart="$totalOrder"/>
                        {{--                        Промокод--}}
                        <x-order.promocode/>
                    @endif
                    {{--                        Сертификат--}}
                    <x-order.certificate :certificate="$certificate"/>

                    <div class="pb-4 mt-4 border-b border-grey-200">
                        <div class="flex justify-between mb-4">
                            <div>Стоимость:</div>
                            <x-price :price="$totalWithoutDiscountSum"/>
                        </div>

                        @if($saleBruler != 0)
                            <div class="flex justify-between mb-4">
                                <div>Sale Bruler:</div>
                                <x-price :price="$saleBruler" minus/>
                            </div>
                        @endif

                        @if(session()->get('useBonus'))
                            <div class="flex justify-between mb-4">
                                <div>Бонусы:</div>
                                <x-price :price="session()->get('bonus')" minus/>
                            </div>
                        @endif

                        @if($promocode)
                            <div class="flex justify-between mb-4">
                                <div>Промокод:</div>
                                <x-price :price="$this->totalDiscount" minus/>
                            </div>
                        @endif
                        @if($certificate)
                            <div class="flex justify-between mb-4">
                                <div>Сертификат:</div>
                                <x-price :price="$certificateRemains" minus/>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <div>Стоимость доставки:</div>
                            @if($totalOrder < 15000)
                                <x-price :price="$deliveryPrice"/>
                            @else
                                <x-price :price="0"/>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <div class="flex justify-between mt-4">
                <div>Итого:</div>
                <x-price :price="$totalOrder" :round="false"/>
            </div>
        </div>
        <div class="px-5 pb-4 md:px-6 md:pb-6">
            <div class="pt-4 w-full text-center border-t border-grey-200">
                <div class="flex items-center space-x-4 cursor-pointer">
                    <input
                        @keydown.enter.prevent
                        wire:model.lazy="acceptTerms"
                        id="acceptTerms"
                        type="checkbox"
                        class="form-checkbox mt-1 @error('acceptTerms') error @enderror"
                    />
                    <span
                        class="text-sm text-left font-medium">
                            Я ознакомлен и согласен с условиями
                        <br/>
                        <a
                            class="text-black text-xs underline cursor-pointer font-normal"
                            href="{{ route('documents') }}"
                            target="_blank"
                        >оферты и политики конфиденциальности.</a>
                    </span>
                </div>
                {{--                @error('acceptTerms')--}}
                {{--                <span class="left-4 bottom-[-18px] text-red text-[11px]">{{ $message }}</span>--}}
                {{--                @enderror--}}
                {{--                        @endif--}}
                @error('server_error')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('addressPoint')
                <span class="ml-[16px] text-[#CD0C0C] font-medium text-[11px]">Необходимо выбрать ПВЗ</span>
                @enderror
                @error('addressCdek')
                <span class="ml-[16px] text-[#CD0C0C] font-medium text-[11px]">Необходимо указать адрес</span>
                @enderror
                @error('dostavistaOrder')
                <span class="ml-[16px] text-[#CD0C0C] font-medium text-[11px]">Необходимо выбрать время доставки</span>
                @enderror
            </div>
            <div class="w-full text-center mt-4">
                <x-button-black
                    type="button"
                    class="w-full"
                    wire:click="createOrder"
                    x-data="{ isProcessing: false }"
                    x-on:click="if (!isProcessing) { isProcessing = true; $wire.createOrder().then(() => { createOrder(); isProcessing = false; }) }"
                >
                    Оформить заказ
                </x-button-black>
            </div>
        </div>
    </div>
</div>
@pushOnce('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('orderSubmit', () => ({
            isPromoCodeInput: @js(session()->get('isActive') ?? false),
            isCertificateInput: @js(session()->get('useCertificate') ?? false),

            togglePromoCodeInput() {
                this.isPromoCodeInput = !this.isPromoCodeInput
            },

            toggleCertificateInput() {
                this.isCertificateInput = !this.isCertificateInput
            },

            createOrder() {
                setTimeout(() => {
                    this.scrollToTopErrorField()
                }, 300)
            },

            scrollToTopErrorField() {
                const errorFields = document.querySelectorAll('.error-field');
                if (errorFields.length > 0) {
                    const topErrorField = errorFields[0];
                    topErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }))
    });
</script>
@endpushonce
