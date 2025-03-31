<?php

namespace App\Livewire;

use App\Models\MainPage;
use Livewire\Component;
use Carbon\Carbon;

class Hero extends Component
{
    public $main;
    public $timerTimestamp;
    public function mount()
    {
        $this->main = MainPage::first();
        $this->timerTimestamp = $this->main->timer ? Carbon::parse($this->main->timer)->getTimestamp() : null;
    }

    public function render()
    {
        return view('livewire.hero');
    }
}
