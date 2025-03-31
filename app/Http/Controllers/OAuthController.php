<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function redirectToVK(Request $request) {
        return Socialite::driver('vkontakte')->redirect();
    }

    public function handleVK(Request $request) {
        $userVK = Socialite::driver('vkontakte')->stateless()->user();
        $user = User::firstOrCreate(['vk_id' => $userVK->id], [
            'name' => $userVK->user['first_name'],
            'last_name' => $userVK->user['last_name'],
            'email' => $userVK->email,
            'vk_id' => $userVK->id
        ]);
        $user->save();
        auth()->login($user);
        return redirect("/profile");
    }

    public function redirectToTelegram(Request $request) {
        return Socialite::driver('telegram')->redirect();
    }

    public function handleTelegram(Request $request) {
        $userTelegram = Socialite::driver('telegram')->stateless()->user();
        $user = User::firstOrCreate(['telegram_id' => $userTelegram->id], [
            'name' => $userTelegram->user['first_name'],
            'telegram_id' => $userTelegram->id
        ]);
        $user->save();
        auth()->login($user);
        return redirect("/profile");
    }

    public function redirectToYandex(Request $request) {
        return Socialite::driver('yandex')->redirect();
    }

    public function handleYandex(Request $request) {
        $userYandex = Socialite::driver('yandex')->stateless()->user();
        $user = User::firstOrCreate(['yandex_id' => $userYandex->id], [
            'name' => $userYandex->name,
            //'email' => $userYandex->email,
            'yandex_id' => $userYandex->id
        ]);
        $user->save();
        auth()->login($user);
        return redirect("/profile");
    }
}
