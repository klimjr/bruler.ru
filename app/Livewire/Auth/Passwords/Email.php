<?php

namespace App\Livewire\Auth\Passwords;

use App\Models\User;
use App\Notifications\ChangePasswordCodeNotification;
use App\Notifications\RecoveryCodeNotification;
use App\Notifications\WelcomeRegistration;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Password;

class Email extends Component
{
    /** @var string */
    public $email;

    public $password;
    public $passwordConfirmation;

    /** @var string */
    public $code = '';

    /** @var string */
    public $verifyCode = '';

    public $isResetPasswordCodeSend = false;

    public function sendResetPasswordCode()
    {
        $this->validate([
            'email' => ['required', 'email'],
            // 'password' => ['required', 'min:8'],
        ]);
        // if ($this->passwordConfirmation !== $this->password) return $this->addError('password', 'Указанные пароли не совпадают');

        $user = User::where('email', $this->email)->first();
        if (is_null($user))
            return $this->addError('email', 'Пользователь не найден');

        $this->verifyCode = $this->generateCode();

        $notificationUser = new User();
        $notificationUser->email = $this->email;
        $notificationUser->notify(new ChangePasswordCodeNotification($this->verifyCode));
        $this->isResetPasswordCodeSend = true;

        return response()->json(['message' => 'Код был успешно отправлен'], 201);

    }

    private function generateCode(int $length = 4): string
    {
        $characters = '0123456789';
        $shuffledCharacters = str_shuffle($characters);
        return substr($shuffledCharacters, 0, $length);
    }

    public function reSendCode()
    {
        $this->verifyCode = $this->generateCode();
        $notificationUser = new User();
        $notificationUser->email = $this->email;
        $notificationUser->notify(new ChangePasswordCodeNotification($this->verifyCode));
    }

    public function checkCodeAndLogin()
    {
        $this->validate([
            'code' => ['required'],
        ]);

        if ($this->code !== $this->verifyCode)
            return $this->addError('code', 'Код не совпадает');
        $user = User::where('email', $this->email)->first();
        $user->password = Hash::make($this->password);
        $user->setRememberToken(Str::random(60));
        $user->save();
        Auth::login($user, true);
        return redirect()->intended(route('home'));
    }

    public function confirmChangePassoword()
    {
        if ($this->code !== $this->verifyCode)
            return $this->addError('code', 'Код не совпадает');
    }

    public function render()
    {
        return view('livewire.auth.passwords.email')->extends('layouts.app');
    }
}
