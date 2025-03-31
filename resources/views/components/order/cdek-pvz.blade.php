<div class="mt-4">
    <script
        src="https://api-maps.yandex.ru/2.1?apikey=<?= config('services.cdek.yandex_api_key') ?>&load=package.full&lang=ru_RU">
    </script>
    <div id="cdekMap" x-data="{
                                            choosePoint(event) {
                                                    let code = event.target.dataset.code;
                                                    let address = event.target.dataset.address;

                                                    document.getElementById('MapContainer').classList.add('hidden');

                                                    @this.fillDeliveryInfo(code, address)
{{--                                                    @this.reRenderPrice();--}}
                                                },
                                                initWidget() {
                                                    document.getElementById('MapContainer').classList.remove('hidden');

                                                    ymaps.ready(function() {
                                                        if (document.getElementById('YMapsID')) {
                                                            document.getElementById('YMapsID').innerHTML = '';
                                                        }

                                                        let centerMap = [55.76, 37.64];
                                                        let geoLat = document.querySelector('#geoLat').value;
{{--                                                        let geoLat = '{{ $cityGeoLat }}';--}}
                                                        let geoLon = document.querySelector('#geoLon').value;
{{--                                                        let geoLon = '{{ $cityGeoLon }}';--}}
                                                        console.log('geoLat', geoLat, 'geoLon', geoLon);

                                                        if (geoLat && geoLon) {
                                                            centerMap = [geoLat, geoLon];
                                                        }
                                                         console.log('initWidget', centerMap);

                                                        var myMap = new ymaps.Map('YMapsID', {
                                                            center: centerMap,
                                                            zoom: 9,
                                                            controls: []
                                                        });

                                                        let jsonYa = @this.cdekPvzsList

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
                                                }
                                        }">
        @if ($city)
            <div id="MapContainer" class="flex-grow w-full flex flex-col rounded-xl"
                 wire:ignore
                 wire:key="map-{{ $city }}"
                 x-init="$nextTick(() => { initWidget(); })"
            >
                <div id="YMapsID" class="w-full h-[300px] md:h-[360px] rounded-xl"></div>
            </div>

        @endif

        @if ($addressPoint)
            <div>Выбранный ПВЗ: {{ $addressPoint }}</div>
            <div class="underline cursor-pointer" x-on:click='initWidget'>Сменить
                ПВЗ
            </div>
        @endif
    </div>
    @error('addressPoint')
    <span class="ml-[16px] text-[#CD0C0C] font-medium text-[11px]">Необходимо выбрать ПВЗ</span>
    @enderror
</div>
