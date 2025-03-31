<style>
    #ambassadors .swiper-slide {
        width: 80%;
    }

    #ambassadors .swiper-pagination-bullet-active {
        background: black;
    }

    #ambassadors .swiper-pagination {
        position: relative;
    }

    #ambassadors .swiper-pagination-bullet {
        width: 14px;
        height: 14px;
    }

    #ambassadors .swiper-button-prev,
    .swiper-button-next {
        position: relative;
    }

    #ambassadors .swiper-pagination-fraction,
    .swiper-pagination-custom,
    .swiper-horizontal>.swiper-pagination-bullets,
    .swiper-pagination-bullets.swiper-pagination-horizontal {
        top: auto;
        bottom: auto;
        width: auto;
    }
</style>

<div class="px-0 md:px-10" x-data="{
    swiper: null,
    initSwiper() {
        this.$nextTick(() => {
            this.swiper = new Swiper($refs.container, {
                slidesPerView: 3,
                spaceBetween: 30,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.go-next',
                    prevEl: '.go-prev',
                },
                breakpoints: {
                    0: {
                        slidesPerView: 'auto'
                    },
                    768: {
                        slidesPerView: 3,
                    }
                },
            });
        });
    }
}" x-init="initSwiper()">

    <div id="ambassadors" class="swiper" x-ref="container">
        <div class="swiper-wrapper">
            @foreach (App\Models\Ambassador::all() as $ambassador)
                <div class="swiper-slide w-[230px] md:w-[420px] h-full flex-shrink-0">
                    <img class="object-cover w-full h-[250px] md:h-[530px]" src="/storage/{{ $ambassador->image }}" />
                    <div class="text-xs md:text-xl mt-1 text-center">{{ $ambassador->name }}</div>
                </div>
            @endforeach
        </div>
        <div class="flex items-center justify-center gap-8 mt-5">
            <div class="go-prev">
                <svg width="10" height="17" viewBox="0 0 10 17" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path id="&#62;"
                        d="M8.08547 16.5L10 14.6226L3.7094 8.5L10 2.37738L8.08547 0.5L0 8.5L8.08547 16.5Z"
                        fill="black" />
                </svg>
            </div>

            <div class="swiper-pagination"></div>

            <div class="go-next">
                <svg width="10" height="17" viewBox="0 0 10 17" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path id="&#62;"
                        d="M1.91453 16.5L0 14.6226L6.2906 8.5L0 2.37738L1.91453 0.5L10 8.5L1.91453 16.5Z"
                        fill="black" />
                </svg>
            </div>
        </div>
    </div>

    {{-- <div class="flex w-full justify-center my-6 md:hidden">
        <div
            class="text-center underline cursor-pointer text-xs md:bg-black md:text-white md:no-underline md:px-12 md:py-3 md:rounded-xl">
            Все амбассадоры
        </div>
    </div> --}}
</div>
