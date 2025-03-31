<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Newsletter;
use Illuminate\Validation\ValidationException;

class Footer extends Component
{
    public $email;

    protected $rules = [
        'email' => 'required|email|unique:newsletters,email'
    ];

    public function subscribe()
    {
        $this->validate();
        Newsletter::create(['email' => $this->email]);
        $this->reset('email');
        session()->flash('message', 'Спасибо за подписку!');
    }

    public function render()
    {
        return view('livewire.footer');
    }
}
