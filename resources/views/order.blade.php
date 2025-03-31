<div class="pt-6 md:pt-8 pb-6 md:pb-8">
    <h1 class="container__inner-mobile text-xl md:text-[30px] font-bold mb-6">Оформление заказа</h1>
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_420px] gap-6">

        <div class="flex flex-col">
            <div class="w-full">
                <form class="space-y-2.5 md:space-y-6">
                    <x-order.recipient/>
                    {{--                    Способы доставки--}}
                    @if ($orderType === \App\Models\Product::TYPE_PRODUCT || $orderType === \App\Models\Product::TYPE_SET)
                        <x-order.delivery
                            :deliveryTypes="$deliveryTypes"
                            :selectedDeliveryType="$selectedDeliveryType"
                            :deliveryPrice="$deliveryPrice"
                            :address="$address"
                            :phone="$phone"
                            :pcs="$pcs"
                            :products="$products"
                            :cityInput="$cityInput"
                            :city-code="$cityCode"
                            :addresses="$addresses"
                            :addressCdek="$addressCdek"
                            :addressDostavista="$addressDostavista"
                            :cities="$cities"
                            :cityGeoLat="$cityGeoLat"
                            :cityGeoLon="$cityGeoLon"
                            :addressPoint="$addressPoint"
                        />
                    @endif

                    <x-order.extra/>

                    {{--                    Способ оплаты--}}
                    <x-order.payment
                        :paymentTypes="$paymentTypes"
                        :selectedPaymentType="$selectedPaymentType"/>
                </form>
            </div>
        </div>
        {{--  Aside  --}}
        <x-order.aside
            :products="$products"
            :count-without-sale="$countWithoutSale"
            :total-without-discount-sum="$totalWithoutDiscountSum"
            :sale-bruler="$saleBruler"
            :promocode="$promocode"
            :certificate="$certificate"
            :certificateRemains="$certificateRemains"
            :promocodeDiscount="$promocodeDiscount"
            :delivery-price="$deliveryPrice"
            :total-order="$totalOrder"
{{--            :bonuses="$bonuses"--}}
            :points="$points"
        />
    </div>
</div>
