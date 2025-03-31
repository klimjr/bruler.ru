<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Product;
use App\Models\Collection;

class CollectionProducts extends Component
{
    public $collectionInfo;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($collection)
    {
        $this->collectionInfo = Collection::where('id', $collection)->first();
        $products = Product::where('collection', $collection)
            ->limit(6)
            ->get()
            ->sortBy('position', SORT_NATURAL | SORT_FLAG_CASE);

        if ($products) {
            $this->collectionInfo->products = $products;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        // Дополнительная обработка переменной $collection, если необходимо
        return view('components.collection-products');
    }
}
