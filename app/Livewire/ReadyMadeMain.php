<?php

namespace App\Livewire;

use App\Models\Look;
use App\Models\ReadyMade;
use Livewire\Component;

class ReadyMadeMain extends Component
{
    public $slug;
    public function render()
    {
        $looks = Look::query()
            ->when($this->slug, function ($query) {
                $query->whereNot('slug', $this->slug);
            })
            ->where('active', true)
            ->orderBy('position', 'asc')
            ->get();
        return view('livewire.ready-made-main', compact('looks'));
    }
}
