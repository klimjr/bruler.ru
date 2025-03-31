<div x-data="citySelect" class="relative">
    <div
        class="w-full rounded-2xl border border-grey-200 bg-white h-12 px-4 py-1 text-sm relative focus-within:border-[1px] focus-within:border-black {{ $errors->has('city') ? 'error-field border-red' : '' }}">
        <input
            @keydown.enter.prevent
            x-on:input.debounce="changeInputCity"
            wire:model.lazy="cityInput"
            id="cityInput"
            name="cityInput"
            type="text"
            class="peer w-full text-sm bg-transparent border-[0px] p-0 translate-y-[18px] placeholder:text-transparent outline-none"
            placeholder=""
        />


        @if (count($cities) > 0)
            <div id="cityList"
                 class="w-full rounded-2xl border border-grey-200 bg-white h-12 px-4 py-1 text-sm relative focus-within:border-[1px] focus-within:border-black {{ $errors->has('city') ? 'border-red' : '' }}">
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
                 class="absolute left-0 max-w-full md:max-w-[392px] top-[50px] z-10 bg-white mt-1 w-full
                 shadow-[0px_2px_8px_0px_rgba(170,170,170,0.50);] rounded-xl overflow-auto max-h-[200px]
                 [&::-webkit-scrollbar]:w-2
                  [&::-webkit-scrollbar-track]:bg-grey-100
                  [&::-webkit-scrollbar-thumb]:bg-grey-200
                "
            >
                <template x-for="city in cities">
                    <div class="px-4 py-2 hover:bg-grey-100 cursor-pointer border-b border-grey-100 last:border-e-0"
                         x-text="city.value"
                         x-on:click="selectCity(city.value, city.data.city ?? city.data.settlement, city.data.city_kladr_id ?? city.data.settlement_kladr_id, city.data.geo_lat, city.data.geo_lon)"
                    >
                    </div>
                </template>
            </div>
        </template>


        <label
            for="cityInput"
            class="absolute top-0 pointer-events-none text-color-111 left-0 ml-4 translate-y-1 text-xs duration-100 ease-linear peer-placeholder-shown:translate-y-4 peer-focus:translate-y-1"
        >
            Город
        </label>
        <input hidden id="geoLat" value="{{ $cityGeoLat }}" />
        <input hidden id="geoLon" value="{{ $cityGeoLon }}" />
    </div>

    @error('city')
    <span class="absolute left-4 bottom-[-18px] text-red text-[11px]">{{ $message }}</span>
    @enderror
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('citySelect', () => ({
            cities: [],
            openCiti: false,


            async changeInputCity() {
                let input = document.querySelector('#cityInput').value;
                // let country = document.querySelector('#country').value;
                let country = 'RU';
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
                    body: JSON.stringify({
                        query: input,
                        from_bound: {value: 'city'},
                        to_bound: {value: 'settlement'},
                        locations: [{country_iso_code: country}]
                    })
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
                        body: JSON.stringify({query: cityCode})
                    }

                    fetch(urlFindCode, options)
                        .then(response => response.text())
                        .then(result => {
                            @this.
                            selectCity(value, JSON.parse(result).suggestions[0].data.cdek_id, geoLat, geoLon)
                        })
                        .catch(error => console.log('error', error));
                }

                this.cities = []
                let input = document.querySelector('#cityInput')
                input.value = cityName
            }
        }));
    });
</script>


