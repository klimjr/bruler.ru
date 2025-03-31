<x-order.section>
    <x-slot:header>
        <h3 class="md:text-[18px] font-bold">Доставка</h3>
    </x-slot:header>

    <div class="space-y-4" x-data="deliveryOrder">
        @foreach ($deliveryTypes as $type)
            <div
                id="d-{{$type['id'] }}"
                {{--                wire:click="set('selectedDeliveryType', '{{ $type['id'] }}')"--}}
            >
                {{--                <div x-data="{sdt: @entangle('selectedDeliveryType')}" x-text="sdt"></div>--}}
                <x-order.radio-element
                    wire:click="set('selectedDeliveryType', '{{ $type['id'] }}')"
                    {{--                    wire:key="{{ now() }}"--}}
                    id="{{ $type['id'] }}"
                    activeId="{{ $selectedDeliveryType }}"
                >
                    {{ $type['label'] }}
                    <x-slot:end>
                        @if($type['id'] == $selectedDeliveryType)
                            <div id="{{ 'price-' . $type['id'] }}">
                            <x-price :price="$deliveryPrice" plus/>
                            </div>
                        @endif
                    </x-slot:end>
                    <x-slot:accordion>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($type['id'] == 'pickup')
                                    {!! $type['description'] !!}
                                @else
                                    <x-order.input
                                        id="countryName"
                                        name="countryName"
                                        label="Страна"
                                        type="text"
                                        caption=""
                                        disabled="disabled"
                                    />
                                    @if($type['id'] == 'dostavista')
                                        <x-order.input
                                            id="{{ $type['id'] }}-city"
                                            name="city"
                                            label="Город"
                                            type="text"
                                            caption=""
                                            disabled="disabled"
                                        />
                                    @else
                                        <x-order.city-select
                                            :cities="$cities"
                                            :cityGeoLat="$cityGeoLat"
                                            :cityGeoLon="$cityGeoLon"
                                        />
                                    @endif

                                    @if($type['id'] == 'dostavista')
                                        <x-order.address-select
                                            :cityInput="$cityInput"
                                            :type="$type['id']"
                                            :addresses="$addresses"
                                            model="addressDostavista"
                                            :address="$addressDostavista"
                                        />
                                    @endif

                                    @if($type['id'] == 'cdek' && $cityCode)
                                        <x-order.address-select
                                            :cityInput="$cityInput"
                                            :type="$type['id']"
                                            :addresses="$addresses"
                                            model="addressCdek"
                                            :address="$addressCdek"
                                        />
                                    @endif
                                @endif
                            </div>
                        </div>


                        @switch($type['id'])
                            @case('dostavista')
                                <livewire:order.dostavista :addressDostavista="$addressDostavista" :pcs="$pcs" :phone="$phone"/>
                                @break
                            @case('cdek')
                                <livewire:order.cdek wire:key="cdek" :addressCdek="$addressCdek" :cityCode="$cityCode"
                                                     :products="$products"/>
                                @break
                            @case('cdek_pvz')
                                <x-order.cdek-pvz wire:key="{{ now() }}"
                                                  :city-geo-lat="$cityGeoLat"
                                                  :city-geo-lon="$cityGeoLon"
                                                  :city="$cityCode"
                                                  :address-point="$addressPoint"/>
                                @break
                        @endswitch
                    </x-slot:accordion>
                </x-order.radio-element>
            </div>
        @endforeach
        <input
            wire:model="selectedDeliveryType"
            type="hidden"
            name="delivery_type"
        >

        @error('delivery_type')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

</x-order.section>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('deliveryOrder', () => ({}));
    });
</script>
