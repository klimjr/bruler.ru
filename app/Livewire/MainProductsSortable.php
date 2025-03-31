<?php

namespace App\Livewire;

use App\Models\MainPage;
use Livewire\Component;

class MainProductsSortable extends Component
{
    public $products;

    public function mount()
    {
        $this->refreshProducts();
    }

    public function refreshProducts()
    {
        $main_products = MainPage::first()->products;
       $products = \App\Models\Product::query()
            ->where('show', true)
            ->whereIn('id', $main_products)
            ->orderByRaw(sprintf("FIELD(id, %s)", implode(',', $main_products)))
            ->get();
        $this->products = $products;
    }

    public function updateOrder($items)
    {
        $orderedIds = collect($items)->pluck('value')->toArray();

        $mainPage = MainPage::first();
        $mainPage->products = $orderedIds;
        $mainPage->save();

        $this->refreshProducts();

        $this->dispatch('sorted');
    }

    public function render()
    {
        return view('livewire.main-products-sortable');
    }
}
