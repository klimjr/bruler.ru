<?php

if (!function_exists('price')) {
    function price($price, $round = true)
    {
        $decimals = $round ? 0 : 2;
        return number_format($price, $decimals, '.', ' ');
    }
}

if (!function_exists('numberToPrice')) {
    function numberToPrice($amount): string
    {
        return number_format((double)$amount, 2, '.', '');
    }
}
