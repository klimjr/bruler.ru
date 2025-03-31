<div class="custom-container mx-auto px-0 md:px-[50px] pt-[55px] md:pt-[88px]" x-data="{
initProduct() {
    let verticalGallery = null;
    let mainGallery = null;
    const slides = document.querySelectorAll('.vertical-gallery .swiper-slide');
    const slidesCount = slides.length;

    document.querySelectorAll('.vertical-gallery video').forEach(video => {
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
        if (video && !video.closest('.vertical-gallery')) {
            video.currentTime = 0;
            video.play().catch(err => console.log('Ошибка воспроизведения:', err));
        }
    }

    function stopAllVideos() {
        document.querySelectorAll('.main-gallery video').forEach(video => {
            video.pause();
            video.currentTime = 0;
        });
    }

    if (slidesCount > 0) {
        verticalGallery = new Swiper('.vertical-gallery', {
            direction: 'vertical',
            slidesPerView: Math.min(slidesCount, 4),
            spaceBetween: 30,
            mousewheel: true,
            loop: slidesCount >= 4,

            navigation: {
                nextEl: '.swiper-button-bottom',
                prevEl: '.swiper-button-top',
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

    if (document.querySelector('.main-gallery')) {
        mainGallery = new Swiper('.main-gallery', {
            zoom: true,
            loop: true,

            navigation: {
                nextEl: '.swiper-button-next-main',
                prevEl: '.swiper-button-prev-main',
            },

            scrollbar: {
                el: '.swiper-scrollbar',
                hide: false
            },

            on: {
                init: function() {
                    selectVerticalSlide(0);
                    const initialVideo = this.slides[this.activeIndex].querySelector('video');
                    playVideo(initialVideo);
                },
                slideChange: function () {
                    stopAllVideos();
                    const activeSlide = this.slides[this.activeIndex];
                    const activeVideo = activeSlide.querySelector('video');
                    playVideo(activeVideo);
                },
                slideChangeTransitionEnd: function() {
                    const activeSlide = this.slides[this.activeIndex];
                    const activeVideo = activeSlide.querySelector('video');
                    playVideo(activeVideo);
                },
                activeIndexChange: function() {
                    if (verticalGallery) {
                        verticalGallery.slideToLoop(this.realIndex);
                    }
                    selectVerticalSlide(this.realIndex);
                }
            },
        });

        document.querySelectorAll('.main-gallery video').forEach(video => {
            video.addEventListener('ended', function() {
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
            slide.style.opacity = index === selectedIndex ? '1' : '0.5';
        });
    }
}
}"
    x-init="initProduct">

    <style>
        .swiper-button-prev:after,
        .swiper-button-next:after {
            font-weight: 900;
            font-size: 20px;
        }

        .swiper-scrollbar {
            background: #F2F2F4;
            border-radius: 0px;
        }

        .swiper-scrollbar-drag {
            background: black;
            border-radius: 0px;
        }

        .swiper-horizontal>.swiper-scrollbar,
        .swiper-scrollbar.swiper-scrollbar-horizontal {
            height: 5px;
        }

        .vertical-gallery .custom-swiper-slide-active {
            border-width: 2px;
            opacity: 1;
        }
    </style>

    <div wire:ignore class="flex flex-col md:flex-row gap-x-20" x-data="{ open: false }">
        @if ($product->type !== \App\Models\Product::TYPE_CERTIFICATE && count($gallery) > 1)
            <div class="relative hidden md:block">
                <div class="swiper vertical-gallery max-h-[700px]">
                    <div class="swiper-wrapper !w-[120px]">
                        @foreach ($gallery as $index => $image)
                        <div class="swiper-slide w-full !h-40 opacity-50 cursor-pointer">
                            @if (strpos($image['image'], '.mp4') !== false)
                                <video class="preview-video w-full h-full object-cover"
                                       preload="auto"
                                       muted
                                       playsinline>
                                    <source src="{{ $image['image'] }}" type="video/mp4">
                                </video>
                            @else
                                <img class="absolute inset-0 w-full h-full object-cover"
                                     alt="{{ $image['alt'] ?? $product->name }}"
                                     src="{{ $image['image'] }}"
                                     x-cloak />  @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="absolute inset-0 pointer-events-none max-h-[700px]">
                    <div
                        class="swiper-button-prev swiper-button-top !text-[#00000033] rotate-90 !w-full !left-auto !-top-10 !m-0 !pointer-events-auto !z-10">
                    </div>
                    <div
                        class="swiper-button-next swiper-button-bottom !text-[#00000033] rotate-90 !w-full !right-auto !top-auto !-bottom-10 !m-0 !pointer-events-auto !z-10">
                    </div>
                </div>
            </div>
        @endif

        <div class="relative md:max-w-[40%]">
            @if ($product->type === \App\Models\Product::TYPE_CERTIFICATE)
                <img alt="{{ $certificate['alt'] ?? $product->name }}" src="{{ $certificate['image'] }}" />
            @else
                @if (count($gallery) > 1)
                    <div class="swiper main-gallery">
                        <div class="swiper-wrapper">
                            @foreach ($gallery as $index => $image)
                                <div class="swiper-slide">
                                    <div class="swiper-zoom-container">
                                        @if (strpos($image['image'], '.mp4') !== false)
                                            <video src="{{ $image['image'] }}" muted autoplay />
                                        @else
                                        <img src="{{ $image['image'] }}" />
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="swiper-scrollbar !bottom-0 md:hidden"></div>
                    </div>

                    <div class="absolute inset-0 pointer-events-none hidden md:block">
                        <div
                            class="swiper-button-prev swiper-button-prev-main !text-[#00000033] !-left-10 !pointer-events-auto !z-10">
                        </div>
                        <div
                            class="swiper-button-next swiper-button-next-main !text-[#00000033] !-right-10 !pointer-events-auto !z-10">
                        </div>
                    </div>
                @else
                    <div class="swiper-slide">
                        <div class="swiper-zoom-container">
                            @if (strpos($gallery[0]['image'], '.mp4') !== false)
                                <video
                                    playsinline
                                    muted
                                    loop
                                    class="w-full h-full object-cover">
                                    <source src="{{ $gallery[0]['image'] }}" type="video/mp4">
                                </video>
                            @else
                                <img src="{{ $gallery[0]['image'] }}" />
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <div class="mt-[25px] md:mt-0 px-2.5 md:px-0 w-full">
            <div id="sidebar" wire:key="{{ rand() }}">
                <div id="product_info" class="sidebar__inner">
                    <div class="flex flex-col justify-between md:justify-start items-start">
                        <div class="flex flex-col w-full">
                            <div class="flex justify-between text-xl font-normal text-primary md:!text-3xl">
                                <div>{{ $product->name_en }}</div>
                            </div>
                            <div class="text-xs md:text-sm md:mt-3 font-medium text-[#757575]">
                                {{ $product->name }}
                            </div>
                        </div>

                        <style>
                            .digi-dolyame-button {
                                border-radius: 12.5px;
                            }

                            .digi-dolyame-button--5 {
                                border: 2px solid black;
                            }

                            .digi-dolyame-button--5:hover {
                                border: 2px solid black;
                            }
                        </style>

                        <div class="flex items-baseline !mt-0 gap-x-5">
                            <div class="price-big !font-medium !text-3xl mt-[25px] mb-[15px] md:my-5 whitespace-nowrap">
                                @if ($product->type === \App\Models\Product::TYPE_CERTIFICATE)
                                    <span x-data="{
                                        productPrice: @this.price,
                                        updateProductPrice() {
                                            if (this.productPrice != @this.price) {
                                                this.productPrice = @this.price;
                                                window.dispatchEvent(new CustomEvent('initSidebarScroll'));
                                            }
                                        }
                                    }" x-init="setInterval(() => updateProductPrice(), 1000);" x-text="productPrice"></span>
                                    ₽
                                @else
                                    @if (isset($product->discount) && $product->discount != 0)
                                        <div class="flex items-center space-x-1 justify-center">
                                            <span
                                                class="!text-[#D0021B]">{{ number_format($product->getDiscountedPrice(), 0, '.', '.') }}₽</span>
                                        </div>
                                    @else
                                        {{ number_format($product->price, 0, '.', '.') }}₽
                                    @endif
                                @endif
                            </div>

                            @if (isset($product->discount) && $product->discount != 0)
                                <div
                                    class="price-big !font-medium !text-3xl flex items-center space-x-1 justify-center whitespace-nowrap">
                                    <s>{{ number_format($product->price, 0, '.', '.') }}₽</s>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="h-32">
                        <script src="https://pay.yandex.ru/sdk/v1/pay.js" onload="onYaPayLoad()" async></script>
                        <script>
                            function onYaPayLoad() {
                                const YaPay = window.YaPay;

                                // Данные платежа
                                const paymentData = {
                                    // Для отладки нужно явно указать `SANDBOX` окружение,
                                    // для продакшена параметр можно убрать или указать `PRODUCTION`
                                    env: YaPay.PaymentEnv.Sandbox,

                                    // Версия 4 указывает на тип оплаты сервисом Яндекс Пэй
                                    // Пользователь производит оплату на форме Яндекс Пэй,
                                    // и мерчанту возвращается только результат проведения оплаты
                                    version: 4,

                                    // Код валюты в которой будете принимать платежи
                                    currencyCode: YaPay.CurrencyCode.Rub,

                                    // Идентификатор продавца, который получают при регистрации в Яндекс Пэй
                                    merchantId: '{{ env('YANDEX_CLIENT_ID') }}',

                                    // Сумма к оплате
                                    // Сумма которая будет отображена на форме зависит от суммы переданной от бэкенда
                                    // Эта сумма влияет на отображение доступности Сплита
                                    totalAmount: '15980.00',

                                    // Доступные для использования методы оплаты
                                    // Доступные на форме способы оплаты также зависят от информации переданной от бэкенда
                                    // Данные передаваемые тут влияют на внешний вид кнопки или виджета
                                    availablePaymentMethods: ['CARD', 'SPLIT'],
                                };

                                // Обработчик на клик по кнопке
                                // Функция должна возвращать промис которые резолвит ссылку на оплату полученную от бэкенда Яндекс Пэй
                                // Подробнее про создание заказа: https://pay.yandex.ru/ru/docs/custom/backend/yandex-pay-api/order/merchant_v1_orders-post
                                async function onPayButtonClick() {
                                    // Создание заказа...
                                    // и возврат URL на оплату вида 'https://pay.ya.ru/l/XXXXXX'
                                }

                                // Обработчик на ошибки при открытии формы оплаты
                                function onFormOpenError(reason) {
                                    // Выводим информацию о недоступности оплаты в данный момент
                                    // и предлагаем пользователю другой способ оплаты.
                                    console.error(`Payment error — ${reason}`);
                                }

                                // Создаем платежную сессию
                                YaPay.createSession(paymentData, {
                                    onPayButtonClick: onPayButtonClick,
                                    onFormOpenError: onFormOpenError,
                                })
                                    .then(function (paymentSession) {
                                        // Показываем кнопку Яндекс Пэй на странице.
                                        paymentSession.mountButton(document.querySelector('#button_container'), {
                                            type: YaPay.ButtonType.Pay,
                                            theme: YaPay.ButtonTheme.Black,
                                            width: YaPay.ButtonWidth.Auto,
                                        });
                                    })
                                    .catch(function (err) {
                                        // Не получилось создать платежную сессию.
                                    });
                            }


                        </script>
                        <yandex-pay-badge
                            merchant-id="{{ env('YANDEX_CLIENT_ID') }}"
                            type="bnpl"
                            amount="1000.00"
                            size="s"
                            variant="detailed"
                            theme="light"
                            align="left"
                            color="transparent"
                        />
                    </div>

                    @if ($product->type === \App\Models\Product::TYPE_PRODUCT)
                        <div class="text-sm md:text-xl font-semibold md:font-medium">Доступные размеры:</div>

                        <div class="flex items-center gap-1 my-3" x-data="{
                            indicatorColor: @this.indicatorColor,
                            indicatorText: @this.indicatorText,
                            updateIndicator() {
                                if (this.indicatorColor != @this.indicatorColor || this.indicatorText != @this.indicatorText) {
                                    this.indicatorColor = @this.indicatorColor;
                                    this.indicatorText = @this.indicatorText;
                                }
                            }
                        }" x-init="setInterval(() => updateIndicator(), 1000);">
                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="6.5" cy="6.5" r="6.5" :fill="indicatorColor"
                                    fill-opacity="0.3" />
                            </svg>

                            <div class="text-xs md:text-sm" :style="'color: ' + indicatorColor" x-text="indicatorText">
                            </div>
                        </div>

                        <livewire:product-variant-selector :selected-color="$color"
                            wire:key="product-variant-selector-{{ $product->id }}" :product="$product" />
                        <div>
                            <div @click="open = true"
                                class="text-xs md:text-sm text-[#757575] mt-2 underline cursor-pointer">
                                Размерная сетка</div>
                        </div>
                    @endif


                    @if ($product->type === \App\Models\Product::TYPE_CERTIFICATE)
                        <livewire:product-certificate-selector :selected-certificate="$certificate"
                            wire:key="product-certificate-selector-{{ $product->id }}" :product="$product" />
                    @endif

                    @if ($product->type === \App\Models\Product::TYPE_SET)
                        <livewire:product-set-selector :selected-color="$color"
                            wire:key="product-set-selector-{{ $product->id }}" :product="$product" />
                    @endif

                    <div>
                        <livewire:add-to-cart :product="$product" />
                    </div>

                    <div x-data="{ isOpen: false }">
                        <div class="mt-5 cursor-pointer flex justify-between items-center h-[50px] border-[#F2F2F4] border-t-[1.5px]"
                            @click="isOpen = !isOpen">
                            <div class="text text-xl !text-primary">Описание</div>
                            <div>
                                <template x-if="!isOpen">
                                    <x-icons.desc-plus />
                                </template>
                                <template x-if="isOpen">
                                    <x-icons.desc-minus />
                                </template>
                            </div>
                        </div>

                        <div class="small-text mb-4 mt-1.5 !text-primary overflow-hidden border-[#F2F2F4] border-b-[1.5px] transition-max-height duration-500 ease-in-out"
                            :style="isOpen ? 'max-height: 1000px;' : 'max-height: 0px;'" x-data="{
                                text: @this.description
                            }">
                            <div x-html="text"></div>

                            @if ($product->type === \App\Models\Product::TYPE_PRODUCT || $product->type === \App\Models\Product::TYPE_SET)
                                <div style="color: #626262" class="font-light mt-2 text-sm" x-show="article"
                                    x-data="{
                                        article: @this.selectedVariantArticle,
                                        updateSelectedVariant() {
                                            if (this.article != @this.selectedVariantArticle) {
                                                this.article = @this.selectedVariantArticle;
                                                window.dispatchEvent(new CustomEvent('initSidebarScroll'));
                                            }
                                        }
                                    }" x-init="setInterval(() => updateSelectedVariant(), 1000);">
                                    <span>артикул:</span>
                                    <span x-text="article"></span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($product->type === \App\Models\Product::TYPE_PRODUCT || $product->type === \App\Models\Product::TYPE_SET)
                        <div x-data="{ isOpen: false }">
                            <div class="mt-5 cursor-pointer flex justify-between items-center h-[50px] border-[#F2F2F4] border-t-[1.5px]"
                                @click="isOpen = !isOpen">
                                <div class="text text-xl !text-primary">Уход и технологии</div>
                                <div>
                                    <template x-if="!isOpen">
                                        <x-icons.desc-plus />
                                    </template>
                                    <template x-if="isOpen">
                                        <x-icons.desc-minus />
                                    </template>
                                </div>
                            </div>

                            <div class="small-text mb-4 mt-1.5 !text-primary overflow-hidden border-[#F2F2F4] border-b-[1.5px] transition-max-height duration-500 ease-in-out"
                                :style="isOpen ? 'max-height: 1000px;' : 'max-height: 0px;'">
                                <div>Ручная или машинная стирка до 30°С.<br>
                                    Стирать с аналогичными цветами.<br>
                                    Отжим до 600 оборотов.<br><br>

                                    *Изделия изготовленные по технологии garment dyed могут незначительно отличаться по
                                    цвету и узору варки.</div>
                            </div>
                        </div>

                        <div x-data="{ isOpen: false }">
                            <div class="mt-5 cursor-pointer flex justify-between items-center h-[50px] border-[#F2F2F4] border-t-[1.5px]"
                                @click="isOpen = !isOpen">
                                <div class="text text-xl !text-primary">Доставка и возврат</div>
                                <div>
                                    <template x-if="!isOpen">
                                        <x-icons.desc-plus />
                                    </template>
                                    <template x-if="isOpen">
                                        <x-icons.desc-minus />
                                    </template>
                                </div>
                            </div>

                            <div class="small-text mb-4 mt-1.5 !text-primary overflow-hidden border-[#F2F2F4] border-b-[1.5px] transition-max-height duration-500 ease-in-out"
                                :style="isOpen ? 'max-height: 1000px;' : 'max-height: 0px;'" x-data="{
                                    update() {
                                        window.dispatchEvent(new CustomEvent('initSidebarScroll'));
                                    }
                                }"
                                x-init="setInterval(() => update(), 1000);">
                                <div>Заказ можно получить следующими способами:<br>
                                    - СДЭК ПВЗ: доставка до пункта выдачи по России.<br><br>

                                    - СДЭК Курьер: доставка до двери по России.</div>

                                <div
                                    class="mt-2 flex flex-col space-x-0 md:space-x-[30px] space-y-2.5 md:space-y-0 md:flex-row items-center justify-center text-xs text-center md:hidden">
                                    <div class="flex flex-col items-center">
                                        <x-icons.detail.cart />
                                        <div>2-3 дня на доставку</div>
                                    </div>

                                    <div class="flex flex-col items-center">
                                        <x-icons.detail.undo />
                                        <div>14 дней на возврат</div>
                                    </div>

                                    <div class="flex flex-col items-center">
                                        <x-icons.detail.bag />
                                        <div>Бесплатная доставка<br>от 15 000 рублей</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex-col space-x-0 md:space-x-[30px] space-y-2.5 md:space-y-0 md:flex-row items-center justify-center text-xs text-center hidden md:flex">
                            <div class="flex flex-col items-center">
                                <x-icons.detail.cart />
                                <div>2-3 дня на доставку</div>
                            </div>

                            <div class="flex flex-col items-center">
                                <x-icons.detail.undo />
                                <div>14 дней на возврат</div>
                            </div>

                            <div class="flex flex-col items-center">
                                <x-icons.detail.bag />
                                <div>Бесплатная доставка<br>от 15 000 рублей</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

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
        <x-recommendation-products :productId="$product->id" />
    @endif
</div>
