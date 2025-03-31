@php
    $bonus = session()->get('bonus');
    $useBonus = session()->get('useBonus');
@endphp
@if ((auth()->check() && $totalCart > 4000) || $useBonus)
    <div class="flex justify-between pb-4 border-b border-grey-200">
        <div class="flex">
            @if($useBonus)
                <span class="mr-1"> Списанные бонусы:</span>
                <x-price price="{{ $bonus }}"/>
            @else
                <span class="mr-1">Всего бонусов:</span>
                <x-price price="{{ $points }}"/>
            @endif
        </div>
        <div>
            @if($useBonus)
                <x-link wire:click="resetBonus" active>Вернуть</x-link>
            @else
                <x-link wire:click="applyBonus" active>Списать</x-link>
            @endif
        </div>
    </div>
@else
    @if(!Auth::check())
    <div class="pb-4 border-b border-grey-200">
        <x-link href="{{ route('profile') }}" active>
            Авторизуйтесь
        </x-link>
        <span class="ml-1">чтобы списать и накопить бонусы</span>
    </div>
    @endif
@endif
