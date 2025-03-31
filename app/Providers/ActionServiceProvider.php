<?php
namespace App\Providers;

use App\Models\Action;
use Cache;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Загружаем все активные акции при старте
        $actions = Cache::rememberForever('actions', function () {
            return Action::where('is_active', true)
                ->get()
                ->map(function ($action) {
                    return [
                        'id' => $action->id,
                        'name' => $action->name,
                        'badge' => $action->badge,
                        'badge_color' => $action->badge_color,
                        'all_products' => $action->all_products,
                        'products_include' => $action->products_include_ids ?? [],
                        'products_exclude' => $action->products_exclude_ids ?? [],
                        'products_related' => $action->products_related_ids ?? [],
                        'discount_amount' => $action->discount_amount,
                        'discount_type' => $action->discount_type,
                    ];
                });
        });

        app()->singleton('actions', function () use ($actions) {
            return $actions;
        });
    }
}
