<div
    class="mb-5"
    id="{{ $type }}-add"
    x-data="{
                                addresses: [],
                                async changeInputAddress() {
                                    let input = document. querySelector('#{{ $type }}-address').value
{{--                                    let country = document.querySelector('#country').value--}}
                                    let country = 'RU';
                                    let city = document.querySelector('#cityInput').value

        var url = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address';
        var token = 'aaf90f1dd0eeda4e104d55b986f954a6b8d10ecf';
        city = city.replace('г ', '')
        var options = {
            method: 'POST',
            mode: 'cors',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Token ' + token
            },
            body: JSON.stringify({ query: input, from_bound: { value: 'street' }, to_bound: { value: 'house' }, restrict_value: true, locations: [{ country_iso_code: country, city: city }] })
        }

        fetch(url, options)
            .then(response => response.text())
            .then(result => this.addresses = Array.from(JSON.parse(result).suggestions))
            .catch(error => console.log('error', error));

    },
    selectAddress(value) {
        @this.selectAddress(value)
        this.addresses = [];
        let input = document. querySelector('#{{ $type }}-address');
        input.value = value;
    }
}"
>
    <div class="relative">
        <div class="w-full rounded-2xl border border-grey-200 bg-white h-12 px-4 py-1 text-sm focus-within:border-[1px] focus-within:border-black {{ $errors->has('address') ? 'error-field border-red' : '' }}">
            <input
                @keydown.enter.prevent
                x-on:input.debounce="changeInputAddress"
                wire:change="onChangeAddress($event.target.value)"
                wire:model.lazy="{{ $model }}"
                id="{{ $type }}-address"
                type="text"
                class="peer w-full text-sm bg-transparent border-[0px] p-0 translate-y-[18px] placeholder:text-transparent outline-none"
                placeholder=""
            />
            @if (count($addresses) > 0)
                <div
                    id="addressList"
                    class="absolute z-10 bg-white mt-1 w-full border rounded-md shadow-lg"
                >
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
                     class="absolute left-0 max-w-full md:max-w-[392px] top-[50px] z-10 bg-white mt-1 w-full
                 shadow-[0px_2px_8px_0px_rgba(170,170,170,0.50);] rounded-xl overflow-auto max-h-[200px]
                 [&::-webkit-scrollbar]:w-2
                  [&::-webkit-scrollbar-track]:bg-grey-100
                  [&::-webkit-scrollbar-thumb]:bg-grey-200
                "
                >
                    <template x-for="address in addresses">
                        <div class="px-4 py-2 hover:bg-grey-100 cursor-pointer border-b border-grey-100 last:border-e-0"
                             x-text="address.value"
                             x-on:click="selectAddress(address.value)"
                        >
                        </div>
                    </template>
                </div>
            </template>

            <label
                for="{{ $type }}-address"
                class="absolute top-0 pointer-events-none text-color-111 left-0 ml-4 translate-y-1 text-xs duration-100 ease-linear peer-placeholder-shown:translate-y-4 peer-focus:translate-y-1"
            >
                Адрес
            </label>
        </div>

        @error('dostavista')
        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
        @enderror
        @error('addressCdek')
        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
        @enderror
    </div>
</div>

