<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class CategorySelect extends Component
{
    public $categories;
    public function mount()
    {
        $this->categories = Category::all();
    }

    public function render()
    {
        return view('livewire.category-select');
    }
}
