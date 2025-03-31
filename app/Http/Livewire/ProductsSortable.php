<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductsSortable extends Component
{
    public $products;
    public $categoriesAndProducts;

    public function mount()
    {
        $this->refreshProducts();
    }

    public function refreshProducts()
    {
        $products = Product::orderBy('sort')
            ->where('show', true)
            ->get();
        $this->products = $products;
        $this->categoriesAndProducts = [
            'Все товары' => $products
        ];
    }

    public function updateOrder($items)
    {
        // Начинаем с 1, чтобы избежать проблем с сортировкой по 0
        $order = 1;

        foreach ($items as $item) {
            Product::where('id', $item['value'])->update(['sort' => $order]);
            $order++;
        }

        // Обновляем список продуктов после сортировки
        $this->refreshProducts();

        $this->dispatch('sorted');
    }

    public function render()
    {
        return view('livewire.products-sortable');
    }
}
