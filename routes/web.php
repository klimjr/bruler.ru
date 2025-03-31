<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\LookController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TinkoffController;
use App\Http\Controllers\XmlController;
use App\Http\Controllers\YandexPayControllerNew;
use App\Livewire\Auth\UnauthorizedForm;
use App\Livewire\Auth\Passwords\Confirm;
use App\Livewire\Auth\Passwords\Email;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Verify;
use App\Livewire\Order;
use App\Livewire\Profile\Payments;
use App\Livewire\Profile\Profile;
use App\Livewire\SuccessOrder;
use App\Models\Promocode;
use App\Models\User;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReadyFashionController;

if (env('MAINTENANCE_MODE', false)) {
    Route::get('/{any}', function () {
        return view('layouts.maintenance');
    })->where('any', '.*');
}
include __DIR__ . '/test.php';
Route::post('/v1/webhook', [YandexPayControllerNew::class, 'handle'])->name('yandex.webhook');
Route::get('/update_feed', function () {
    $xml = new XmlController();
    $xml->generateXml();
    $xml->generateXml(true);
    return ['ok'];
});
Route::get('/add-points-to-all/{points}/{key}', function($points, $key) {
    try {
        $validKey = env('POINTS_ACCESS_KEY', 'Bruler2332');

        if ($key !== $validKey) {
            throw new \Exception('Invalid access key');
        }

        if (!is_numeric($points) || $points < 1) {
            throw new \Exception('Points must be a positive number');
        }

        $startTime = now();
        $userCount = User::count();

        // Добавление баллов
        User::query()->update([
            'points' => DB::raw("points + $points")
        ]);

        // Логирование в отдельный файл
        Log::channel('points')->info('Points addition operation', [
            'operation' => 'add_points_to_all',
            'points_added' => $points,
            'total_users' => $userCount,
            'start_time' => $startTime->format('Y-m-d H:i:s'),
            'end_time' => now()->format('Y-m-d H:i:s'),
            'execution_time' => now()->diffInSeconds($startTime) . ' seconds',
            'ip_address' => request()->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Added $points points to all users",
            'total_users' => $userCount
        ]);

    } catch (\Exception $e) {
        Log::channel('points')->error('Points operation failed', [
            'error' => $e->getMessage(),
            'points_attempted' => $points,
            'ip_address' => request()->ip()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/sync/{key}', function ($key) {
    if ($key !== env('SYNC_SECRET_KEY')) {
        abort(403);
    }

    $output = shell_exec(base_path() . '/sync.sh 2>&1');
    return response($output);
})->middleware('throttle:3,1');

Route::get('/tmp/check_mail', function () {
    try {
        Mail::raw('Hello world', function (Message $message) {
            $message->to(config('bruler.email'))->from('admin@bruler.ru');
        });
        return response()->json(['message' => 'Ok'], 200);
    } catch (\Exception $e) {
        // Handle the exception or log it
        return response()->json(['message' => 'Error'], 500);
    }
});

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/ready-fashion', [ReadyFashionController::class, 'index'])->name('ready-fashion');

Route::view('/contacts', 'contacts')->name('contacts');
Route::view('/delivery', 'delivery')->name('delivery');
Route::view('/payment', 'payment')->name('payment');
Route::view('/refund', 'refund')->name('refund');
Route::view('/preorder', 'preorder')->name('preorder');
Route::view('/about_brand', 'about_brand')->name('about_brand');
Route::view('/documents', 'documents')->name('documents');
Route::view('/failed_order', 'failed_order')->name('failed_order');
Route::view('/loyalty', 'loyalty')->name('loyalty');
Route::get('/kit/{slug?}', [ProductController::class, 'getKit'])->name('kit.show');
Route::get('/catalog', [ProductController::class, 'catalog'])->name('catalog');
Route::redirect('/collection', '/collection/filter');
Route::redirect('/collection/all', '/collection/filter')->name('collection.all');
// Route::get('/collection/all', [ProductController::class, 'getCollectionsAndProducts'])->name('collection.all');
Route::get('/collection/filter', [ProductController::class, 'filterProducts'])->name('collection.filter');
Route::get('/collection/{collection}', [ProductController::class, 'getCategoriesAndProducts'])->name('collection.show');

//Route::view('/order', 'order')->name('order');
Route::get('order', Order::class)
    ->name('order');

Route::get('profile', Profile::class)
    ->name('profile');

Route::get('login', UnauthorizedForm::class)
    ->name('login');

Route::get('register', Register::class)
    ->name('register');

Route::get('profile/password-reset', Email::class)
    ->name('password-reset');

Route::get('success_order', SuccessOrder::class)
    ->name('success_order');

//Route::get('profile/password-reset', Reset::class)
//    ->name('password-reset');

Route::middleware('auth')->group(function () {
    Route::get('email/verify', Verify::class)
        ->middleware('throttle:6,1')
        ->name('verification.notice');

    Route::get('profile/payments', Payments::class)
        ->name('payments');

    Route::get('password/confirm', Confirm::class)
        ->name('password.confirm');

    Route::get('/profile/orders', [AccountController::class, 'getOrders'])->name('profile.orders');
    Route::get('/profile/orders/{order}', [AccountController::class, 'getOrder'])->name('profile.orders.order');
});

Route::middleware('auth')->group(function () {
    Route::get('email/verify/{id}/{hash}', EmailVerificationController::class)
        ->middleware('signed')
        ->name('verification.verify');

    Route::any('logout', LogoutController::class)
        ->name('logout');
});

Route::get('/look/{slug}', [LookController::class, 'index'])->name('look');

Route::get('/profile/favourites', [AccountController::class, 'getFavourites'])->name('profile.favourites');

Route::get('/{category:slug}', [ProductController::class, 'list'])->name('products');
Route::get('/{category:slug}/{product:slug}', [ProductController::class, 'viewProduct'])->name('product');
