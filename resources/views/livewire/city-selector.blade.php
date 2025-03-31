<div>
    <input wire:change="updateCities" wire:model.lazy="cityInput" id="cityInput" type="text" required autofocus class="form-input @error('cityInput') border-red text-red-900 placeholder-red focus:border-red focus:ring-red @enderror" />
    <div id="cityList" class="absolute z-10 bg-white mt-1 w-full border rounded-md shadow-lg">
        @foreach($cities as $city)
            <div class="px-3 py-2 hover:bg-gray-200 cursor-pointer" wire:click="selectCity('{{ $city['value'] }}')">
                {{ $city['label'] }}
            </div>
            <hr class="border-gray-200"

        @endforeach
    </div>
</div>
