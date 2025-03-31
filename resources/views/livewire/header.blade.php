<header
    x-data="{
        catalogDropdown: false,
        collectionDropdown: false,
        aboutDropdown: false,
        buyDropdown: false,
        isMobilePopupOpen: false,
        showCatalogMenu: false,
        showCollectionMenu: false,
        showAboutMenu: false,
        showBuyMenu: false,
        cartDesktop: false,
        bgBlack: false,
        bgBlackCart: false,

        closeCart() {
            this.cartDesktop = false
            document.body.classList.remove('overflow-hidden');
            this.bgBlackCart = false
        },

        openCart() {
            this.cartDesktop = true
            document.body.classList.add('overflow-hidden');
            this.bgBlackCart = true
        },

        openMainMenu() {
            this.showCatalogMenu = false;
            this.showCollectionMenu = false;
            this.showAboutMenu = false;
            this.showBuyMenu = false;
            this.isMobilePopupOpen = true;
        },
        openCatalogMenu() {
            this.showCatalogMenu = true;
            this.showCollectionMenu = false;
            this.showAboutMenu = false;
            this.showBuyMenu = false;
        },
        openCollectionMenu() {
            this.showCollectionMenu = true;
            this.showCatalogMenu = false;
            this.showAboutMenu = false;
            this.showBuyMenu = false;
        },
        openAboutMenu() {
            this.showAboutMenu = true;
            this.showCatalogMenu = false;
            this.showCollectionMenu = false;
            this.showBuyMenu = false;
        },
        openBuyMenu() {
            this.showBuyMenu = true;
            this.showCatalogMenu = false;
            this.showCollectionMenu = false;
            this.showAboutMenu = false;
        },
        backToMainMenu() {
            this.showCatalogMenu = false;
            this.showCollectionMenu = false;
            this.showAboutMenu = false;
            this.showBuyMenu = false;
        },
    }"
>
    <div
        x-init="init()"
        class="fixed w-full z-40 top-0 font-normal js-header"
    >
        @if ($runningTexts)
            <div
                x-show="isShowRunningTexts"
                x-cloak
                class="flex items-center justify-center py-[8px] px-[20px] xl:px-[30px]"
                style="background: {{ $runningTexts['bg_color'] }}; color: {{ $runningTexts['text_color'] }}"
            >
            <span class="w-full text-center text-xs">
                {{ $runningTexts['text'] }}
            </span>
                <x-icons.new.close-stroke class="cursor-pointer ml-auto" @click="closeRunningTexts"/>
            </div>
        @endif

        <div class="flex items-center justify-between bg-[#F7F7F7] py-4 px-4 xl:px-[30px] select-none relative">
            <ul class="xl:flex items-center gap-x-[24px] hidden">
                <li><a href="{{ route('collection.show', 'new') }}">Новое</a></li>
                <li class="text-[#CD0C0C]"><a href="{{ route('collection.show', 'sale') }}">Sale</a></li>
                <li @click="catalogDropdown = !catalogDropdown"
                    class="flex items-center gap-x-[8px] relative cursor-pointer">
                <span>Каталог
                </span>
                    <div :class="{ 'transform rotate-180': catalogDropdown }" class="transition-transform duration-300">
                        <x-icons.new.arrow/>
                    </div>

                    <div x-show="catalogDropdown" @click.away="catalogDropdown = false" x-transition x-cloak
                         class="absolute top-7 bg-white drop-shadow-md rounded-xl overflow-hidden">
                        <ul class="whitespace-nowrap">
                            <li class="border-b-[1px] border-[#F7F7F7] hover:bg-[#F7F7F7] active:bg-[#EBEBEB]">
                                <a class="py-[8px] px-[16px] w-full block" href="{{ route('catalog') }}">Смотреть
                                    все</a>
                            </li>
                            <li class="border-b-[1px] border-[#F7F7F7] hover:bg-[#F7F7F7] active:bg-[#EBEBEB]">
                                <a class="py-[8px] px-[16px] w-full block"
                                   href="{{ route('collection.show', 'new') }}">Новинки</a>
                            </li>
                            @foreach (App\Models\Category::orderBy('order')->get() as $category)
                                @if($category->id == 7)
                                    <li class="border-[#F7F7F7] border-b hover:bg-[#F7F7F7] active:bg-[#EBEBEB]">
                                        <a class="py-[8px] px-[16px] w-full flex items-center justify-between"
                                           href="{{ route('kit.show') }}">Комплекты
                                            <span class="text-[#CD0C0C]">NEW</span>
                                        </a>
                                    </li>
                                @endif
                                <li
                                    class="hover:bg-[#F7F7F7] active:bg-[#EBEBEB] {{ !$loop->last ? 'border-b-[1px] border-[#F7F7F7]' : 'rounded-b-[12px]' }}">
                                    <a class="py-[8px] px-[16px] w-full flex items-center justify-between @if ($category->discount > 0) text-[#CD0C0C] @endif"
                                       href="{{ route('collection.filter', ['category' => [$category->id]]) }}">
                                        <span>{{ $category->name }}</span>
                                        @if ($category->discount > 0)
                                            <span>{{ $category->discount }}%</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
                <li @click="collectionDropdown = !collectionDropdown"
                    class="flex items-center gap-x-[8px] relative cursor-pointer">
                <span>Коллекции
                </span>
                    <div :class="{ 'transform rotate-180': collectionDropdown }" class="transition-transform duration-300">
                        <x-icons.new.arrow/>
                    </div>

                    <div x-show="collectionDropdown" @click.away="collectionDropdown = false" x-transition x-cloak
                         class="absolute top-7 bg-white drop-shadow-md rounded-xl overflow-hidden">
                        <ul class="whitespace-nowrap">
                            @foreach (App\Models\Collection::orderBy('position', 'desc')->get() as $collection)
                                <li
                                    class="hover:bg-[#F7F7F7] active:bg-[#EBEBEB] {{ !$loop->last ? 'border-b-[1px] border-[#F7F7F7]' : 'rounded-b-[12px]' }}">
                                    <a class="py-[8px] px-[16px] w-full flex flex-col"
                                       href="{{ route('collection.show', ['collection' => $collection->id]) }}">
                                        <span>{{ $collection->title }}</span>
                                        <span class="text-[#5A5A5A]">{{ $collection->desc }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
                <li @click="aboutDropdown = !aboutDropdown" class="flex items-center gap-x-[8px] relative cursor-pointer">
                <span>О нас
                </span>
                    <div :class="{ 'transform rotate-180': aboutDropdown }" class="transition-transform duration-300">
                        <x-icons.new.arrow/>
                    </div>

                    <div
                        x-show="aboutDropdown"
                        @click.away="aboutDropdown = false"
                        x-transition
                        x-cloak
                        class="absolute top-7 bg-white drop-shadow-md rounded-xl"
                    >
                        <ul class="whitespace-nowrap">
                            <li class="rounded-xl hover:bg-[#F7F7F7] active:bg-[#EBEBEB]">
                                <a class="py-[8px] px-[16px] w-full block" href="{{ route('about_brand') }}">
                                    О бренде
                                </a>
                            </li>
                            {{-- <li class="border-b-[1px] border-[#F7F7F7] hover:bg-[#F7F7F7] active:bg-[#EBEBEB]"><a
                                    class="py-[8px] px-[16px] w-full block">Магазин</a>
                            </li>
                            <li class="border-[#F7F7F7] rounded-b-[12px] hover:bg-[#F7F7F7] active:bg-[#EBEBEB]">
                                <a class="py-[8px] px-[16px] w-full block">Вакансии</a>
                            </li> --}}
                        </ul>
                    </div>
                </li>
            </ul>

            <div class="xl:hidden">
                <div @click="isMobilePopupOpen = !isMobilePopupOpen; bgBlack = !bgBlack" class="cursor-pointer">
                    <template x-if="!isMobilePopupOpen">
                        <x-icons.new.burg/>
                    </template>
                    <template x-if="isMobilePopupOpen">
                        <x-icons.new.close-burg/>
                    </template>
                </div>
            </div>

            <div class="absolute left-1/2 transform -translate-x-1/2">
                <a href="/">
                    <x-new-logo-desktop class="w-[150px] xl:w-auto"/>
                </a>
            </div>

            <ul class="flex items-center gap-x-[12px] xl:gap-x-[24px] max-md:h-4">
                <li class="relative">
                    @if(Auth::check())
                        <div
                            class="absolute right-[-6px] top-[-6px] w-[7px] h-[7px] animate-pulse bg-black rounded-full flex items-center justify-center">
                        </div>
                    @endif
                    <a href="{{ route('profile') }}">
                        <x-icons.new.user class="h-[18px] w-[18px] xl:w-auto xl:h-auto" /></a>
                </li>

                <li class="relative">
                    <div class="absolute right-[-10px] top-[-8px] w-[14px] h-[14px] bg-[#CD0C0C] rounded-full text-center flex items-center justify-center">
                        <span class="text-[10px] text-white w-full h-full">{{ $favoritesCount }}</span>
                    </div>

                    <a href="{{ route('profile.favourites') }}"  >
                        <x-icons.new.heart class="h-[18px] w-[18px] xl:w-auto xl:h-auto" />
                    </a>
                </li>

                <li class="hidden xl:block relative cursor-pointer"
                    @click="openCart">
                    <div
                        class="absolute right-[-10px] top-[-8px] w-[14px] h-[14px] bg-[#CD0C0C] rounded-full text-center flex items-center justify-center">
                        <span class="text-[10px] text-white w-full h-full">{{ $productCount }}</span>
                    </div>
                    <x-icons.new.bag class="h-[18px] w-[18px] xl:w-auto xl:h-auto" />
                </li>


                <li class="relative xl:hidden leading-none">
                    <button type="button" @click="openCart">
                        <div
                            class="absolute right-[-10px] top-[-8px] w-[14px] h-[14px] bg-[#CD0C0C] rounded-full flex items-center justify-center">
                            <span class="text-[10px] text-white">{{ $productCount }}</span>
                        </div>
                        <x-icons.new.bag class="h-[18px] w-[18px] xl:w-auto xl:h-auto" />
                    </button>
                </li>

                <li @click="buyDropdown = !buyDropdown"
                    class="xl:flex items-center gap-x-[8px] relative cursor-pointer hidden">
                <span>Покупателям
                </span>
                    <div :class="{ 'transform rotate-180': buyDropdown }" class="transition-transform duration-300">
                        <x-icons.new.arrow/>
                    </div>

                    <div x-show="buyDropdown" @click.away="buyDropdown = false" x-transition x-cloak
                         class="absolute right-0 top-7 bg-white drop-shadow-md rounded-[12px]">
                        <ul class="whitespace-nowrap">
                            <li
                                class="border-b-[1px] border-[#F7F7F7] rounded-t-[12px] hover:bg-[#F7F7F7] active:bg-[#EBEBEB]">
                                <a class="py-[8px] px-[16px] w-full block" href="{{ route('loyalty') }}">Программа
                                    лояльности</a>
                            </li>
                            <li class="border-b-[1px] border-[#F7F7F7] hover:bg-[#F7F7F7] active:bg-[#EBEBEB]"><a
                                    class="py-[8px] px-[16px] w-full block" href="{{ route('payment') }}">Оплата</a>
                            </li>
                            <li class="border-b-[1px] border-[#F7F7F7] hover:bg-[#F7F7F7] active:bg-[#EBEBEB]">
                                <a class="py-[8px] px-[16px] w-full block" href="{{ route('delivery') }}">Доставка</a>
                            </li>
                            <li class="border-b-[1px] border-[#F7F7F7] hover:bg-[#F7F7F7] active:bg-[#EBEBEB]">
                                <a class="py-[8px] px-[16px] w-full block" href="{{ route('refund') }}">Обмен и возврат</a>
                            </li>
                            <li class="border-b-[1px] border-[#F7F7F7] hover:bg-[#F7F7F7] active:bg-[#EBEBEB]"><a
                                    class="py-[8px] px-[16px] w-full block" href="{{ route('contacts') }}">Контакты</a>
                            </li>
                            <li class="border-[#F7F7F7] rounded-b-[12px] hover:bg-[#F7F7F7] active:bg-[#EBEBEB]"><a
                                    class="py-[8px] px-[16px] w-full block" href="{{ route('documents') }}">Документы</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>

        {{--     ToDo  вынести бы за пределы, только надо будет передлать меню     --}}
        <div
            x-cloak
            x-show="isMobilePopupOpen"
            x-transition:enter="transition-transform ease-out duration-300"
            x-transition:enter-start="transform -translate-x-full"
            x-transition:enter-end="transform translate-x-0"
            x-transition:leave="transition-transform ease-in duration-300"
            x-transition:leave-start="transform translate-x-0"
            x-transition:leave-end="transform -translate-x-full"
            class="absolute h-screen w-[90vw] py-[16px] left-0 bottom-[-100vh] bg-[#F7F7F7] shadow-lg z-[55] overflow-y-auto"
            @click.away="isMobilePopupOpen = false; bgBlack = false;"
        >
            <ul class="px-[12px]" x-show="!showCatalogMenu">
                <li class="border-b-[1px] border-[#EBEBEB] hover:bg-[#EBEBEB]"><a
                        class="w-full block px-[4px] py-[12px]"
                        href="{{ route('collection.show', 'new') }}">Новое</a></li>
                <li class="border-b-[1px] border-[#EBEBEB] hover:bg-[#EBEBEB]"><a
                        href="{{ route('collection.show', 'sale') }}"
                        class="w-full block px-[4px] py-[12px] text-[#CD0C0C]">Sale</a></li>
                <li class="border-b-[1px] border-[#EBEBEB] px-[4px] py-[12px] hover:bg-[#EBEBEB] flex items-center justify-between cursor-pointer"
                    @click="openCatalogMenu">
                    <span>Каталог</span>
                    <x-icons.new.arrow-right />
                </li>
                <li class="border-b-[1px] border-[#EBEBEB] px-[4px] py-[12px] hover:bg-[#EBEBEB] flex items-center justify-between cursor-pointer"
                    @click="openCollectionMenu">
                    <span>Коллекции</span>
                    <x-icons.new.arrow-right />
                </li>
                <li class="border-b-[1px] border-[#EBEBEB] px-[4px] py-[12px] hover:bg-[#EBEBEB] flex items-center justify-between cursor-pointer"
                    @click="openAboutMenu">
                    <span>О нас</span>
                    <x-icons.new.arrow-right />
                </li>
                <li class="px-[4px] py-[12px] hover:bg-[#EBEBEB] flex items-center justify-between cursor-pointer"
                    @click="openBuyMenu">
                    <span>Покупателям</span>
                    <x-icons.new.arrow-right />
                </li>
            </ul>

            <ul x-show="showCatalogMenu" x-transition:enter="transition-transform ease-out duration-300"
                x-transition:enter-start="transform -translate-x-full"
                x-transition:enter-end="transform translate-x-0"
                x-transition:leave="transition-transform ease-in duration-300"
                x-transition:leave-start="transform translate-x-0"
                x-transition:leave-end="transform -translate-x-full"
                class="absolute top-0 left-0 px-[12px] h-[calc(100%+200px)] w-full bg-[#F7F7F7] z-50" x-cloak>
                <li class="px-[4px] py-[12px] flex items-center gap-x-[8px] cursor-pointer" @click="backToMainMenu">
                    <x-icons.new.arrow-right class="rotate-180" />
                    <span class="relative underline-animated">Назад</span>
                </li>
                <li class="border-b-[1px] border-[#EBEBEB] hover:bg-[#EBEBEB]"><a
                        class="w-full block px-[4px] py-[12px]" href="{{ route('collection.filter') }}">Смотреть
                        все</a></li>
                <li class="border-b-[1px] border-[#EBEBEB] hover:bg-[#EBEBEB]"><a
                        class="w-full block px-[4px] py-[12px]"
                        href="{{ route('collection.show', 'new') }}">Новинки</a></li>
                @foreach (App\Models\Category::orderBy('order')->get() as $category)
                    @if($category->id == 7)
                        <li class="hover:bg-[#EBEBEB] {{ !$loop->last ? 'border-b-[1px] border-[#EBEBEB]' : '' }}"><a
                                class="w-full block px-[4px] py-[12px] flex items-center justify-between @if ($category->discount > 0) text-[#CD0C0C] @endif"
                                href="{{ route('kit.show') }}">
                                Комплекты
                                <span class="text-[#CD0C0C]">NEW</span>
                            </a>
                        </li>
                    @endif
                    <li class="hover:bg-[#EBEBEB] {{ !$loop->last ? 'border-b-[1px] border-[#EBEBEB]' : '' }}"><a
                            class="w-full block px-[4px] py-[12px] flex items-center justify-between @if ($category->discount > 0) text-[#CD0C0C] @endif"
                            href="{{ route('collection.filter', ['category' => [$category->id]]) }}">
                            <span>{{ $category->name }}</span>
                            @if ($category->discount > 0)
                                <span>{{ $category->discount }}%</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>

            <ul x-show="showCollectionMenu" x-transition:enter="transition-transform ease-out duration-300"
                x-transition:enter-start="transform -translate-x-full"
                x-transition:enter-end="transform translate-x-0"
                x-transition:leave="transition-transform ease-in duration-300"
                x-transition:leave-start="transform translate-x-0"
                x-transition:leave-end="transform -translate-x-full"
                class="absolute top-0 left-0 px-[12px] h-[calc(100%+200px)] w-full bg-[#F7F7F7] z-50" x-cloak>
                <li class="px-[4px] py-[12px] flex items-center gap-x-[8px] cursor-pointer" @click="backToMainMenu">
                    <x-icons.new.arrow-right class="rotate-180" />
                    <span class="relative underline-animated">Назад</span>
                </li>
                @foreach (App\Models\Collection::orderBy('position', 'desc')->get() as $collection)
                    <li class="hover:bg-[#EBEBEB] {{ !$loop->last ? 'border-b-[1px] border-[#EBEBEB]' : '' }}"><a
                            class="w-full block px-[4px] py-[12px]"
                            href="{{ route('collection.show', ['collection' => $collection->id]) }}">{{ $collection->title }}</a>
                    </li>
                @endforeach
            </ul>

            <ul x-show="showAboutMenu" x-transition:enter="transition-transform ease-out duration-300"
                x-transition:enter-start="transform -translate-x-full"
                x-transition:enter-end="transform translate-x-0"
                x-transition:leave="transition-transform ease-in duration-300"
                x-transition:leave-start="transform translate-x-0"
                x-transition:leave-end="transform -translate-x-full"
                class="absolute top-0 left-0 px-[12px] h-[calc(100%+200px)] w-full bg-[#F7F7F7] z-50" x-cloak>
                <li class="px-[4px] py-[12px] flex items-center gap-x-[8px] cursor-pointer" @click="backToMainMenu">
                    <x-icons.new.arrow-right class="rotate-180" />
                    <span class="relative underline-animated">Назад</span>
                </li>
                <li class="border-b-[1px] border-[#EBEBEB] hover:bg-[#EBEBEB]"><a
                        class="w-full block px-[4px] py-[12px]" href="{{ route('about_brand') }}">О бренде</a></li>
                {{-- <li class="border-b-[1px] border-[#EBEBEB] hover:bg-[#EBEBEB]"><a
                        class="w-full block px-[4px] py-[12px]">Магазин</a></li>
                <li class="hover:bg-[#EBEBEB]"><a class="w-full block px-[4px] py-[12px]">Вакансии</a></li> --}}
            </ul>

            <ul x-show="showBuyMenu" x-transition:enter="transition-transform ease-out duration-300"
                x-transition:enter-start="transform -translate-x-full"
                x-transition:enter-end="transform translate-x-0"
                x-transition:leave="transition-transform ease-in duration-300"
                x-transition:leave-start="transform translate-x-0"
                x-transition:leave-end="transform -translate-x-full"
                class="absolute top-0 left-0 px-[12px] h-[calc(100%+200px)] w-full bg-[#F7F7F7] z-50" x-cloak>
                <li class="px-[4px] py-[12px] flex items-center gap-x-[8px] cursor-pointer" @click="backToMainMenu">
                    <x-icons.new.arrow-right class="rotate-180" />
                    <span class="relative underline-animated">Назад</span>
                </li>
                <li class="border-b-[1px] border-[#EBEBEB] hover:bg-[#EBEBEB]"><a
                        class="w-full block px-[4px] py-[12px]" href="{{ route('loyalty') }}">Программа
                        лояльности</a></li>
                <li class="border-b-[1px] border-[#EBEBEB] hover:bg-[#EBEBEB]"><a
                        class="w-full block px-[4px] py-[12px]" href="{{ route('payment') }}">Оплата</a></li>
                <li class="border-b-[1px] border-[#EBEBEB] hover:bg-[#EBEBEB]"><a
                        class="w-full block px-[4px] py-[12px]" href="{{ route('delivery') }}">Доставка</a>
                </li>
                <li class="border-b-[1px] border-[#EBEBEB] hover:bg-[#EBEBEB]">
                    <a class="w-full block px-[4px] py-[12px]" href="{{ route('refund') }}">Обмен и возврат</a>
                </li>
                <li class="border-b-[1px] border-[#EBEBEB] hover:bg-[#EBEBEB]">
                    <a class="w-full block px-[4px] py-[12px]" href="{{ route('contacts') }}">Контакты</a>
                </li>
                <li class="hover:bg-[#EBEBEB]">
                    <a
                        class="w-full block px-[4px] py-[12px]"
                                                  href="{{ route('documents') }}"
                    >
                        Документы
                    </a>
                </li>
            </ul>
        </div>
        {{--     ToDo revert bg old, что бы не менять верстку меню   --}}
        <div
            x-cloak
            x-show="bgBlack"
            class="fixed inset-0 bg-black-opacity z-10"
            :class="isShowRunningTexts ? 'top-[48px]' : 'top-[80px]'"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-50"
            x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-50"
            x-transition:leave-end="opacity-0"
        ></div>
    </div>

    <div
        x-cloak
        class="fixed top-0 right-0 h-[100dvh] w-full max-w-[530px] bg-white z-[55]"
        x-show="cartDesktop"
        x-transition:enter="transition ease-out duration-500 transform"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-500 transform"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
    >
        <x-cart-redesigned :productCount="$productCount"/>
    </div>

    <div
        x-cloak
        x-show="bgBlackCart"
        :class="isShowRunningTexts ? 'mt-[48px] xl:mt-[56px]' : 'mt-0'"
        class="fixed inset-0 bg-black-opacity z-50"
        @click="cartDesktop ? closeCart() : null"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-50"
        x-transition:leave="transition ease-in duration-500"
        x-transition:leave-start="opacity-50"
        x-transition:leave-end="opacity-0"
    ></div>
</header>
