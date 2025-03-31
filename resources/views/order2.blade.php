@section('title', 'оформление заказа')

<div class="flex flex-col justify-center md:flex-row w-full">
    <div class="w-full">
        <div class="text-xl md:text-3xl font-normal my-5">Оформление</div>

        <div class="mb-10 md:mb-0 md:mt-10 w-full max-w-md md:max-w-[600px]">
            <form class="space-y-2.5 md:space-y-6"
                x-effect="if (@this.firstErrorField) {
                document.getElementById(@this.firstErrorField)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                @this.firstErrorField = '';
            }">
                <h3 class="text-sm md:text-[22px] font-semibold">
                    <span>Получатель</span>
                    @if ($type === \App\Models\Product::TYPE_CERTIFICATE)
                        (ваши данные)
                    @endif
                </h3>
                @if ($type === \App\Models\Product::TYPE_CERTIFICATE)
                    <label for="target_email" class="block leading-5 space-y-1 md:space-y-3">
                        <span class="form-label main-text">Эл.почта получателя сертификата</span>
                        <input @keydown.enter.prevent wire:model.lazy="target_email" id="target_email" type="email"
                            class="form-input @error('target_email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror" />
                        @error('target_email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </label>
                @endif
                <label for="name" class="block leading-5 space-y-1 md:space-y-3">
                    <input placeholder="Имя*" @keydown.enter.prevent data-text-pattern wire:model.lazy="name"
                        id="name" type="text" required
                        class="rounded-[10px] border-2 border-black h-[40px] md:h-[50px] w-full @error('name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror" />
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </label>
                <label for="last_name" class="block leading-5 space-y-1 md:space-y-3">
                    <input placeholder="Фамилия*" @keydown.enter.prevent data-text-pattern wire:model.lazy="last_name"
                        id="last_name" type="text" required
                        class="rounded-[10px] border-2 border-black h-[40px] md:h-[50px] w-full @error('last_name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror" />
                    @error('last_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </label>
                <label for="phone" class="block leading-5 space-y-1 md:space-y-3">
                    <input placeholder="Телефон*" maxlength="17" @keydown.enter.prevent data-phone-pattern
                        wire:model.lazy="phone" id="phone" type="tel" required
                        class="rounded-[10px] border-2 border-black h-[40px] md:h-[50px] w-full @error('phone') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror" />
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </label>
                <label for="email" class="block leading-5">
                    <input placeholder="E-Mail*" @keydown.enter.prevent wire:model.lazy="email" id="email"
                        type="email" required
                        class="rounded-[10px] border-2 border-black h-[40px] md:h-[50px] w-full @error('email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror" />
                    <p class="text-xs text-[#757575]">Пожалуйста, используйте почтовые сервисы Yandex или Mail</p>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </label>

                @if ($type === \App\Models\Product::TYPE_PRODUCT || $type === \App\Models\Product::TYPE_SET)
                    <h3 class="text-sm md:text-[22px] font-semibold">Доставка</h3>
                    <label for="country" class="block leading-5 space-y-1 md:space-y-3">
                        <select wire:model="country" id="country" required
                            class="rounded-[10px] border-2 border-black h-[40px] md:h-[50px] w-full @error('country') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror">
                            <option disabled>Укажите страну</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country['code'] }}">{{ $country['name'] }}</option>
                            @endforeach
                        </select>
                        @error('country')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </label>
                    <div x-data="{
                        cities: [],
                        async changeInputCity() {
                            let input = document.querySelector('#cityInput').value;
                            let country = document.querySelector('#country').value;

                            var url = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address';
                            var token = 'aaf90f1dd0eeda4e104d55b986f954a6b8d10ecf';

                            var options = {
                                method: 'POST',
                                mode: 'cors',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'Authorization': 'Token ' + token
                                },
                                body: JSON.stringify({ query: input, from_bound: { value: 'city' }, to_bound: { value: 'settlement' }, locations: [{ country_iso_code: country }] })
                            }

                            fetch(url, options)
                                .then(response => response.text())
                                .then(result => this.cities = Array.from(JSON.parse(result).suggestions))
                                .catch(error => console.log('error', error));
                        },
                        selectCity(value, cityName, cityCode, geoLat, geoLon) {
                            var urlFindCode = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/delivery';
                            var token = 'aaf90f1dd0eeda4e104d55b986f954a6b8d10ecf';

                            if (cityCode) {
                                var options = {
                                    method: 'POST',
                                    mode: 'cors',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'Authorization': 'Token ' + token
                                    },
                                    body: JSON.stringify({ query: cityCode })
                                }

                                fetch(urlFindCode, options)
                                    .then(response => response.text())
                                    .then(result => {
                                        @this.selectCity(value, JSON.parse(result).suggestions[0].data.cdek_id, geoLat, geoLon)
                                    })
                                    .catch(error => console.log('error', error));
                            }

                            this.cities = []
                            let input = document.querySelector('#cityInput')
                            input.value = cityName
                        }
                    }">
                        <div for="city" class="block leading-5 space-y-1 md:space-y-3 relative">
                            <input placeholder="Город*" @keydown.enter.prevent x-on:input.debounce="changeInputCity"
                                wire:model.lazy="cityInput" id="cityInput" type="text" required
                                class="rounded-[10px] border-2 border-black h-[40px] md:h-[50px] w-full @error('cityInput') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror" />
                            @if (count($cities) > 0)
                                <div id="cityList"
                                    class="absolute z-10 bg-white mt-1 w-full border rounded-md shadow-lg">
                                    @foreach ($cities as $city)
                                        <div class="px-3 py-2 hover:bg-gray-200 cursor-pointer"
                                            wire:click="selectCity('{{ $city['value'] }}', '{{ $city['data']['city'] ?? $city['data']['settlement'] }}', '{{ $city['data']['city_kladr_id'] ?? $city['data']['settlement_kladr_id'] }}', '{{ $city['data']['geo_lat'] }}', '{{ $city['data']['geo_lon'] }}')">
                                            {{ $city['value'] }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <template x-if="cities.length > 0">
                                <div id="cityList"
                                    class="absolute max-w-full md:max-w-[392px] top-[47px] md:top-[59px] z-10 bg-white mt-1 w-full border-r-2 border-b-2 border-l-2 rounded-b-md shadow-lg">
                                    <template x-for="city in cities">
                                        <div class="px-3 py-2 hover:bg-gray-200 cursor-pointer" x-text="city.value"
                                            x-on:click="selectCity(city.value, city.data.city ?? city.data.settlement, city.data.city_kladr_id ?? city.data.settlement_kladr_id, city.data.geo_lat, city.data.geo_lon)">
                                        </div>
                                    </template>
                                </div>
                            </template>
                            @error('cityInput')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <input hidden id="geoLat" value="{{ $cityGeoLat }}" />
                        <input hidden id="geoLon" value="{{ $cityGeoLon }}" />
                    </div>


                    <div x-data="{ deliveryType: '{{ $delivery_type }}' }">
                        <div class="mb-5" x-show="deliveryType == '{{ \App\Models\Order::DELIVERY_TYPE_CDEK }}'"
                            x-data="{
                                addresses: [],
                                async changeInputAddress() {
                                    let input = document.querySelector('#address').value
                                    let country = document.querySelector('#country').value
                                    let city = document.querySelector('#cityInput').value

                                    var url = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address';
                                    var token = 'aaf90f1dd0eeda4e104d55b986f954a6b8d10ecf';

                                    var options = {
                                        method: 'POST',
                                        mode: 'cors',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'Authorization': 'Token ' + token
                                        },
                                        body: JSON.stringify({ query: input, from_bound: { value: 'street' }, to_bound: { value: 'flat' }, locations: [{ country_iso_code: country, city: city }] })
                                    }

                                    fetch(url, options)
                                        .then(response => response.text())
                                        .then(result => this.addresses = Array.from(JSON.parse(result).suggestions))
                                        .catch(error => console.log('error', error));

                                },
                                selectAddress(value) {
                                    @this.selectAddress(value)
                                    this.addresses = [];
                                    let input = document.querySelector('#address');
                                    input.value = value;
                                }
                            }">
                            <div for="address" class="block leading-5 space-y-1 md:space-y-3 relative">
                                <input placeholder="Адрес*" @keydown.enter.prevent
                                    x-on:input.debounce="changeInputAddress" wire:change="onChangeAddress"
                                    wire:model.lazy="address" id="address" type="text"
                                    class="rounded-[10px] border-2 border-black h-[40px] md:h-[50px] w-full @error('address') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror" />
                                @if (count($addresses) > 0)
                                    <div id="addressList"
                                        class="absolute z-10 bg-white mt-1 w-full border rounded-md shadow-lg">
                                        @foreach ($addresses as $address)
                                            <div class="px-3 py-2 hover:bg-gray-200 cursor-pointer"
                                                wire:click="selectAddress('{{ $address['value'] }}')">
                                                {{ $address['value'] }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <template x-if="addresses.length > 0">
                                    <div id="addressList"
                                        class="absolute max-w-full md:max-w-[392px] top-[47px] md:top-[59px] z-10 bg-white mt-1 w-full border-r-2 border-b-2 border-l-2 rounded-b-md shadow-lg">
                                        <template x-for="address in addresses">
                                            <div class="px-3 py-2 hover:bg-gray-200 cursor-pointer"
                                                x-text="address.value" x-on:click="selectAddress(address.value)">
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <script
                            src="https://api-maps.yandex.ru/2.1?apikey=<?= config('services.cdek.yandex_api_key') ?>&load=package.full&lang=ru_RU">
                        </script>

                        <div wire:loading>Загрузка...</div>

                        <div wire:loading.class="opacity-50 pointer-events-none"
                            class="space-y-6 md:space-y-3 w-full md:min-w-[600px]">
                            @foreach ($delivery_types as $_delivery_type)
                                <label for="delivery_type_{{ $_delivery_type['id'] }}"
                                    class="flex justify-between items-start"
                                    @if ($_delivery_type['id'] === \App\Models\Order::DELIVERY_TYPE_PICKUP && $cityCode != 44) style="display: none !important;" @endif>
                                    <div class="flex items-center space-x-4">
                                        <input @keydown.enter.prevent wire:model.lazy="delivery_type"
                                            x-on:change="deliveryType = '{{ $_delivery_type['id'] }}'"
                                            wire:change="onChangeDeliveryType" name="delivery_type"
                                            id="delivery_type_{{ $_delivery_type['id'] }}" type="radio"
                                            value="{{ $_delivery_type['id'] }}" required
                                            class="form-checkbox @error('delivery_type') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror" />
                                        <div class="flex flex-col space-y-2 cursor-pointer">
                                            <p class="main-text !text-sm !font-medium">
                                                {{ $_delivery_type['label'] }}</p>
                                            <p class="small-text !text-xs !mt-1 !font-medium">
                                                {!! $_delivery_type['description'] !!}</p>
                                        </div>
                                    </div>
                                    @if ($_delivery_type['price'] != 0)
                                        <p class="price-small !text-[20px] min-w-[85px] md:min-w-[150px]">
                                            + {{ $_delivery_type['price'] }} ₽
                                        </p>
                                    @endif
                                </label>

                                @if ($_delivery_type['id'] === \App\Models\Order::DELIVERY_TYPE_CDEK_PVZ)
                                    <div x-show="deliveryType == '{{ \App\Models\Order::DELIVERY_TYPE_CDEK_PVZ }}'"
                                        id="cdekMap" x-data="{
                                            choosePoint(event) {
                                                    let code = event.target.dataset.code;
                                                    let address = event.target.dataset.address;

                                                    document.getElementById('MapContainer').classList.add('hidden');

                                                    @this.fillDeliveryInfo(code, address);
                                                    @this.reRenderPrice();
                                                },
                                                initWidget() {
                                                    document.getElementById('MapContainer').classList.remove('hidden');
                                                    ymaps.ready(function() {
                                                        if (document.getElementById('YMapsID')) {
                                                            document.getElementById('YMapsID').innerHTML = '';
                                                        }

                                                        let centerMap = [55.76, 37.64];
                                                        let geoLat = document.querySelector('#geoLat').value;
                                                        let geoLon = document.querySelector('#geoLon').value;

                                                        if (geoLat && geoLon) {
                                                            centerMap = [geoLat, geoLon];
                                                        }

                                                        var myMap = new ymaps.Map('YMapsID', {
                                                            center: centerMap,
                                                            zoom: 9,
                                                            controls: []
                                                        });

                                                        let jsonYa = @this.cdekPvzsList;

                                                        if (jsonYa && jsonYa != 1) {
                                                            var clusterer = new ymaps.Clusterer({
                                                                preset: 'islands#invertedDarkGreenClusterIcons',
                                                                groupByCoordinates: false,
                                                                clusterDisableClickZoom: false,
                                                                hasBalloon: false,
                                                                clusterHideIconOnBalloonOpen: false,
                                                                geoObjectHideIconOnBalloonOpen: false
                                                            });

                                                            let placemarks = [];

                                                            JSON.parse(jsonYa).forEach((el) => {
                                                                let phoneNumbers = '';

                                                                JSON.parse(el.phones).forEach((phone) => {
                                                                    phoneNumbers += `<li>${formatPhoneNumber(phone.number)}</li>`;
                                                                });

                                                                var myPlacemark = new ymaps.Placemark([el.location_latitude, el.location_longitude], {
                                                                    balloonContentHeader: `<h3 class='main-text !text-[12px] md:!text-[18px] font-extrabold mb-2 md:mb-4'>${el.address}</h3>`,
                                                                    balloonContent: `<div class='main-text font-semibold'>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class='!text-[12px] md:!text-[18px] mb-2 md:mb-4'>${(@this.tariffInfo.delivery_sum) ? `Доставка: <span class='text-[#20A758]'>${@this.tariffInfo.delivery_sum}₽</span>` : 'Ошибка'}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <ul class='!text-[12px] md:!text-[14px] mb-2 md:mb-4'>${phoneNumbers}</ul>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class='max-w-[120px] md:max-w-[150px] !text-[12px] md:!text-[14px] mb-2 md:mb-4'>${el.work_time}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class='!text-[12px] md:!text-[14px] text-[#757575] mb-2 md:mb-4'>${(el.is_dressing_room) ? 'Есть примерочная*' : 'Нет примерочной*'}</div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class='w-full flex items-center justify-center text-center cursor-pointer'>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div x-on:click='choosePoint' data-address='${el.address}' data-code='${el.code}' class='bg-black py-3 md:py-3.5 !text-[10px] md:!text-[14px] font-semibold md:font-medium text-white rounded-[12px] w-full h-full'>Доставить сюда</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>`
                                                                }, {
                                                                    balloonPanelMaxMapArea: 0,
                                                                    iconColor: '#00bc4c'
                                                                });

                                                                placemarks.push(myPlacemark);
                                                            });

                                                            clusterer.add(placemarks);
                                                            myMap.geoObjects.add(clusterer);
                                                        }
                                                    });

                                                    function formatPhoneNumber(phone) {
                                                        const cleanPhone = phone.replace(/\D/g, '');
                                                        const formattedPhone = cleanPhone.replace(/(\d)(\d{3})(\d{3})(\d{2})(\d{2})/, '+$1 $2 $3-$4-$5');
                                                        return formattedPhone;
                                                    }
                                                },
                                        }">
                                        @if ($city)
                                            <div id="MapContainer" class="flex-grow w-full flex flex-col hidden"
                                                wire:ignore x-init="$nextTick(() => { initWidget(); })">
                                                <div id="YMapsID" class="w-full h-[300px] md:h-[360px]">
                                                </div>
                                            </div>
                                        @endif

                                        @if ($addressPoint)
                                            <div>Выбранный ПВЗ: {{ $addressPoint }}</div>
                                            <div class="underline cursor-pointer" x-on:click='initWidget'>Сменить
                                                ПВЗ
                                            </div>
                                        @endif
                                    </div>
                                    @error('delivery_type_' . $_delivery_type['id'])
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                @endif

                                @if ($_delivery_type['id'] === \App\Models\Order::DELIVERY_TYPE_BOXBERRY)
                                    <div x-data="{
                                        callback_function(result) {
                                            @this.selectBoxberry(result.id)
                                        }
                                    }">
                                        <script type="text/javascript" src="https://points.boxberry.de/js/boxberry.js"></script>
                                        <p @click="boxberry.open(callback_function, '', @this.city || 'Москва', '', @this.totalPrice, @this.sumWeight); return false;"
                                            class="small-text underline text-gray cursor-pointer">выбрать на карте
                                        </p>
                                    </div>
                                    @error('delivery_type_' . $_delivery_type['id'])
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <h3 class="text-sm md:text-[22px] font-semibold">Дополнительно</h3>
                <label for="comment" class="block leading-5 space-y-1 md:space-y-3">
                    <input placeholder="Комментарий" @keydown.enter.prevent wire:model.lazy="comment" id="comment"
                        type="text"
                        class="rounded-[10px] border-2 border-black h-[40px] md:h-[50px] w-full @error('comment') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror" />
                    @error('comment')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </label>


                <div class="space-y-6 md:space-y-3" x-data="{ selectedPaymentType: @entangle('payment_type') }">
                    <h3 class="text-sm font-semibold">Способ оплаты</h3>
                    <div class="relative w-full h-full overflow-x-auto">
                        <div class="flex gap-x-3 h-full">
                            @foreach ($payment_types as $_payment_type)
                                <div class="h-full flex-shrink-0 cursor-pointer"
                                    @click="selectedPaymentType = {{ $_payment_type['id'] }}">
                                    <img :class="{ 'hidden': selectedPaymentType === {{ $_payment_type['id'] }} }"
                                        src="/storage/{{ $_payment_type['icon'] }}"
                                        class="object-cover w-full h-[80px]">
                                    <img :class="{ 'hidden': selectedPaymentType !== {{ $_payment_type['id'] }} }"
                                        src="/storage/{{ $_payment_type['icon_active'] }}"
                                        class="object-cover w-full h-[80px]">
                                </div>
                            @endforeach
                            <input type="hidden" name="payment_type" x-model="selectedPaymentType"
                                value="{{ old('payment_type') }}">
                        </div>
                    </div>
                    @error('payment_type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <label for="accept_pp" class="block leading-5 space-y-1 md:space-y-3">
                    <div class="flex items-center space-x-4 cursor-pointer">
                        <input @keydown.enter.prevent wire:model.lazy="accept_pp" id="accept_pp" type="checkbox"
                            checked
                            class="form-checkbox @error('accept_pp') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror" />
                        <span class="text-sm">
                            Я ознакомлен и согласен с условиями <br /> <span
                                class="text-[#757575] text-xs underline cursor-pointer"><a
                                    href="{{ route('documents') }}">оферты и политики
                                    конфиденциальности.</a></span>
                        </span>
                    </div>
                    @error('accept_pp')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </label>
            </form>

            <div>
                <div class="text-xl md:text-[22px] font-normal text-primary my-[30px]"">
                    Ваш заказ
                </div>

                @if ($type == \App\Models\Product::TYPE_PRODUCT)
                    @if (Auth::user() && $totalCart > 4000)
                        <livewire:points :totalCart="$totalCart" />
                    @endif

                    <div wire:ignore>
                        <livewire:promocode-check />
                    </div>

                    <div class="flex gap-0 md:gap-3 mt-5">
                        <div
                            class="w-full flex items-center min-w-[315px] h-[40px] md:h-[50px] rounded-[12.5px] border-[1.5px] @error('typedCertificate') border-[#D0021B] @enderror md:border-2">
                            <input wire:model.lazy="typedCertificate" id="typedCertificate" placeholder="Сертификат"
                                class="certInput bg-transparent w-full rounded-[12.5px] border-none h-[40px] md:h-[50px] @if ($useCertificate) text-[#757575] @endif @error('typedCertificate') text-[#D0021B] @enderror"
                                type="text" @if ($useCertificate) disabled @endif>
                            <div class="uppercase font-semibold pr-2 md:hidden">
                                @if ($useCertificate)
                                    <button onclick="clearCertInput()" wire:click="resetCertificate">Сбросить</button>
                                @else
                                    <button wire:click="checkCertificate">Применить</button>
                                @endif
                            </div>
                        </div>
                        <div class="hidden md:block">
                            @if ($useCertificate)
                                <x-button-black class="min-w-[170px]" onclick="clearCertInput()"
                                    wire:click="resetCertificate">Сбросить</x-button-black>
                            @else
                                <x-button-black class="min-w-[170px]"
                                    wire:click="checkCertificate">Применить</x-button-black>
                            @endif
                        </div>
                    </div>
                    @if ($certMessage)
                        <span
                            class="text-xs @if ($certError) text-[#D0021B] @else text-[#757575] @endif">{{ $certMessage }}</span>
                    @endif

                    <script>
                        function clearCertInput() {
                            document.querySelectorAll('.certInput').forEach((element) => {
                                element.value = ''
                            })
                        }
                    </script>
                @endif

                <div class="flex justify-between mt-4 md:mt-[30px]">
                    <div class="text-sm">Сумма:</div>
                    <div class="text-xl font-semibold">{{ number_format($totalCartWitchout, 0, '.', '.') }}₽</div>
                </div>

                @if ($saleBruler != 0)
                    <div class="flex justify-between mt-2">
                        <div class="text-sm">Sale Bruler:</div>
                        <div class="price-small !text-[20px] !text-[#D0021B]">
                            -{{ number_format($saleBruler, 0, '.', '.') }}₽</div>
                    </div>
                @endif
                @if ($onePlusOneSale != 0)
                    <div class="flex justify-between mt-2">
                        <div class="text-sm">1+1 = 3:</div>
                        <div class="price-small !text-[20px] !text-[#D0021B]">
                            -{{ number_format($onePlusOneSale, 0, '.', '.') }}₽</div>
                    </div>
                @endif
                @if ($saleBonusPromoCert != 0)
                    <div class="flex justify-between mt-2">
                        <div class="text-sm">Баллы/Промокод/Сертификат:</div>
                        <div class="price-small !text-[20px] !text-[#D0021B]">
                            -{{ number_format($saleBonusPromoCert, 0, '.', '.') }}₽</div>
                    </div>
                @endif

                {{-- @if ($usePromocode || $useCertificate || ($useBonus && $bonus > 0))
                    <div class="flex justify-between mt-[20px]">
                        <div class="text-sm">Сумма с учетом скидки:</div>
                        <div class="text-xl font-semibold">
                            {{ $totalCartWithPromocode >= 1 ? number_format($totalCartWithPromocode, 0, '.', '.') : 0 }}₽
                        </div>
                    </div>
                @endif --}}

                @if ($type === \App\Models\Product::TYPE_PRODUCT || $type === \App\Models\Product::TYPE_SET)
                    <div class="flex justify-between my-2">
                        <div class="text-sm">Стоимость доставки:</div>

                        <div class="text-xl font-semibold">{{ number_format($deliveryPrice, 0, '.', '.') }}₽</div>
                    </div>
                @endif
                <div class="flex border-t-[2px] border-[#27272733] pt-3.5 justify-between items-center">
                    <div class="text-sm">Итого:</div>
                    <div class="text-xl font-semibold">
                        {{ $totalPrice >= 1 ? number_format($totalPrice, 0, '.', '.') : 0 }}₽</div>
                </div>
                @error('server_error')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @error('products_amount')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <a @if ($productAmountError) href="{{ route('collection.filter') }}" @endif
                    @if (!$productAmountError) wire:click.prevent="confirmOrder" @endif
                    wire:loading.attr="disabled" class="flex justify-center mt-[30px]">
                    <x-button-black class="w-[175px]">
                        @if ($isLoading)
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8v-8H4z"></path>
                            </svg>
                        @elseif ($productAmountError)
                            Вернуться в каталог
                        @else
                            Оформить заказ
                        @endif
                    </x-button-black>
                </a>
            </div>
        </div>
    </div>
    {{--      Navigation --}}
    <div class="relative w-full">
        <div class="md:sticky top-24 right-0 overflow-auto flex items-end flex-col max-h-[700px] hide_in_mobile">
            @foreach ($products as $productId => $product)
                @if ($product['type'] === \App\Models\Product::TYPE_PRODUCT)
                    @livewire('order-item', ['product' => $product, 'quantity' => $product['quantity'], 'type' => $product['type'], 'variant' => $product['variant'], 'certificate' => null, 'set_products' => null, 'is_free' => $product['is_free']], key($productId))
                @endif

                @if ($product['type'] === \App\Models\Product::TYPE_CERTIFICATE)
                    @livewire('order-item', ['product' => $product, 'quantity' => $product['quantity'], 'type' => $product['type'], 'variant' => null, 'certificate' => $product['certificate'], 'set_products' => null, 'is_free' => $product['is_free']], key($productId))
                @endif

                @if ($product['type'] === \App\Models\Product::TYPE_SET)
                    @livewire('order-item', ['product' => $product, 'quantity' => $product['quantity'], 'type' => $product['type'], 'variant' => null, 'certificate' => null, 'set_products' => $product['set_products'], 'is_free' => $product['is_free']], key($productId))
                @endif
            @endforeach
        </div>
    </div>
</div>
