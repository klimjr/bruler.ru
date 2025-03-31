<?php

namespace App\Livewire;

use App\Models\ProductVariant;
use App\Models\RunningText;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class Product extends Component
{
    public \App\Models\Product $product;

    public $key = '';
    public $indicatorColor = '#53CD3B';
    public $indicatorText = 'Есть в наличии';
    public $color = null;
    public $certificate = null;
    public $selectedVariantArticle = null;
    public $certificate_params = null;

    public $price = null;
    public $gallery = null;
    public $selectedImgIndex = 0;
    public $isRunningText = false;

    public $description = null;

    public $alt = 'slider_image';

    #[On('productColorUpdated')]
    public function onProductColorUpdated($color)
    {
        $this->color = $color;

        $this->gallery = [];

        foreach ($this->product->gallery as $g) {
            if ($g['color_id'] == $this->color) {
                $this->gallery = array_map(function ($item) {
                    return [
                        'image' => asset('storage/' . $item['image']),
                        'alt' => $item['alt']
                    ];
                }, $g['images']);
                $this->description = nl2br(e($g['description'])) ?? $this->product->description;
                $this->alt = $g['alt'] ?? 'slider_image';
                break;
            }
        }
        $this->selectedImgIndex = 0;
    }

    #[On('productVariantUpdated')]
    public function onProductVariantUpdate($productVariant)
    {
        $db_product_variant = ProductVariant::find($productVariant);
        $this->selectedVariantArticle = $db_product_variant->article;

        if ($db_product_variant->amount != 0 && $db_product_variant->amount < 5) {
            $this->indicatorColor = '#FF3D2F';
            $this->indicatorText = 'Осталось несколько экземпляров';
        } else {
            $this->indicatorColor = '#53CD3B';
            $this->indicatorText = 'Есть в наличии';
        }
    }

    #[On('productCertificateUpdated')]
    public function onProductCertificateUpdated($cert)
    {
        $this->certificate = $cert;
        $this->price = $cert['price'];
    }

    public function incrementSelectedImageIndex()
    {
        $index = ($this->selectedImgIndex >= count($this->gallery) - 1) ? 0 : $this->selectedImgIndex + 1;
        $this->selectedImgIndex = $index;
    }

    public function updateSelectedImageIndex($index)
    {
        $this->selectedImgIndex = $index;
    }

    public function decrementSelectedImageIndex()
    {
        $index = ($this->selectedImgIndex <= 0) ? count($this->gallery) - 1 : $this->selectedImgIndex - 1;
        $this->selectedImgIndex = $index;
    }

    public function mount()
    {
        $sumProductAmount = 0;
        foreach ($this->product->variants as $variant) {
            $sumProductAmount += $variant->amount;
        }

        if ($sumProductAmount == 0) {
            $this->indicatorColor = '#00000080';
            $this->indicatorText = 'Sold out';
        }

        $runningTexts = RunningText::all();
        if (count($runningTexts) >= 1)
            $this->isRunningText = true;

        switch ($this->product->type) {
            case \App\Models\Product::TYPE_PRODUCT:
            case \App\Models\Product::TYPE_SET:
                $colors = $this->product->availableColors();
                $this->price = $this->product->price;
                $this->gallery = [];
                $this->key = rand();
                $this->color = $colors->first()->id;
                foreach ($this->product->gallery as $g) {
                    if ($g['color_id'] == $this->color) {
                        $this->gallery = array_map(function ($item) {
                            return [
                                'image' => asset('storage/' . $item['image']),
                                'alt' => $item['alt']
                            ];
                        }, $g['images']);
                        $this->description = nl2br(e($g['description'])) ?? $this->product->description;
                        $this->alt = $g['alt'] ?? 'slider_image';

                        break;
                    }
                }
                break;
            case \App\Models\Product::TYPE_CERTIFICATE:
                if (count($this->product->certificate_params) >= 1) {
                    $this->certificate_params = array_map(function ($cert) {
                        return [
                            'image' => asset('storage/' . $cert['image']),
                            'price' => $cert['price'],
                            'alt' => $cert['alt']
                        ];
                    }, $this->product->certificate_params);
                }
                $this->certificate = $this->certificate_params[0] ?? [];
                $this->price = $this->certificate['price'];
                $this->description = nl2br(e($this->product->description));
                $this->alt = 'slider_image';

                break;
        }
    }

    public function render()
    {
        return view('livewire.product', ['wireKey' => $this->key]);
    }
}
