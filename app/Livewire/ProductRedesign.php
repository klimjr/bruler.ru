<?php

namespace App\Livewire;

use App\Models\ProductVariant;
use App\Models\RunningText;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProductRedesign extends Component
{
    private const INDICATOR_COLOR_SOLD_OUT = '#5A5A5A';
    private const INDICATOR_BG_SOLD_OUT = '#F7F7F7';
    private const INDICATOR_COLOR_LOW_STOCK = '#FFA800';
    private const INDICATOR_BG_LOW_STOCK = '#FFEFCF';
    private const INDICATOR_COLOR_IN_STOCK = '#16945E';
    private const INDICATOR_BG_IN_STOCK = '#DAF3EA';

    public \App\Models\Product $product;

    public $key = '';
    public $indicatorColor = self::INDICATOR_COLOR_SOLD_OUT;
    public $indicatorBgColor = self::INDICATOR_BG_SOLD_OUT;
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

    private function updateIndicator($amount)
    {
        if ($amount == 0) {
            $this->indicatorBgColor = self::INDICATOR_BG_SOLD_OUT;
            $this->indicatorColor = self::INDICATOR_COLOR_SOLD_OUT;
            $this->indicatorText = 'Нет в наличии';
        } elseif ($amount < 5) {
            $this->indicatorBgColor = self::INDICATOR_BG_LOW_STOCK;
            $this->indicatorColor = self::INDICATOR_COLOR_LOW_STOCK;
            $this->indicatorText = 'Осталось несколько экземпляров';
        } else {
            $this->indicatorBgColor = self::INDICATOR_BG_IN_STOCK;
            $this->indicatorColor = self::INDICATOR_COLOR_IN_STOCK;
            $this->indicatorText = 'Есть в наличии';
        }
    }

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

        $this->updateIndicator($db_product_variant->amount);
    }

    #[On('productCertificateUpdated')]
    public function onProductCertificateUpdated($cert)
    {
        $this->certificate = $cert;
        $this->price = $this->certificate['price'];
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

        $this->updateIndicator($sumProductAmount);

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
        return view('livewire.product-redesign', ['wireKey' => $this->key]);
    }
}
