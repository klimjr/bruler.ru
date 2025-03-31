<?php

namespace App\Livewire\Auth;

use App\Models\Order;
use App\Notifications\OrderConfirmed;
use App\Notifications\RecoveryCodeNotification;
use App\Notifications\WelcomeRegistration;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class Register extends Component
{
  /** @var string */
  public $name = '';

  /** @var string */
  public $last_name = '';

  public $birthday = '';

  /** @var string */
  public $phone = '+7';

  /** @var string */
  public $email = '';

  /** @var string */
  public $password = '';

  /** @var string */
  public $passwordConfirmation = '';

  /** @var string */
  public $code = '';

  /** @var string */
  public $verifyCode = '';

  public $user;

  public $allowedMails = ['@yandex.ru', '@mail.ru', '@inbox.ru', '@bk.ru', '@list.ru', '@internet.ru', '@vk.com', '@xmail.ru'];

  /** @var bool */
  public $isEmailCodeSend = false;

  public function register()
  {
    $this->validate([
      'name' => ['required'],
      'last_name' => ['required'],
      'birthday' => ['required'],
      'phone' => ['required', 'min:11'],
      'email' => ['required', 'email'],
      'password' => ['required', 'min:8'],
    ]);

    $checkUser = User::where('email', $this->email)->first();
    if ($checkUser)
      return $this->addError('email', 'Данная почта уже используется');
    if ($this->passwordConfirmation !== $this->password)
      return $this->addError('password', 'Указанные пароли не совпадают');

    $allowed = false;
    foreach ($this->allowedMails as $domain) {
      if (str_ends_with($this->email, $domain)) {
        $allowed = true;
        break;
      }
    }

    if (!$allowed) {
      return $this->addError('email', 'Используйте один из следующих почтовых доменов: ' . implode(', ', $this->allowedMails));
    }

    $this->user = [
      'name' => $this->name,
      'last_name' => $this->last_name,
      'birthday' => $this->birthday,
      'email' => $this->email,
      'phone' => $this->phone,
      'password' => Hash::make($this->password),
    ];

    $this->verifyCode = $this->generateCode();

    $notificationUser = new User();
    $notificationUser->email = $this->user['email'];
    $notificationUser->notify(new RecoveryCodeNotification($this->verifyCode));
    $this->isEmailCodeSend = true;

    return response()->json(['message' => 'Код был успешно отправлен'], 201);
  }

  public function checkCodeAndRegister()
  {

    $this->validate([
      'code' => ['required'],
    ]);

    if ($this->code !== $this->verifyCode)
      return $this->addError('code', 'Код не совпадает');
    $user = User::create($this->user);
    $user->email_verified_at = Carbon::now();
    event(new Registered($user));
    $user->notify(new WelcomeRegistration($user->name, $user->email));
    Auth::login($user, true);
    return redirect()->intended(route('home'));
  }

  public function reSendCode()
  {
    $this->verifyCode = $this->generateCode();
    $notificationUser = new User();
    $notificationUser->email = $this->user['email'];
    $notificationUser->notify(new RecoveryCodeNotification($this->verifyCode));
  }

  private function generateCode(int $length = 4): string
  {
    $characters = '0123456789';
    $shuffledCharacters = str_shuffle($characters);
    return substr($shuffledCharacters, 0, $length);
  }

  public function render()
  {
    return view('livewire.auth.register')->extends('layouts.app');
  }
}
