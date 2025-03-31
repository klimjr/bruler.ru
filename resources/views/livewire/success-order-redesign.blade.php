@php
    $totalWithoutDiscountSum = 3000;
    $saleBruler = -200;
    $promocode = -500;
    $totalOrder = 2300;
@endphp


<div
    x-data="thankyouData"
    x-init="createDataLayerPurchase()"
    class="max-w-[500px] mx-auto pb-4 pt-4 md:pb-6 md:pt-6 px-5 md:px-6"
>
    <div class="h-[194px] md:h-[224px] w-full p-4 bg-grey-100 rounded-t-2xl text-center flex flex-col items-center justify-center">
        <div class="mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="43" height="42" viewBox="0 0 43 42" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.75 21C0.75 9.40154 10.1515 0 21.75 0C33.3485 0 42.75 9.40154 42.75 21C42.75 32.5985 33.3485 42 21.75 42C10.1515 42 0.75 32.5985 0.75 21ZM29.5254 17.0929C29.6546 16.9207 29.7481 16.7244 29.8004 16.5156C29.8527 16.3067 29.8627 16.0895 29.8298 15.8768C29.797 15.664 29.7219 15.4599 29.6091 15.2766C29.4962 15.0932 29.3479 14.9343 29.1727 14.8091C28.9976 14.6839 28.7991 14.595 28.5891 14.5476C28.3791 14.5002 28.1618 14.4952 27.9498 14.533C27.7378 14.5708 27.5356 14.6506 27.3549 14.7677C27.1742 14.8847 27.0188 15.0368 26.8977 15.2148L19.9278 24.9717L16.43 21.4738C16.1238 21.1885 15.7188 21.0332 15.3003 21.0405C14.8818 21.0479 14.4825 21.2175 14.1865 21.5134C13.8905 21.8094 13.721 22.2087 13.7136 22.6272C13.7062 23.0457 13.8616 23.4507 14.1469 23.7569L18.9931 28.6031C19.1589 28.7688 19.3588 28.8964 19.5789 28.9771C19.799 29.0578 20.034 29.0896 20.2676 29.0703C20.5013 29.0511 20.7279 28.9812 20.9318 28.8655C21.1357 28.7499 21.312 28.5912 21.4485 28.4006L29.5254 17.0929Z" fill="#16945E"/>
            </svg>
        </div>
        <div class="mb-2 font-bold text-[18px]">
            Спасибо за покупку!
        </div>
        <div class="text-grey-300">
            Заказ №34556
        </div>
    </div>

    <div class="bg-grey-100 h-10 md:h-12 w-full flex items-center relative">
        <div class="absolute left-0 transform -translate-x-1/2 bg-white w-10 md:w-12 h-10 md:h-12 rounded-full"></div>
        <div class="absolute right-0 transform translate-x-1/2 bg-white w-10 md:w-12 h-10 md:h-12 rounded-full"></div>
        <div class="border-b-2 w-full" style="border-bottom-style: dashed; border-color: #D1D1D1;"></div>
    </div>

    <div class="bg-grey-100 p-4 rounded-b-2xl">
        <div class="space-y-4">
            <x-order.cart-item />
            <x-order.cart-item />
            <x-order.cart-item />
        </div>

        <div class="pb-4 mt-4 border-b border-grey-200">
            <div class="flex justify-between mb-4">
                <div>Стоимость</div>
                <div>{{ $totalWithoutDiscountSum }} ₽</div>
            </div>

            @if($saleBruler != 0)
                <div class="flex justify-between mb-4">
                    <div>Sale Bruler</div>
                    <div class="text-red">{{ $saleBruler }} ₽</div>
                </div>
            @endif

            <div class="flex justify-between">
                <div>Промокод</div>
                <div class="text-red">{{ $promocode }} ₽</div>
            </div>
        </div>

        <div class="flex justify-between mt-4">
            <div>Итого</div>
            <div>{{ $totalOrder }} ₽</div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('thankyouData', () => ({
            order_id: 0,
            order_products: [],
            promocode: null,

            createDataLayerPurchase() {
                // if (document.cookie.split(";").some((item) => item.trim().startsWith("YMPurchaseSaved"))) return false
                this.order_id = `{{ !is_null(request("order_id")) ? request("order_id") : 0 }}`
                this.promocode = `{{ !is_null(request("promocode")) ? request("promocode") : 0 }}`
                this.order_products = `{{ !is_null(json_encode(request("order_products"))) ? json_encode(request("order_products")) : [] }}`

                if (!this.order_products || this.order_products.length === 0 || this.order_id === 0) {
                    return false
                }

                let actionField = {
                    "id" : this.order_id,
                }

                if (this.promocode != 0) actionField.coupon = this.promocode

                window.dataLayer.push({
                    "ecommerce": {
                        "currencyCode": "RUB",
                        "purchase": {
                            "actionField": actionField,
                            "products": this.order_products.map((product) => {
                                if (!product.variant) return null;
                                return {
                                    id: product.id,
                                    name: product.name,
                                    price: product.price,
                                    variant: product.variant.toString(),
                                    quantity: product.quantity,
                                }
                            })
                        }
                    }
                })

                let expireDate = new Date()
                expireDate.setTime(expireDate.getTime() + (60 * 60 * 1000))
                document.cookie = `YMPurchaseSaved=true; expires=${expireDate.toUTCString()}; path=/`
            }
        }));
    });
</script>
