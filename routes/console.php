<?php

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

Artisan::command('points:december-purge', function () {
    DB::transaction(function () {
        // Получаем заказы, где пользователи воспользовались бонусом
        $orders = Order::query()
            ->whereNotNull('user_id')
            ->where('use_bonus', 1)
            ->whereBetween('created_at', [
                new Carbon('2024-12-24'),
                Carbon::now(),
            ])
            ->get()
            ->unique('user_id')
            ->pluck('user_id', 'id');


        // Получаем ID пользователей, которые воспользовались бонусом
        $userIdsWithBonus = $orders->values()->toArray();

        // Находим пользователей, которые НЕ воспользовались бонусом,
        // у которых баланс >= 1500 и которые зарегистрировались до 2024-12-24
        $usersWithoutBonus = User::query()
            ->whereNotIn('id', $userIdsWithBonus)
            ->where('points', '>=', 1500)
            ->where('created_at', '<=', new Carbon('2024-12-24'))
            ->get();

        // Вычитаем 1500 баллов у отфильтрованных пользователей
        foreach ($usersWithoutBonus as $user) {
            Log::channel('points')
                ->info('Points removal operation', ['operation' => 'remove_points-1500', 'user_id' => $user->id]);
            $user->points -= 1500;
            $user->save();
        }
    });
})->describe('Remove points from users who did not make an order in December');

