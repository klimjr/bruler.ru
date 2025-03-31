<footer
    class="md:h-[360px] bg-[#F7F7F7] px-4 md:px-[30px] py-4 md:py-[24px] mt-2 flex justify-center flex-col gap-y-6">
    <div class="flex flex-col gap-y-6 md:gap-y-[0px] md:flex-row md:justify-between">
        <div class="flex flex-col gap-y-3">
            <x-new-logo-desktop />
            <span>г. Москва, 2-я Бауманская, 9/23c3, офис 3203</span>
            <span>Ежедневно с 10:00 по 19:00</span>

            <a href="mailto:support@bruler.ru">support@bruler.ru</a>
            <div class="flex items-center gap-x-[16px]">
                <a target="_blank" href="mailto:support@bruler.ru"><x-icons.new.mail /></a>
                <a target="_blank" href="https://vk.com/brulerdamour"><x-icons.new.vk /></a>
                <a target="_blank" href="https://t.me/brulerwear"><x-icons.new.tg /></a>
                <a target="_blank" href="https://wa.me/message/3VVEZGYWKTN6L1"><x-icons.new.whatsapp /></a>
            </div>
        </div>

        <ul class="flex flex-col gap-y-3">
            <li><a class="relative underline-animated" href="/">Главная</a></li>
            <li><a class="relative underline-animated" href="{{ route('about_brand') }}">О бренде</a></li>
            {{-- <li><a class="relative underline-animated">Вакансии</a></li> --}}
            <li><a class="relative underline-animated" href="{{ route('documents') }}">Документы</a></li>
        </ul>
        <ul class="flex flex-col gap-y-3">
{{--            <li><a class="relative underline-animated" href="{{ route('collection.filter') }}">Магазин</a></li>--}}
            <li><a class="relative underline-animated" href="{{ route('loyalty') }}">Программа лояльности</a></li>
            <li><a class="relative underline-animated" href="{{ route('payment') }}">Оплата</a></li>
            <li><a class="relative underline-animated" href="{{ route('delivery') }}">Доставка</a></li>
            <li><a class="relative underline-animated" href="{{ route('refund') }}">Обмен и возврат</a></li>
            <li><a class="relative underline-animated" href="{{ route('contacts') }}">Контакты</a></li>
        </ul>
        <div class="flex flex-col gap-y-4">
            <div>
                <h3 class="text-lg">Подпишись на новости</h3>
                <span class="text-[#5A5A5A] text-sm">Не пропустите самые актуальные обновления и эксклюзивные
                    предложения</span>
            </div>
            <div class="flex flex-col gap-y-[16px]">
                <div>
                    <div
                        class="w-full h-[48px] px-[16px] rounded-[16px] bg-[#EBEBEB] relative focus-within:border-[1px] focus-within:border-black @error('email') border-[1px] border-[#CD0C0C] @enderror">
                        <input id="emailInput" type="email" wire:model="email"
                               class="peer w-full text-sm bg-transparent border-[0px] p-0 translate-y-[18px] placeholder:text-transparent"
                               placeholder="" />
                        <label
                            class="absolute pointer-events-none text-[#999999] left-0 ml-[16px] translate-y-[4px] text-xs duration-100 ease-linear peer-placeholder-shown:translate-y-[16px] peer-focus:translate-y-[4px]">
                            Введите ваш email
                        </label>
                    </div>

                    @error('email')
                    <span class="ml-[16px] text-[#CD0C0C] font-medium text-[11px]">{{ $message }}</span>
                    @enderror
{{--        TODO: доделать--}}
{{--                    @if (session()->has('message'))--}}
{{--                        <span class="ml-[16px] text-green-500 font-medium text-[11px]">{{ session('message') }}</span>--}}
{{--                    @endif--}}
                </div>

                <button type="button" wire:click="subscribe"
                        class="px-[24px] h-[48px] rounded-[16px] bg-black text-white text-base w-full">
                    Подписаться
                </button>
            </div>
        </div>
    </div>
    <div class="pt-[8px] border-[#EBEBEB] border-t-[1px]">
        <span>© Brûler d'Amour</span>
    </div>
</footer>
