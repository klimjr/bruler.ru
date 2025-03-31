<div class="relative" x-data='{
            images: {{ json_encode($images) }},
            selectedImg: null,
            selectedImgIndex: 1,
            loopIndex: 1,
            swiper: null,
            swiper_mobile: new Swiper($refs.container2, {
                    slidesPerView: 1,
                    loop: true,
                }),
            isZooming: false,
            isZoomingSelectedImg: false,
            openZoom(index) {
                this.swiper = new Swiper($refs.container, {
                    slidesPerView: 1,
                    loopAddBlankSlides: false,
                    loop: true,
                    on: {
                        slideChange: function () {
                            let realIndex = this.realIndex + 1
                            $refs.container.setAttribute("data-selected-img-index", realIndex);
                        },
                    }
                })
                if (!$refs.container.dataset.loopIndex) $refs.container.setAttribute("data-loop-index", 1)
                this.swiper.slideTo(index - $refs.container.dataset.loopIndex, 0, false)
                $refs.container.setAttribute("data-selected-img-index", index)
                this.isZooming = true
                this.selectedImg = this.images[index]
                this.selectedImgIndex = index
                document.body.style.overflow = "hidden"
            },
            openZoomSelectedImg() {
                this.isZoomingSelectedImg = true;
                this.selectedImgIndex = $refs.container.dataset.selectedImgIndex
                document.addEventListener("mousemove", this.moveZoomedImage);
            },
            closeZoom() {
                this.isZooming = false
                this.isZoomingSelectedImg = false;
                document.body.style.overflow = "auto"
            },
            closeZoomSelectedImg() {
                this.isZoomingSelectedImg = false;
                document.removeEventListener("mousemove", this.moveZoomedImage);
            },
            moveZoomedImage(event) {
                const cursorPosition = {
                  x: event.clientX,
                  y: event.clientY
                };

                const offset = {
                  y: cursorPosition.y - window.innerHeight / 2
                };

                let el = document.getElementById("zoomedImage" + $refs.container.dataset.selectedImgIndex)

                if (offset.y >= 0) el.style.transform = `translateY(${-Math.abs(offset.y)}px) scale(1.35)`;
                else el.style.transform = `translateY(${Math.abs(offset.y)}px) scale(1.35)`;
              },
              nextSlideFn() {
                this.swiper.slideNext()
                this.isZoomingSelectedImg = false
                this.selectedImgIndex = $refs.container.dataset.selectedImgIndex
              },
              prevSlideFn() {
                this.swiper.slidePrev()
                this.isZoomingSelectedImg = false
                this.selectedImgIndex = $refs.container.dataset.selectedImgIndex
              },
              nextSlideFnMobile() {
                this.swiper_mobile.slideNext()
              },
              prevSlideFnMobile() {
                this.swiper_mobile.slidePrev()
              }
        }'>
    <div class="hide_in_mobile">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mx-4 justify-center">
            <template x-for="(image, index) in images">
                <div class="cursor-zoom-in" @click="openZoom(index)">
                    <img alt="слайдер" :src="image" class="col-span-1 w-full h-full object-cover" />
                </div>
            </template>
        </div>
    </div>
    <div class="hide_in_desktop">
        <div class="swiper" x-ref="container2">
            <div class="swiper-wrapper">
                <template x-for="(image, index) in images">
                    <div class="swiper-slide my-auto">
                        <img class="h-screen w-screen object-contain" alt="слайдер" :src="image"/>
                    </div>
                </template>
            </div>
        </div>
        <div class="product-image-slider-actions-mobile">
            <button type="button" class="absolute right-2" @click="nextSlideFnMobile()">
                <x-icons.arrow-slider-right />
            </button>
            <button type="button" class="absolute left-2" style="transform: rotate(180deg)" @click="prevSlideFnMobile()">
                <x-icons.arrow-slider-left />
            </button>
        </div>
    </div>
    <div x-show="isZooming" :style="{opacity: isZooming ? '100' : '0'}" class="fixed top-0 left-0 h-screen w-screen bg-white z-40 opacity-0" >
        <button class="absolute top-4 right-4 z-[41]" @click="closeZoom()">
            <x-icons.close/>
        </button>
        <button x-show="isZoomingSelectedImg" class="absolute top-4 z-[41] right-16" @click="closeZoomSelectedImg()">
            <x-icons.zoomOut/>
        </button>
        <button x-show="!isZoomingSelectedImg" class="absolute top-4 z-[41] right-16 hidden md:block" @click="openZoomSelectedImg()">
            <x-icons.zoomIn/>
        </button>
        <div class="relative">
            <div class="product-image-slider-actions-mobile">
                <button type="button" class="absolute right-2" @click="nextSlideFn()">
                    <x-icons.arrow-slider-right />
                </button>
                <button type="button" class="absolute left-2" style="transform: rotate(180deg)" @click="prevSlideFn()">
                    <x-icons.arrow-slider-left />
                </button>
            </div>
            <div class="swiper" x-ref="container">
                <div class="swiper-wrapper">
                    <template x-for="(image, index) in images">
                        <div class="swiper-slide bg-white">
                            <img :style="{ opacity: selectedImgIndex == index ? '100' : '0' }" @click="closeZoomSelectedImg()" x-show="isZoomingSelectedImg" class="block w-screen h-screen object-contain cursor-zoom-out" alt="слайдер" :src="image" :id="'zoomedImage' + index" @mousemove="moveZoomedImage"/>
                            <img x-show="!isZoomingSelectedImg" class="w-screen h-screen object-contain z-10 cursor-zoom-in block md:hidden" alt="слайдер" :src="image"/>
                            <img @click="openZoomSelectedImg()" x-show="!isZoomingSelectedImg" class="w-screen h-screen object-contain z-10 cursor-zoom-in hidden md:block" alt="слайдер" :src="image"/>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
