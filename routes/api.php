<?php

use App\Http\Controllers\CDEKController;
use App\Http\Controllers\DolyamiController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\TinkoffController;
use App\Http\Controllers\ProductAmountController;
use App\Http\Controllers\YandexPayController;
use App\Http\Controllers\YandexPayControllerNew;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('cdek/widget', function () {
    require __DIR__ . '/../app/CDEK/service.php';
})->name('cdek.widget');


Route::get('/cdek/cities', [CDEKController::class, 'getCitiesRequest']);

Route::post('/tinkoff/webhook', [TinkoffController::class, 'handle'])->name('tinkoff.webhook');
Route::post('/dolyami/webhook', [DolyamiController::class, 'handle'])->name('dolyami.webhook');
//Route::post('/yandex/v1/webhook', [YandexPayControllerNew::class, 'handle'])->name('yandex.webhook'); // Not used

Route::any('/optimize-images', function () {
    Artisan::call('app:optimize-images');
});

Route::group(['prefix' => 'oauth'], function () {
    Route::get('vk', [OAuthController::class, 'redirectToVK'])->middleware('web');
    Route::get('vk/handle', [OAuthController::class, 'handleVK'])->middleware('web');
    Route::get('telegram', [OAuthController::class, 'redirectToTelegram'])->middleware('web');
    Route::get('telegram/handle', [OAuthController::class, 'handleTelegram'])->middleware('web');
    Route::get('yandex', [OAuthController::class, 'redirectToYandex'])->middleware('web');
    Route::get('yandex/handle', [OAuthController::class, 'handleYandex'])->middleware('web');
});


Route::prefix('1c')->group(function () {

    Route::middleware('guest:sanctum')
        ->get('/orders', \App\Http\Controllers\Api\OrdersController::class);

    Route::get('/order/{id}', [\App\Http\Controllers\OrderController::class, 'index']);
});

Route::post('/products/update-amount', [ProductAmountController::class, 'updateAmount']);
