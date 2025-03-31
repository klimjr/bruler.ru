<?php

namespace App\Livewire;

use App\Models\MainPage;
use Livewire\Component;

class MainProducts extends Component
{
    public $main;
    public $products;
    public function mount()
    {
        $this->main = MainPage::first();
        $this->products = \App\Models\Product::query()
            ->where('show', true)
            ->whereIn('id', $this->main->products)
            ->orderByRaw(sprintf("FIELD(id, %s)", implode(',', $this->main->products)))
            ->get();
    }

    public function render()
    {
        return view('livewire.main-products');
    }
}
