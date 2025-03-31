<?php

namespace App\Livewire\Profile;

use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Payments extends Component
{
  /** @var string */
  public $name = '';

  /** @var string */
  public $last_name = '';

  /** @var string */
  public $phone = '';

  /** @var string */
  public $email = '';

  /** @var string */
  public $city = '';

  /** @var string */
  public $birthday = '';

  public function save()
  {
    $this->validate([
      'name' => ['required'],
      'last_name' => ['required'],
      'phone' => ['required', 'integer','digits:11'],
      'city' => ['required'],
      'birthday' => ['required'],
      'email' => ['required', 'email'],
    ]);

    $user = Auth::user();

    $user->update([
      'email' => $this->email,
      'city' => $this->city,
      'birthday' => $this->birthday,
      'name' => $this->name,
      'last_name' => $this->last_name,
      'phone' => $this->phone,
    ]);
  }

  public function mount()
  {
    if (Auth::user()) {
      $this->name = Auth::user()->name;
      $this->last_name = Auth::user()->last_name;
      $this->phone = Auth::user()->phone;
      $this->city = Auth::user()->city;
      $this->birthday = Auth::user()->birthday;
      $this->email = Auth::user()->email;
    }
  }

  public function render()
  {
    return view('profile.payments')->extends('layouts.account');
  }
}
