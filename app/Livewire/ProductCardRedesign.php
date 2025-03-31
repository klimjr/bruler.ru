<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Livewire\AddToCart;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductCardRedesign extends Component
{
    public \App\Models\Product $product;
    public $url = '';
    public $image = '';
    public $image_back = '';
    public $available_colors = [];
    public $gallery = [];
    public $selectedColor;
    public $initImage = false;
    public $sizes = [];
    public $selectedVariant = null;
    public $isNew = false;
    public $isSoldOut = false;
    public $isPreorder = false;
    public $sizeContainer = 'md:h-[31vw]';
    public $sizeLink = 'w-full h-[240px] md:h-full';
    public $isActive = false;
    public $isFavourite = false;

    protected $listeners = ['favouriteStatusUpdated' => 'updateFavouriteStatus'];

    public function mount($product)
    {
        $this->getSizesInfo();

        $this->url = $this->product->getRouteUrl();
        $this->image_back = $this->product->back_img ? asset('storage/' . $this->product->back_img) : '';

        switch ($this->product->type) {
            case \App\Models\Product::TYPE_PRODUCT:
                $this->available_colors = $this->product->availableColors();

                if ($this->available_colors->isNotEmpty() && $this->available_colors->first() !== null) {
                    $this->selectedColor = $this->selectedColor ?? $this->available_colors->first()->id;
                } else {
                    $this->selectedColor = $this->selectedColor ?? 1;
                }

                $this->isSoldOut = $this->isSoldOutFn();
                $this->isPreorder = $this->product->preorder;
                $this->isNew = $this->product->new;

                if (!$this->initImage) {
                    $this->image = $this->product->getImageUrlAttribute();
                    $this->initImage = true;
                }
                break;
            case \App\Models\Product::TYPE_CERTIFICATE:
            case \App\Models\Product::TYPE_SET:
                $this->image = $this->product->getImageUrlAttribute();
                break;
        }

        $this->product = $product;
        $this->checkFavouriteStatus();
    }

    private function checkFavouriteStatus()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->isFavourite = $this->product->favouritesBy()->where('user_id', $user->id)->exists();
        } else {
            $favourites = session()->get('favourites', []);
            $this->isFavourite = in_array($this->product->id, $favourites);
        }
    }

    #[On('favouritesUpdated')]
    public function updateFavouriteStatus($productId = null, $isFavourite = null)
    {
        if ($productId === null || $isFavourite === null) {
            $this->checkFavouriteStatus();
        } elseif ($this->product->id === $productId) {
            $this->isFavourite = $isFavourite;
        }
    }

    public function selectSize($id)
    {
        $this->selectedVariant = $id;
    }

    public function addToCart()
    {
        if (!$this->selectedVariant) {
            return;
        }

        $addToCart = new AddToCart;
        $addToCart->handleAddToCart([
            'product_id' => $this->product->id,
            'variant_id' => $this->selectedVariant,
            'quantity' => 1
        ]);
        $this->sizes[$this->selectedVariant]['inCart'] = true;
        $this->dispatch('cartUpdated');
    }

    public function isSoldOutFn(): bool
    {
        $variants = $this->product->variants()->get();
        $counter = $variants->sum('amount');
        return $counter < 1;
    }

    public function getSizesInfo()
    {
        $cart = session()->get('cart', []);
        $variants = $this->product->variants()->get();

        foreach ($variants as $variant) {
            $sizeInfo = $variant->size()->first();

            $inCart = !empty(array_filter($cart, function ($item) use ($variant) {
                if (isset($item['variant'])) {
                    return $item['variant'] == $variant->id;
                }
            }));

            $this->sizes[$variant->id] = [
                'name' => $sizeInfo->name,
                'available' => ($variant->amount > 0),
                'inCart' => $inCart
            ];
        }
    }

    public function selectColor(int $color)
    {
        $this->selectedColor = $color;
        $this->initImage = false;
        foreach ($this->product->gallery as $value) {
            if ($this->initImage)
                break;
            if ($value['color_id'] == $this->selectedColor) {
                $image = count($value['images']) >= 1 ? asset('storage/' . $value['images'][0]) : $this->product->getImageUrlAttribute();
                $this->image = $image;
                $this->initImage = true;
            }
        }
        if (!$this->initImage) {
            $this->image = $this->product->getImageUrlAttribute();
            $this->initImage = true;
        }
    }

    public function render()
    {
        return view('livewire.product-card-redesign');
    }
}
