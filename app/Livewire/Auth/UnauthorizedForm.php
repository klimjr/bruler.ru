<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UnauthorizedForm extends Component
{
    /** @var string */
    public $email = '';

    /** @var string */
    public $password = '';

    /** @var bool */
    public $remember = false;
    public $back_url = null;
    public $itsOrder = false;

    protected $rules = [
        'email' => ['required', 'email'],
        'password' => ['required'],
    ];

    public function mount(Request $request)
    {
        $this->back_url = $request->query('back_url');
    }

    public function authenticate()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8']
        ]);

        $authUser = User::where('email', $this->email)->first();

        if ($authUser && is_null($authUser->password))
            return $this->addError('password', 'У данного пользователя нет пароля');

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Неверная почта или пароль');
            return;
        }

        $back_url = $this->back_url ?? route('home');

        return redirect($back_url);
    }

    public function render()
    {
        return view('livewire.auth.unauthorized-form')->extends('layouts.account');
    }
}
