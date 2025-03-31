<?php

namespace App\Livewire;

use App\Models\Favourite;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Fire extends Component
{
    public Product $product;
    public string $classes;
    public bool $isFavourite;
    public bool $isActive;
    public $buttonClasses = '';

    public function mount(Product $product, string $buttonClasses = '')
    {
        $this->product = $product;
        $this->$buttonClasses = $buttonClasses;
        $this->checkFavouriteStatus();
    }

    private function checkFavouriteStatus()
    {
        $user = Auth::user();

        if ($user) {
            $this->isFavourite = $this->product->favouritesBy()->where('user_id', $user->id)->exists();
        } else {
            $favourites = session()->get('favourites', []);
            $this->isFavourite = in_array($this->product->id, $favourites);
        }
    }

    public function setFavouriteProduct()
    {
        $user = Auth::user();

        if ($user) {
            $existingFavourite = $this->product->favourites->where('user_id', $user->id)->first();
            if ($existingFavourite) {
                $existingFavourite->delete();
                $this->isFavourite = false;
            } else {
                $favourite = Favourite::firstOrNew(['user_id' => $user->id, 'product_id' => $this->product->id]);
                $favourite->save();
                $this->isFavourite = true;
            }
        } else {
            $favourites = session()->get('favourites', []);
            if (in_array($this->product->id, $favourites)) {
                $favourites = array_diff($favourites, [$this->product->id]);
                $this->isFavourite = false;
            } else {
                $favourites[] = $this->product->id;
                $this->isFavourite = true;
            }
            session()->put('favourites', $favourites);
        }

        $this->dispatch('favouritesUpdated');
    }

    public function updateFavouriteStatus()
    {
        $this->dispatch('favouritesUpdated', $this->product->id, $this->isFavourite);
    }

    public function getButtonClasses($isActive, $isFavourite)
    {
        $baseClasses = $this->buttonClasses;
        $visibilityClass = $isActive || $isFavourite ? '' : 'md:hidden';
        return $baseClasses . ' ' . $visibilityClass;
    }



    public function render()
    {
        return view('livewire.fire');
    }
}
