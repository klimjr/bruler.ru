<?php

namespace App\Livewire;

use App\Http\Controllers\CDEKController;
use Livewire\Component;

class CitySelector extends Component
{
    public $country;
    public $city = '';
    public $cities = [];
    public $cityInput = '';
    public function updateCities()
    {
        $cities = [];
        foreach ((array)((new CDEKController())->getCities($this->cityInput, $this->country)) as $city) {
            \Log::info(json_encode($city));
            $cities[] = ['label' => $city->city, 'value' => $city->city];
        }
        $this->cities = $cities;
    }

    public function selectCity($city)
    {
        $this->city = $city;
    }

    public function render()
    {
        return view('livewire.city-selector');
    }

}
