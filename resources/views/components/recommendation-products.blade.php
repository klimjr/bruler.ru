<div {{ $attributes->merge(['class' => 'products']) }} {{ $attributes }}>
    <div class="text-[28px] mb-4 text-center">Собери свой образ</div>

    <div class="grid grid-cols-2 gap-y-4 gap-x-1 md:grid-cols-4 md:gap-y-2 md:gap-x-2 md:hidden" wire:ignore>
        @foreach ($products as $product)
            <div>
                @if ($product->show)
                    <livewire:product-card-redesign :product="$product" />
                @endif
            </div>
        @endforeach
    </div>

    <div
        x-data="recommendationProductsSwiper"
        x-init="initRecommendationSwiper"
        class="hidden md:block"
    >
        <div id="recommendationProductsSlider" class="swiper">
            <div class="swiper-wrapper">
                @foreach ($products as $product)
                    @if ($product->show)
                        <div class="swiper-slide">
                            <livewire:product-card-redesign :product="$product" />
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('recommendationProductsSwiper', () => ({
            swiperInstance: null,

            initRecommendationSwiper() {
                this.initSwiper()

                window.addEventListener('resize', this.throttle(this.initSwiper, 200));
            },

            initSwiper() {
                const screenWidth = window.innerWidth;

                if (screenWidth >= 768) {
                    if (!this.swiperInstance) {
                        this.swiperInstance = new Swiper('#recommendationProductsSlider', {
                            slidesPerView: 4,
                            spaceBetween: 8,
                        });
                    }
                } else {
                    if (this.swiperInstance) {
                        this.swiperInstance.destroy(true, true);
                        this.swiperInstance = null;
                    }
                }
            },

            throttle(func, limit) {
            let lastFunc;
            let lastRan;

            return function() {
                const context = this;
                const args = arguments;

                if (!lastRan) {
                    func.apply(context, args);
                    lastRan = Date.now();
                } else {
                    clearTimeout(lastFunc);
                    lastFunc = setTimeout(function() {
                        if (Date.now() - lastRan >= limit) {
                            func.apply(context, args);
                            lastRan = Date.now();
                        }
                    }, limit - (Date.now() - lastRan));
                }
            };
        }
        }));
    });
</script>
