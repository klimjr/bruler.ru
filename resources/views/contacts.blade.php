@extends('layouts.app')
@section('content')
    <div class="mx-auto max-w-[800px] pb-6 md:pb-[62px] pt-4 md:pt-[54px] px-5 lg:px-0">
        <h1 class="mb-7 md:mb-8 text-3xl font-bold">Контакты</h1>

        <div class="text-sm md:text-base text-[#131313]">
            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-lg font-bold mb-3">Социальные сети</h2>

                <div class="flex items-center space-x-2">
                    <x-button-black size="md" square target="_blank" href="https://vk.com/brulerdamour">
                        <x-icons.new.vk />
                    </x-button-black>

                    <x-button-black size="md" square target="_blank" href="https://t.me/brulerwear">
                        <x-icons.new.tg />
                    </x-button-black>
                </div>
            </div>

            <div class="pb-[18px] mb-[18px] md:pb-6 md:mb-6 border-b border-[#EBEBEB]">
                <h2 class="text-base md:text-lg font-bold mb-3">Нужна помощь?</h2>

                <p>Возникли проблемы с заказом или есть вопросы?</p>
                <p>Поможем разобраться!</p>

                <x-button-black href="https://t.me/bruler_support" target="_blank" size="md" class="inline-flex mt-3">
                    Техподдержка
                </x-button-black>
            </div>

            <div>
                <h2 class="text-base md:text-lg font-bold mb-3">Данные о компании</h2>

                <p>Электронная почта: brulerd@mail.ru</p>
                <p>Наименование организации: ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ "БРУЛЕР"</p>
                <p>ИНН: 9714017207</p>
                <p>ОГРНИП: 1237700545378</p>
                <p>Банк: АО «Тинькофф Банк»</p>
                <p>БИК: 044525974</p>
                <p>Расчётный счёт: 40702810410001518679</p>
                <p>Корр. счёт: 30101810145250000974</p>
            </div>
        </div>
    </div>
@endsection
