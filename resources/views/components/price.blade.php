@props(['price', 'minus' => false, 'plus' => false, 'round' => false])
@php
    $plus = $plus ? '+' : '';
@endphp
@if($price < 0 || $minus)
<div class="text-red">-{{ price($price, $round) }} ₽</div>
@else
<div>{{ $plus }}{{ price($price, $round) }} ₽</div>
@endif
