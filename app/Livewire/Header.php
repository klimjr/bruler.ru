<?php

namespace App\Livewire;

use App\Models\RunningText;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Header extends Component
{
    public $productCount = 0;
    public $favoritesCount = 0;

    public $runningTexts = [];

    public function mount()
    {
        $runningTexts = RunningText::first();
        if (!empty($runningTexts))
            $this->runningTexts = $runningTexts;
        $this->onCartUpdate();
    }

    #[On('cartUpdated')]
    #[On('totalCartUpdated')]
    public function onCartUpdate()
    {
        $this->productCount = 0;
        foreach (session()->get('cart', []) as $product) {
            $this->productCount += $product['quantity'] ?? 0;
        }

        if (Auth::check()) {
            $this->favoritesCount = Auth::user()->favourites()->count();
        } else {
            $favourites = session()->get('favourites', []);
            $this->favoritesCount = count($favourites) ?? 0;
        }
        $this->render();
    }

    #[On('favouritesUpdated')]
    public function favouritesUpdated()
    {
        if (Auth::check()) {
            $this->favoritesCount = Auth::user()->favourites()->count();
        } else {
            $favourites = session()->get('favourites', []);
            $this->favoritesCount = count($favourites);
        }
        $this->render();
    }

    public function render()
    {
        return view('livewire.header');
    }
}
