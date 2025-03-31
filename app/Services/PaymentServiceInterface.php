<?php

namespace App\Services;

interface PaymentServiceInterface
{
    public function initPayment(int $orderId, array $productItems = []);
}
