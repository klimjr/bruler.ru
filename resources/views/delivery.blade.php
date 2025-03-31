@extends('layouts.app')
@section('content')
    <div class="mx-auto max-w-[800px] pb-6 md:pb-[62px] pt-4 md:pt-[54px] px-5 lg:px-0">
        <h1 class="mb-7 md:mb-8 text-3xl font-bold">Доставка</h1>

        <div class="text-sm md:text-base text-[#131313]">
            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-xl font-bold mb-3">Достависта</h2>

                <h3 class="text-base md:text-lg font-bold mb-3">Доставка заказов день в день по Москве и Московской области осуществляется через сервис Достависта.</h3>

                <ul class="list-[var(--my-marker)] list-inside">
                    <li>
                        Вы можете оформить заказ с доставкой в день оформления до 15:00.
                    </li>
                    <li>
                        Доставку можно выбрать на 2 дня вперед по доступным временным интервалам.
                    </li>
                    <li>
                        Заказы, оформленные в праздничные и выходные дни, передаются курьеру на доставку на следующий рабочий день в соответствии с выбранным интервалом при оформлении заказа на сайте.
                    </li>
                </ul>
            </div>

            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-xl font-bold mb-3">СДЭК</h2>

                <div class="mb-2">
                    <h3 class="text-base md:text-lg font-bold mb-3">Доставка осуществляется до пункта выдачи заказов (ПВЗ) или по указанному адресу.</h3>

                    <ul class="list-[var(--my-marker)] list-inside">
                        <li>
                            Стоимость рассчитывается исходя из удаленности адреса в момент оформления заказа и фиксируется на этот момент.
                        </li>
                        <li>
                            После оплаты заказа вам будет направлено сообщение с трек-номером для отслеживания.
                        </li>
                        <li>
                            Статусы можно отслеживать в личном кабинете СДЭК.
                        </li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-base md:text-lg font-bold mb-3">Передача заказа курьеру СДЭК зависит от времени оформления:</h2>

                    <ul class="list-[var(--my-marker)] list-inside">
                        <li>
                            Если заказ был оформлен до 13:00 с понедельника по пятницу, он обрабатывается и передается курьеру в тот же день. Дальнейшие сроки зависят от вашего адреса.
                        </li>
                        <li>
                            Если заказ был оформлен после 13:00 с понедельника по четверг, он обрабатывается и передается курьеру на следующий день после оформления.
                        </li>
                        <li>
                            Если заказ был оформлен в выходные дни, он обрабатывается и передается курьеру начиная с понедельника.
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-xl font-bold mb-3">Доставка в другие страны</h2>

                <p>
                    Стоимость доставки рассчитывается индивидуально.
                </p>
                <p>
                    Для уточнения стоимости доставки в другую страну, пожалуйста, свяжитесь со службой поддержки <x-link href="https://t.me/bruler_support" target="_blank" class="text-blue-500">t.me/bruler_support.</x-link>
                </p>
            </div>
        </div>
    </div>
@endsection
