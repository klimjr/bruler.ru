@extends('layouts.app')
@section('content')
    <div class="mx-auto max-w-[800px] pb-6 md:pb-[62px] pt-4 md:pt-[54px] px-5 lg:px-0">
        <h1 class="mb-7 md:mb-8 text-3xl font-bold">Программа лояльности</h1>

        <div class="text-sm md:text-base text-[#131313]">
            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-lg font-bold mb-3">Как стать участником программы лояльности?</h2>

                <p>
                    Пройдите регистрацию на сайте bruler.ru и получите welcome-бонус 1000 руб. на первую покупку.
                </p>
                <p>
                    После регистрации вы автоматически становитесь участником программы лояльности. После первой покупки в онлайн-магазине вам присваивается статус "Первый взгляд" и на бонусный
                </p>
            </div>

            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-lg font-bold mb-3">Уровни программы лояльности:</h2>

                <div class="space-y-2 lg:space-y-0 lg:space-x-2 flex max-lg:flex-col max-lg:items-center">
                    <div>
                        <div class="absolute flex flex-col items-center text-white p-4 lg:p-3 w-[335px] h-[190px] lg:w-[260px] lg:h-[148px]">
                            <div class="text-sm lg:text-xs">Уровень</div>
                            <div class="text-lg lg:text-base font-semibold mb-1">Первый взгляд</div>
                            <div class="text-sm lg:text-xs">Процент накопления баллов</div>
                            <div class="text-lg lg:text-base font-semibold mb-1">3%</div>
                            <div class="text-sm lg:text-xs">Сумма покупок для достижения уровня</div>
                            <div class="text-lg lg:text-base font-semibold">0 - 49 999 руб.</div>
                        </div>
                        <div class="w-[335px] h-[190px] lg:w-[260px] lg:h-[148px]">
                            <x-icons.cards.card1 />
                        </div>
                    </div>
                    <div>
                        <div class="absolute flex flex-col items-center text-white p-4 lg:p-3 w-[335px] h-[190px] lg:w-[260px] lg:h-[148px]">
                            <div class="text-sm lg:text-xs">Уровень</div>
                            <div class="text-lg lg:text-base font-semibold mb-1">Влюбленное сердце</div>
                            <div class="text-sm lg:text-xs">Процент накопления баллов</div>
                            <div class="text-lg lg:text-base font-semibold mb-1">5%</div>
                            <div class="text-sm lg:text-xs">Сумма покупок для достижения уровня</div>
                            <div class="text-lg lg:text-base font-semibold">50 000 - 149 999 руб.</div>
                        </div>
                        <div class="w-[335px] h-[190px] lg:w-[260px] lg:h-[148px]">
                            <x-icons.cards.card2 />
                        </div>
                    </div>
                    <div>
                        <div class="absolute flex flex-col items-center text-white p-4 lg:p-3 w-[335px] h-[190px] lg:w-[260px] lg:h-[148px]">
                            <div class="text-sm lg:text-xs">Уровень</div>
                            <div class="text-lg lg:text-base font-semibold mb-1">Вечная страсть</div>
                            <div class="text-sm lg:text-xs">Процент накопления баллов</div>
                            <div class="text-lg lg:text-base font-semibold mb-1">7%</div>
                            <div class="text-sm lg:text-xs">Сумма покупок для достижения уровня</div>
                            <div class="text-lg lg:text-base font-semibold">150 001 - 300 000 руб.</div>
                        </div>
                        <div class="w-[335px] h-[190px] lg:w-[260px] lg:h-[148px]">
                            <x-icons.cards.card3 />
                        </div>
                    </div>
                </div>
            </div>

            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-lg font-bold mb-3">Как применить скидку по программе лояльности?</h2>

                <p>
                    Совершая покупку в Интернет-магазине bruler.ru в специальном окне, выберите "Оплата баллами", сайт автоматически покажет наличие баллов для списания. 1 балл = 1 рубль
                </p>
                <p>
                    Баллами возможно оплатить до 99% от стоимости заказа, без учета стоимости доставки. Остаток суммы можете оплатить любым удобным способом.
                </p>
            </div>

            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-lg font-bold mb-3">На какие товары можно применять скидку?</h2>

                <p>
                    Скидка действует на все товары в онлайн-магазине. Скидку невозможно применить на покупку подарочных сертификатов и товаров категории Sale.
                </p>
            </div>

            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-lg font-bold mb-3">Какой срок действия баллов?</h2>

                <p>
                    Баллы можно использовать в течение 1 года с момента начисления.
                </p>
            </div>

            <div>
                <h2 class="text-base md:text-lg font-bold mb-3">Как работает дополнительная скидка ко дню рождения?</h2>

                <p>
                    Скидка 1500 рублей ко Дню рождения клиента действует 5 дней — за 2 дня до Дня рождения, в день праздника и 2 дня после.
                </p>
                <p>
                    Скидка действует на все товары и суммируется с баллами по программе лояльности. При этом основная скидка по программе лояльности не распространяется на товары из распродажи.
                </p>
            </div>
        </div>
    </div>
@endsection
