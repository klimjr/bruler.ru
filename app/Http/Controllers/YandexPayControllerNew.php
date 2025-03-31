<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\YandexPayService;
use App\Models\Order;

class YandexPayControllerNew extends Controller
{
    protected YandexPayService $yandexPayService;

    public function __construct(YandexPayService $yandexPayService)
    {
        $this->yandexPayService = $yandexPayService;
    }

    /**
     * Инициация оплаты
     */
    public function pay(Order $order)
    {
        $result = $this->yandexPayService->createOrder($order);

        if ($result['status'] === 'success' && isset($result['payment_url'])) {
            return redirect($result['payment_url']);
        }

        return response()->json(['error' => $result['message']], 400);
    }

    public function handle(Request $request)
    {
        \Log::driver('yandex')->info(request()->all());
    }

    /**
     * Обработка вебхука
     */
    public function webhook(Request $request)
    {
        return $this->yandexPayService->handleWebhook($request->all());
    }

    public function testpay($id)
    {
        if (config('app.env') !== 'local') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $order = Order::findOrFail($id);
        return redirect()->route('order.pay', ['order' => $order->id]);
    }


}
