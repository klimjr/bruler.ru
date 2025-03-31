<?php

namespace App\Livewire;

use \App\Models\Slider as SliderModel;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Slider extends Component
{
  public $images = [];
  public $positions = [];

  public function mount() {
    $this->images();
  }

  public function images()
  {
    $slider = SliderModel::orderBy('position')->get(); 
    foreach ($slider as $image){

      $this->images[$image['position']] = $image->getImageUrlAttribute();
    }
    $this->render();
  }

  public function render()
  {
    return view('livewire.slider');
  }
}
