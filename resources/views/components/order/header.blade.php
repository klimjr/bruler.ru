<header class="w-full bg-grey-100">
    <div class="container container__inner-mobile flex items-center justify-between">
        <a href="/">
            <x-icons.logo
                class="md:w-[280px] h-auto"
            />
        </a>
        <x-link
            href="{{ route('collection.filter') }}"
            class="text-xs md:text-base"
        >
            Вернуться в каталог
        </x-link>
    </div>
</header>
