<div
    wire:ignore
    x-cloak
    x-data="productDetailedSwiper({ hideRunningTexts: sessionStorage.getItem('hideRunningTexts') === null })"
>
    <style>
        .swiper-thumb {
            height: 530px;
        }

        .swiper-button-prev-thumb,
        .swiper-button-next-thumb {
            position: relative;
            left: 50%;
            transform: translateX(-50%);
        }

        .swiper-button-prev-thumb {
            margin-bottom: 8px;
        }
        .swiper-button-next-thumb {
            margin-top: 8px;
        }

        .swiper-scrollbar {
            background: #F2F2F4;
            border-radius: 0px;
        }

        .swiper-scrollbar-drag {
            background: var(--black);
            border-radius: 0px;
        }

        .swiper-horizontal>.swiper-scrollbar,
        .swiper-scrollbar.swiper-scrollbar-horizontal {
            height: 5px;
            left: 0;
            top: initial;
            bottom: 0;
            width: 100%;
            background-color: var(--grey-200);
        }

        .swiper-slide-thumb {
            width: 100px;
            height: 140px;
            border: 2px solid transparent;
        }

        @media (hover: hover) {
            .swiper-slide-thumb:hover {
                border-color: var(--black);
                cursor: pointer;
            }
        }

        .swiper-slide-thumb.custom-swiper-slide-active {
            border: 2px solid var(--black);
        }

        .thumb-slider {
            @media (min-width: 768px) {
                margin-right: 8px;
            }
            @media (max-width: 767px) {
                display: none;
            }
        }

        .swiper-slide-main {
            height: 500px;


            @media (min-width: 768px) {
                height: calc(100vh - 84px);

                &.full-banner {
                    height: calc(100vh - 52px);
                }
            }
        }
    </style>

    <div class="lg:flex">
        <div class="lg:w-[48%] md:ml-[30px] md:flex">
            @if ($product->type !== \App\Models\Product::TYPE_CERTIFICATE && count($gallery) > 1)
                <div class="thumb-slider">
                    <x-button-outlined
                        class="swiper-button-prev-thumb"
                        square
                        size="lg"
                    >
                        <x-icons.arrow-top />
                    </x-button-outlined>

                    <div id="thumbsProduct" class="swiper swiper-thumb relative">
                        <div
                            class="swiper-wrapper swiper-wrapper-thumb"
                        >
                            @foreach ($gallery as $index => $image)
                                <div class="swiper-slide swiper-slide-thumb">
                                    @if (strpos($image['image'], '.mp4') !== false)
                                        <video
                                            class="preview-video w-full h-full object-contain"
                                            muted
                                            playsinline>
                                            <source src="{{ $image['image'] }}" type="video/mp4">
                                        </video>
                                    @else
                                        <img
                                            class="w-full h-full object-contain"
                                            alt="{{ $image['alt'] ?? $product->name }}"
                                            src="{{ $image['image'] }}"
                                            x-cloak />  @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <x-button-outlined
                        class="swiper-button-next-thumb"
                        square
                        size="lg"
                    >
                        <x-icons.arrow-down-angle />
                    </x-button-outlined>
                </div>
        @endif

        <!-- Main Swiper -->

            <div
                class="card-main relative min-w-0"
                :class="isShowRunningTexts ? 'md:h-[calc(100vh-96px)]' : 'md:h-[calc(100vh-52px)]'"
            >
                @if ($product->type === \App\Models\Product::TYPE_CERTIFICATE)
                    <img alt="{{ $certificate['alt'] ?? $product->name }}" src="{{ $certificate['image'] }}" />
                @else
                    <div id="mainProduct" class="swiper swiper-main">
                        <div class="swiper-wrapper swiper-wrapper-main">
                            @foreach ($gallery as $index => $image)
                                <div
                                    class="swiper-slide swiper-slide-main relative"
                                    :class="{
                                       'full-banner-running': isShowRunningTexts,
                                       'full-banner': !isShowRunningTexts
                                     }"
                                >
                                    @if(Str::endsWith($image['image'], ['.jpg', '.png', '.jpeg', '.webp']))
                                        <img
                                            src="{{ $image['image'] }}"
                                            alt="Изображение галереи"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                        />
                                    @elseif(Str::endsWith($image['image'], ['.mov', '.mp4']))
                                        <video
                                            playsinline
                                            muted
                                            loop
                                            preload="auto"
                                            class="w-full h-full object-cover">
                                            <source src="{{ $image['image'] }}" type="video/mp4">
                                        </video>
                                    @endif
                                </div>
                            @endforeach

                        </div>
                        <div class="swiper-scrollbar md:hidden"></div>
                    </div>
            @endif

            <!-- Иконка favorite для мобильной версии -->
                <div class="absolute top-[14px] right-[14px] z-10 md:hidden">
                    <livewire:fire
                        button-classes="text-color-111"
                        :isActive="true"
                        :product="$product"
                    />
                </div>
            </div>
        </div>

        {{--   Блок с информацией         --}}
        <div id="sidebar" wire:key="{{ rand() }}" class="p-8 lg:w-[50%] bg-grey-100 md:mx-2 max-md:mt-1">
            <div class="space-y-6 xl:max-w-[480px]">
                <div class="flex flex-col justify-between md:justify-start items-start">
                    <div class="flex flex-col w-full">
                        <div class="flex justify-between text-[28px] md:text-[32px] font-semibold">
                            <div>{{ $product->name_en }}</div>
                        </div>
                        <div class="text-grey-300 mt-2">
                            {{ $product->name }}
                        </div>
                    </div>

                    <div class="flex items-baseline space-x-6">
                        <div class="whitespace-nowrap">
                            @if ($product->type === \App\Models\Product::TYPE_CERTIFICATE)
                                <span x-data="{
                                        productPrice: @this.price,
                                        updateProductPrice() {
                                            if (this.productPrice != @this.price) {
                                                this.productPrice = @this.price;
                                                window.dispatchEvent(new CustomEvent('initSidebarScroll'));
                                            }
                                        }
                                    }"
                                      x-init="setInterval(() => updateProductPrice(), 1000);"
                                      x-text="productPrice"
                                ></span>
                                ₽
                            @else
                                @if (isset($product->discount) && $product->discount != 0)
                                    <div class="flex items-center space-x-1 justify-center">
                                        <span class="text-red text-[28px]">{{ number_format($product->getDiscountedPrice(), 0, ' ', ' ') }}₽</span>
                                    </div>
                                @else
                                    {{ number_format($product->price, 0, ' ', ' ') }}₽
                                @endif
                            @endif
                        </div>

                        @if (isset($product->discount) && $product->discount != 0)
                            <div
                                class="text-[28px] text-color-111 whitespace-nowrap line-through">
                                <s>{{ number_format($product->price, 0, ' ', ' ') }}₽</s>
                            </div>
                        @endif
                    </div>
                    <div>
                        <yandex-pay-badge
                            merchant-id="{{ env('YANDEX_MERCHANT_ID') }}"
                            type="bnpl"
                            amount="{{ $product->price }}"
                            size="s"
                            variant="detailed"
                            theme="light"
                            align="left"
                            color="transparent"
                        />
{{--                        <img src="/storage/01JKWNMK2BD029VAWAPNQ8Z6P9.svg" alt="Яндекс Сплит">--}}
                    </div>
                </div>

                @if ($product->type === \App\Models\Product::TYPE_PRODUCT)
                    <div>
                        <div class="text-sm mb-2">Выберите размер:</div>
                        <div
                            class="inline-flex items-center px-2 py-1 space-x-1"
                            x-data="{
                            indicatorBgColor: @this.indicatorBgColor,
                            indicatorColor: @this.indicatorColor,
                            indicatorText: @this.indicatorText,
                            updateIndicator() {
                                if (this.indicatorColor != @this.indicatorColor || this.indicatorText != @this.indicatorText) {
                                    this.indicatorColor = @this.indicatorColor;
                                    this.indicatorBgColor = @this.indicatorBgColor;
                                    this.indicatorText = @this.indicatorText;
                                }
                            }
                        }" x-init="setInterval(() => updateIndicator(), 1000);"
                            :style="{ backgroundColor: indicatorBgColor, color: indicatorColor }"
                        >
                            <x-icons.product-indicator />

                            <div class="text-xs md:text-sm" x-text="indicatorText"></div>
                        </div>

                        <livewire:product-variant-selector-redesign
                            :selected-color="$color"
                            wire:key="product-variant-selector-{{ $product->id }}"
                            :product="$product"
                        />
                    </div>

                    <x-link
                        class=""
                        active
                        @click="open = true"
                    >
                        Размерная сетка
                    </x-link>
                @endif

                @if ($product->type === \App\Models\Product::TYPE_CERTIFICATE)
                    <livewire:product-certificate-selector
                        :selected-certificate="$certificate"
                        wire:key="product-certificate-selector-{{ $product->id }}"
                        :product="$product"
                    />
                @endif

                {{--                @if ($product->type === \App\Models\Product::TYPE_SET)--}}
                {{--                    <livewire:product-set-selector--}}
                {{--                        :selected-color="$color"--}}
                {{--                        wire:key="product-set-selector-{{ $product->id }}"--}}
                {{--                        :product="$product"--}}
                {{--                    />--}}
                {{--                @endif--}}

                <div class="md:flex md:space-x-4">
                    <livewire:add-to-cart :product="$product" />

                    <livewire:fire
                        button-classes="w-[48px] h-[48px] rounded-[12px] border border-black max-md:hidden"
                        :product="$product"
                        :isActive="true"
                    />
                </div>


                {{--     Аккордеон     --}}
                <div x-data="{ openAccordion: null }">
                    {{-- Аккордеон 1 --}}
                    <div class="w-full overflow-hidden mb-4 pb-4 border-b border-grey-200">
                        <div
                            class="cursor-pointer flex justify-between items-center"
                            @click="openAccordion = (openAccordion === 1 ? null : 1)"
                        >
                            <div>Описание</div>
                            <div>
                                <template x-if="openAccordion !== 1">
                                    <x-icons.desc-plus />
                                </template>
                                <template x-if="openAccordion === 1">
                                    <x-icons.desc-minus />
                                </template>
                            </div>
                        </div>

                        <div
                            x-show="openAccordion === 1"
                            x-collapse
                            x-data="{ text: @this.description }"
                            class="pt-3"
                        >
                            <div x-html="text"></div>

                            @if ($product->type === \App\Models\Product::TYPE_PRODUCT || $product->type === \App\Models\Product::TYPE_SET)
                                <div
                                    x-show="article"
                                    x-data="{
                        article: @this.selectedVariantArticle,
                        updateSelectedVariant() {
                            if (this.article != @this.selectedVariantArticle) {
                                this.article = @this.selectedVariantArticle;
                                window.dispatchEvent(new CustomEvent('initSidebarScroll'));
                            }
                        }
                    }"
                                    class="font-light mt-2 text-sm"
                                >
                                    <span>артикул:</span>
                                    <span x-text="article"></span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Аккордеон 2 --}}
                    <div class="w-full overflow-hidden mb-4 pb-4 border-b border-grey-200">
                        <div
                            class="cursor-pointer flex justify-between items-center"
                            @click="openAccordion = (openAccordion === 2 ? null : 2)"
                        >
                            <div>Уход и технологии</div>
                            <div>
                                <template x-if="openAccordion !== 2">
                                    <x-icons.desc-plus />
                                </template>
                                <template x-if="openAccordion === 2">
                                    <x-icons.desc-minus />
                                </template>
                            </div>
                        </div>

                        <div
                            x-show="openAccordion === 2"
                            x-collapse
                            class="pt-3"
                        >
                            <div>Ручная или машинная стирка до 30°С.<br>
                                Стирать с аналогичными цветами.<br>
                                Отжим до 600 оборотов.<br><br>

                                *Изделия изготовленные по технологии garment dyed могут незначительно отличаться по
                                цвету и узору варки.</div>
                        </div>
                    </div>

                    {{-- Аккордеон 3 --}}
                    <div class="w-full overflow-hidden mb-4 pb-4 border-b border-grey-200">
                        <div
                            class="cursor-pointer flex justify-between items-center"
                            @click="openAccordion = (openAccordion === 3 ? null : 3)"
                        >
                            <div>Доставка и возврат</div>
                            <div>
                                <template x-if="openAccordion !== 3">
                                    <x-icons.desc-plus />
                                </template>
                                <template x-if="openAccordion === 3">
                                    <x-icons.desc-minus />
                                </template>
                            </div>
                        </div>

                        <div
                            x-show="openAccordion === 3"
                            x-collapse
                            class="pt-3"
                            x-data="{
                update() {
                     window.dispatchEvent(new CustomEvent('initSidebarScroll'));
                   }
                }"
                            x-init="setInterval(() => update(), 1000);"
                        >
                            <div>
                                Заказ можно получить следующими способами:<br>
                                - СДЭК ПВЗ: доставка до пункта выдачи по России.<br><br>

                                - СДЭК Курьер: доставка до двери по России.
                            </div>
                        </div>
                    </div>

                    {{-- Аккордеон 4 --}}
                    <div class="w-full overflow-hidden mb-4 pb-4 border-b border-grey-200">
                        <div
                            class="cursor-pointer flex justify-between items-center"
                            @click="openAccordion = (openAccordion === 4 ? null : 4)"
                        >
                            <div>Задать вопрос</div>
                            <div>
                                <template x-if="openAccordion !== 4">
                                    <x-icons.desc-plus />
                                </template>
                                <template x-if="openAccordion === 4">
                                    <x-icons.desc-minus />
                                </template>
                            </div>
                        </div>

                        <div
                            x-show="openAccordion === 4"
                            x-collapse
                            class="pt-3"
                        >
                            <div>
                                <x-button-outlined target="_blank" href="https://t.me/bruler_support" square size="sm">
                                    <x-icons.telegram-icon />
                                </x-button-outlined>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center text-sm">
                            <x-icons.car />
                            <p class="ml-2">2-3 дня на доставку</p>
                        </div>

                        <div class="flex items-center text-sm">
                            <x-icons.detail.undo />
                            <p class="ml-2">14 дней на возврат</p>
                        </div>

                        <div class="flex items-center text-sm">
                            <x-icons.present-icon />
                            <p class="ml-2">Бесплатная доставка от 15 000 рублей</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Модальное окно -->
        <div x-cloak x-show="open" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title"
             role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <!-- This element is to trick the browser into centering the momain-bg px-4 pt-5 pb-4 sm:p-6 sm:pb-4dal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom main-bg text-left overflow-hidden shadow-xl transform transition-all lg:my-8 lg:align-middle lg:max-w-[1200px] lg:w-full">
                    <div class="main-bg px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="flex w-full gap-3">
                                <div class="@if (!$product->mockup) hidden @else hidden md:block @endif">
                                    <img class="w-full h-auto" src="\storage\{{ $product->mockup }}" />
                                </div>

                                <div class="w-full">
                                    <div class="flex w-full items-center">
                                        <div class="text !text-primary mr-auto">Размерная сетка</div>
                                        <div class="ml-auto">
                                            <x-icons.times class="cursor-pointer" @click="open = false" />
                                        </div>
                                    </div>

                                    <div class="button-text-letter !text-primary uppercase mt-2 md:mt-5">
                                        {{ $product->name }}
                                    </div>

                                    @php
                                        $hideHipGirth = collect($product->size_chart)->every(
                                            fn($size) => is_null($size['hip_girth']),
                                        );
                                        $hideChestGirth = collect($product->size_chart)->every(
                                            fn($size) => is_null($size['chest_girth']),
                                        );
                                        $hideWaistGirth = collect($product->size_chart)->every(
                                            fn($size) => is_null($size['waist_girth']),
                                        );
                                        $hideSleeveLength = collect($product->size_chart)->every(
                                            fn($size) => is_null($size['sleeve_length']),
                                        );
                                        $hideInnerSeamLength = collect($product->size_chart)->every(
                                            fn($size) => is_null($size['inner_seam_length']),
                                        );
                                        $hideProductLength = collect($product->size_chart)->every(
                                            fn($size) => is_null($size['product_length']),
                                        );
                                    @endphp

                                    <div class="mt-4 md:mt-8 flex gap-x-2 overflow-x-auto">
                                        <div
                                            class="@if (!$product->mockup) hidden @endif md:hidden max-w-[140px] flex-shrink-0">
                                            <img class="w-full h-auto" src="\storage\{{ $product->mockup }}" />
                                        </div>
                                        <table class="min-w-max w-full border-collapse">
                                            <thead>
                                            <tr>
                                                <th
                                                    class="button-text !text-gray text-center border-b-2 border-black px-2">
                                                    Размер</th>
                                                @if ($product->category->slug === 'pants')
                                                    @unless ($hideWaistGirth)
                                                        <th
                                                            class="button-text !text-gray text-center border-b-2 border-black px-2">
                                                            Обхват талии</th>
                                                    @endunless
                                                @else
                                                    @unless ($hideChestGirth)
                                                        <th
                                                            class="button-text !text-gray text-center border-b-2 border-black px-2">
                                                            Обхват груди</th>
                                                    @endunless
                                                @endif
                                                @unless ($hideHipGirth)
                                                    <th
                                                        class="button-text !text-gray text-center border-b-2 border-black px-2">
                                                        Обхват бедер</th>
                                                @endunless
                                                @if ($product->category->slug === 'pants')
                                                    @unless ($hideInnerSeamLength)
                                                        <th
                                                            class="button-text !text-gray text-center border-b-2 border-black px-2">
                                                            Длина по внутреннему шву</th>
                                                    @endunless
                                                @else
                                                    @unless ($hideSleeveLength)
                                                        <th
                                                            class="button-text !text-gray text-center border-b-2 border-black px-2">
                                                            Длина рукава</th>
                                                    @endunless
                                                @endif
                                                @unless ($hideProductLength)
                                                    <th
                                                        class="button-text !text-gray text-center border-b-2 border-black px-2">
                                                        Длина изделия</th>
                                                @endunless
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($product->size_chart as $size)
                                                <tr class="border-b-2 border-black">
                                                    <td class="button-text !text-gray text-center uppercase py-2">
                                                        {{ $size['size'] }}</td>
                                                    @if ($product->category->slug === 'pants')
                                                        @unless ($hideWaistGirth)
                                                            <td class="button-text !text-gray text-center py-2">
                                                                {{ $size['waist_girth'] ?? '-' }}</td>
                                                        @endunless
                                                    @else
                                                        @unless ($hideChestGirth)
                                                            <td class="button-text !text-gray text-center py-2">
                                                                {{ $size['chest_girth'] ?? '-' }}</td>
                                                        @endunless
                                                    @endif
                                                    @unless ($hideHipGirth)
                                                        <td class="button-text !text-gray text-center py-2">
                                                            {{ $size['hip_girth'] ?? '-' }}</td>
                                                    @endunless
                                                    @if ($product->category->slug === 'pants')
                                                        @unless ($hideInnerSeamLength)
                                                            <td class="button-text !text-gray text-center py-2">
                                                                {{ $size['inner_seam_length'] ?? '-' }}</td>
                                                        @endunless
                                                    @else
                                                        @unless ($hideSleeveLength)
                                                            <td class="button-text !text-gray text-center py-2">
                                                                {{ $size['sleeve_length'] ?? '-' }}</td>
                                                        @endunless
                                                    @endif
                                                    @unless ($hideProductLength)
                                                        <td class="button-text !text-gray text-center py-2">
                                                            {{ $size['product_length'] ?? '-' }}</td>
                                                    @endunless
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-4 md:mt-8 flex">
                                        <div class="small-text !text-gray mr-auto">размеры указаны в сантиметрах</div>
                                        <a href="{{ route('contacts') }}"
                                           class="ml-auto small-text underline !text-gray text-right">
                                            нужна помощь?
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($product->type != 'certificate')
        <div wire:ignore class="mt-4 md:mt-7">
            <x-recommendation-products :productId="$product->id" />
        </div>
    @endif
</div>


<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productDetailedSwiper', (payload) => ({
            open: false,

            initProduct() {
                let verticalGallery = null;
                let mainGallery = null;

                const slides = document.querySelectorAll('.swiper-slide-thumb');
                const slidesCount = slides.length;

                document.querySelectorAll('.swiper-slide-thumb video').forEach(video => {
                    video.removeAttribute('autoplay');
                    video.pause();
                    video.currentTime = 0; // Устанавливаем первый кадр
                    video.preload = 'auto';
                    video.addEventListener('play', (e) => {
                        e.preventDefault();
                        video.pause();
                    });
                });

                function playVideo(video) {
                    if (video) {
                        video.currentTime = 0;
                        video.addEventListener('canplay', function() {
                            video.play().catch(err => console.error('Ошибка воспроизведения:', err));
                        });
                    }
                }

                function stopAllVideos() {
                    document.querySelectorAll('#mainProduct video').forEach(video => {
                        video.pause();
                        video.currentTime = 0;
                    });
                }

                if (slidesCount > 1) {
                    verticalGallery = new Swiper('#thumbsProduct', {
                        direction: 'vertical',
                        slidesPerView: 4,
                        spaceBetween: 8,
                        mousewheel: true,
                        loop: slidesCount >= 4,

                        navigation: {
                            nextEl: '.swiper-button-next-thumb',
                            prevEl: '.swiper-button-prev-thumb',
                        },
                    });

                    selectVerticalSlide(0);

                    slides.forEach((slide, index) => {
                        slide.addEventListener('click', () => {
                            selectVerticalSlide(index);
                            if (mainGallery) {
                                mainGallery.slideToLoop(index);
                            }
                        });
                    });
                }

                if (document.querySelector('#mainProduct')) {
                    mainGallery = new Swiper('#mainProduct', {
                        slidesPerView: 1,
                        spaceBetween: 10,
                        loop: true,

                        scrollbar: {
                            el: '.swiper-scrollbar',
                            hide: false
                        },

                        on: {
                            init() {
                                selectVerticalSlide(0);
                                const initialVideo = slides[this.activeIndex].querySelector('video');
                                playVideo(initialVideo);
                            },
                            slideChange() {
                                // stopAllVideos();
                                const activeSlide = slides[this.activeIndex];
                                const activeVideo = activeSlide.querySelector('video');
                                playVideo(activeVideo);
                            },
                            slideChangeTransitionEnd() {
                                const activeSlide = slides[this.activeIndex];
                                const activeVideo = activeSlide.querySelector('video');
                                playVideo(activeVideo);
                            },
                            activeIndexChange() {
                                if (verticalGallery) {
                                    verticalGallery.slideToLoop(this.realIndex);
                                }
                                selectVerticalSlide(this.realIndex);
                            }
                        },
                    });

                    document.querySelectorAll('#mainProduct video').forEach(video => {
                        video.addEventListener('ended', function () {
                            this.currentTime = 0;
                            this.play().catch(err => console.log('Ошибка воспроизведения:', err));
                        });

                        video.setAttribute('playsinline', '');
                        video.setAttribute('muted', '');
                        video.setAttribute('loop', '');
                    });
                }

                function selectVerticalSlide(selectedIndex) {
                    slides.forEach((slide, index) => {
                        slide.classList.toggle('custom-swiper-slide-active', index === selectedIndex);
                    });
                }
            },

            init() {
                this.initProduct()
            },
        }));
    });
</script>
