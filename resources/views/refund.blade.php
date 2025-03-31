@extends('layouts.app')
@section('content')
    <div class="mx-auto max-w-[800px] pb-6 md:pb-[62px] pt-4 md:pt-[54px] px-5 lg:px-0">
        <h1 class="mb-7 md:mb-8 text-3xl font-bold">Обмен и возврат</h1>

        <div class="text-sm md:text-base text-[#131313]">
            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-lg font-bold mb-3">1. Подготовка посылки</h2>

                <p>Тщательно упакуйте товары, по возможности используя первоначальную упаковку, и приложите <strong>бланк возврата</strong>.</p>
                <p>В бланке возврата укажите список возвращаемых товаров и номер вашего заказа.</p>
                <p>Если вы не можете найти бланк возврата, скачайте его <x-link target="_blank" class="text-blue-500" href="/ЗАЯВЛЕНИЕ_НА_ВОЗВРАТ_ТОВАРА_BR.pdf">[здесь].</x-link> Наличие бланка возврата обязательно. Без заполненного бланка товары не будут приняты к возврату или обмену.</p>
            </div>

            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-lg font-bold mb-3">2. Отправка товара</h2>

                <p>Укажите наш адрес для возврата: <strong>2-я Бауманская, 9/23c3, офис 3203. ООО «Брулер»</strong>.</p>
                <p>Для возврата товаров надлежащего качества стоимость доставки оплачивается покупателем. В случае возврата товара ненадлежащего качества (бракованного), стоимость доставки оплачивается нашей компанией.</p>
            </div>

            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-lg font-bold mb-3">3. Какие товары можно вернуть?</h2>

                <div class="mb-3">
                    <p class="font-bold">Товары надлежащего качества:</p>
                    <p>Вы можете вернуть товар, если он сохранил свои потребительские свойства и товарный вид. На товаре не должно быть следов эксплуатации и носки, а также должны быть сохранены оригинальная и неповреждённая упаковка и ярлыки. Возврат возможен, если товар вам не подошёл по цвету, фасону или размеру.</p>
                </div>
                <div>
                    <p class="font-bold">Товары ненадлежащего качества:</p>
                    <p>Если доставленный товар оказался бракованным, вы имеете право вернуть его или обменять на аналогичный товар надлежащего качества (при наличии на складе). В этом случае стоимость доставки оплачивается с нашей стороны. Пришлите фото/видео, где видно брак, в <x-link target="_blank" class="text-blue-500" href="https://t.me/bruler_support">чат поддержки.</x-link></p>
                </div>
            </div>

            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-lg font-bold mb-3">4. Сроки возврата</h2>

                <ul class="list-[var(--my-marker)] list-inside">
                    <li>Посылка с возвращаемым товаром должна прибыть в ПВЗ СДЭК не позднее 14 дней после получения вами заказа.</li>
                    <li>Мы рекомендуем начать процедуру возврата в течение первых 7 дней с момента получения заказа, чтобы уложиться в установленные сроки.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-base md:text-lg font-bold mb-3">5. К возврату не принимаются</h2>

                <ul class="list-[var(--my-marker)] list-inside">
                    <li>Вязаные изделия, а также нательные изделия бельевого типа (нижнее белье, бельевые топы, боди и купальники)</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
