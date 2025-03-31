<div
    x-data="fashionProductsSwiper"
    x-init="initRecommendationSwiper"
    x-ref="sliderContainer"
    id="slider-container"
    class="relative w-full overflow-hidden hidden md:block"
>
    <div x-ref="slider" id="slider" class="flex gap-2 transition-transform duration-500 ease-in-out">
        @foreach ($products as $product)
            <div class="slider-slide">
                <livewire:product-card-redesign
                    :product="$product"
                    :isActive="true"
                    sizeContainer="md:h-[500px] md:w-[354px]"
                    sizeLink="md:h-[500px] md:w-[354px]"
                />
            </div>
        @endforeach
    </div>
</div>

<style>
    .slider-slide {
        transform: translateZ(0);
        backface-visibility: hidden;
        flex-shrink: 0;
        height: 100%;
        position: relative;
        transition-property: transform;
        pointer-events: auto !important;
        user-select: auto !important;
    }

    .slider-slide a {
        pointer-events: auto !important; /* Включите обработку событий */
        user-select: auto !important;
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('fashionProductsSwiper', () => ({
            eventHandlers: null,

            initRecommendationSwiper() {
                this.initSlider()

                window.addEventListener('resize', this.throttle(this.initSlider.bind(this), 200));
            },

            initSlider() {
                const screenWidth = window.innerWidth;

                if (screenWidth >= 768) {
                    this.sliderHandler()
                } else if (screenWidth < 768 && this.eventHandlers !== null) {
                    this.destroySlider()
                }
            },

            sliderHandler() {
                if (this.eventHandlers && Object.values(this.eventHandlers).some((handler) => handler !== null)) {
                    return; // Обработчики уже добавлены
                }

                const sliderContainer = this.$refs.sliderContainer;
                const slider = this.$refs.slider;

                let isDragging = false;
                let startX = 0;
                let currentTranslate = 0;
                let prevTranslate = 0;
                let currentIndex = 0;
                const cardWidth = 354 + 8;
                let hasMoved = false;
                let activeLink = null;

                // Массив для хранения обработчиков
                this.eventHandlers = {
                    mouseMove: null,
                    mouseDown: null,
                    mouseUp: null,
                    mouseLeave: null,
                    touchMove: null,
                    touchEnd: null,
                    sliderClick: null,
                };

                // Обработчики событий

                const handleMouseDown = (e) => {
                    if (e.button !== 0) return;

                    isDragging = true;
                    startX = e.clientX;
                    slider.style.transition = 'none';
                    hasMoved = false;

                    // Проверяем, если событие начато на ссылке
                    activeLink = e.target.closest('a') || null;

                    if (activeLink) {
                        e.preventDefault()
                    }
                };

                const handleMouseMove = (e) => {
                    if (!isDragging) return;
                    const deltaX = e.clientX - startX;
                    currentTranslate = prevTranslate + deltaX;
                    slider.style.transform = `translateX(${currentTranslate}px)`;
                    if (Math.abs(deltaX) > 5) {
                        hasMoved = true;
                    }
                };

                const handleMouseUp = () => {
                    if (!isDragging) return;
                    isDragging = false;
                    finalizeDrag();
                };

                const handleMouseLeave = () => {
                    if (!isDragging) return;
                    isDragging = false;
                    finalizeDrag();
                };

                const handleTouchStart = (e) => {
                    isDragging = true;
                    startX = e.touches[0].clientX;
                    slider.style.transition = 'none';
                    hasMoved = false;

                    // Проверяем, если событие начато на ссылке
                    activeLink = e.target.closest('a') || null;
                    if (activeLink) {
                        e.preventDefault()
                    }
                };

                const handleTouchMove = (e) => {
                    if (!isDragging) return;
                    const deltaX = e.touches[0].clientX - startX;
                    currentTranslate = prevTranslate + deltaX;
                    slider.style.transform = `translateX(${currentTranslate}px)`;
                    if (Math.abs(deltaX) > 5) {
                        hasMoved = true;
                    }
                };

                const handleTouchEnd = () => {
                    if (!isDragging) return;
                    isDragging = false;
                    finalizeDrag();
                };

                const handleClick = (e) => {
                    if (hasMoved) {
                        e.preventDefault();
                    }
                };

                const finalizeDrag = () => {
                    const threshold = 50;
                    const moveBy = Math.round(currentTranslate / -cardWidth);

                    if (Math.abs(currentTranslate - prevTranslate) > threshold) {
                        currentIndex = moveBy > currentIndex ? currentIndex + 1 : currentIndex - 1;
                    }

                    currentIndex = Math.max(0, Math.min(currentIndex, slider.children.length - 1));

                    prevTranslate = -currentIndex * cardWidth;
                    slider.style.transition = 'transform 0.5s ease-in-out';
                    slider.style.transform = `translateX(${prevTranslate}px)`;
                };

                // Назначение обработчиков
                this.eventHandlers.mouseDown = handleMouseDown;
                this.eventHandlers.mouseMove = handleMouseMove;
                this.eventHandlers.mouseUp = handleMouseUp;
                this.eventHandlers.mouseLeave = handleMouseLeave;
                this.eventHandlers.touchStart = handleTouchStart;
                this.eventHandlers.touchMove = handleTouchMove;
                this.eventHandlers.touchEnd = handleTouchEnd;
                this.eventHandlers.sliderClick = handleClick;

                // Добавление обработчиков
                slider.addEventListener('mousedown', handleMouseDown);
                slider.addEventListener('mousemove', handleMouseMove);
                slider.addEventListener('mouseup', handleMouseUp);
                slider.addEventListener('mouseleave', handleMouseLeave);
                slider.addEventListener('touchstart', handleTouchStart);
                slider.addEventListener('touchmove', handleTouchMove);
                slider.addEventListener('touchend', handleTouchEnd);
                slider.addEventListener('click', handleClick);

                sliderContainer.querySelectorAll('.slider-slide a').forEach((link) => {
                    link.addEventListener('click', handleClick);
                });
            },

            removeSliderHandlers() {
                if (!this.eventHandlers) return

                const sliderContainer = this.$refs.sliderContainer;
                const slider = this.$refs.slider

                slider.removeEventListener('mousedown', this.eventHandlers.mouseDown);
                slider.removeEventListener('mousemove', this.eventHandlers.mouseMove);
                slider.removeEventListener('mouseup', this.eventHandlers.mouseUp);
                slider.removeEventListener('mouseleave', this.eventHandlers.mouseLeave);
                slider.removeEventListener('touchstart', this.eventHandlers.touchStart);
                slider.removeEventListener('touchmove', this.eventHandlers.touchMove);
                slider.removeEventListener('touchend', this.eventHandlers.touchEnd);
                // slider.removeEventListener('click', this.eventHandlers.sliderClick);

                sliderContainer.querySelectorAll('.slider-slide a').forEach((link) => {
                    link.removeEventListener('click', this.eventHandlers.sliderClick);
                });
                // Очищаем ссылки
                this.eventHandlers = null;
            },

            destroySlider() {
                this.removeSliderHandlers();
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
            },
        }));
    });
</script>
