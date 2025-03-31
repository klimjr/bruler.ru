<div x-data="{
    timerTimestamp: @json($timerTimestamp),
    days: '00',
    hours: '00',
    minutes: '00',
    seconds: '00',
    init() {
        if (this.timerTimestamp) {
            this.updateCountdown();
            setInterval(() => this.updateCountdown(), 1000);
        }
    },
    updateCountdown() {
        const now = Math.floor(Date.now() / 1000);
        const timeLeft = this.timerTimestamp - now;
        if (timeLeft <= 0) {
            this.days = this.hours = this.minutes = this.seconds = '00';
            return;
        }
        const days = Math.floor(timeLeft / 86400);
        const hours = Math.floor((timeLeft % 86400) / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;

        this.days = String(days).padStart(2, '0');
        this.hours = String(hours).padStart(2, '0');
        this.minutes = String(minutes).padStart(2, '0');
        this.seconds = String(seconds).padStart(2, '0');
    }
}"
     x-init="init()"
    :class="isShowRunningTexts ? 'h-[calc(100vh-80px)] md:h-[calc(100vh-88px)]' : 'h-[calc(100vh-48px)] md:h-[calc(100vh-56px)]'"
    class="relative -mt-1 md:-mt-2"
>

    @if (isset($main->banner) || isset($main->banner_mobile) || isset($main->video) || isset($main->video_mobile))
        @if (isset($main->banner) || isset($main->banner_mobile))
            {{-- Desktop Image --}}
            @if (isset($main->banner))
                <img class="w-full h-full object-cover max-lg:hidden"
                     src="/storage/{{ $main->banner }}"
                     alt="Desktop Banner"/>
            @endif

            {{-- Mobile Image --}}
            @if (isset($main->banner_mobile))
                <img class="w-full h-full object-cover lg:hidden"
                     src="/storage/{{ $main->banner_mobile }}"
                     alt="Mobile Banner"/>
            @endif
        @endif

        @if (isset($main->video) || isset($main->video_mobile))
            {{-- Desktop Video --}}
            @if (isset($main->video))
                <video class="w-full h-full object-cover max-lg:hidden" autoplay muted loop playsinline>
                    <source src="/storage/{{ $main->video }}" type="video/mp4">
                </video>
            @endif

            {{-- Mobile Video --}}
            @if (isset($main->video_mobile))
                <video class="w-full h-full object-cover lg:hidden" autoplay muted loop playsinline>
                    <source src="/storage/{{ $main->video_mobile }}" type="video/mp4">
                </video>
            @endif
        @endif
    @endif
    <div class="absolute inset-0 bg-[#0000000F]"></div>

    <div
        class="absolute top-0 w-full font-normal"
        :class="isShowRunningTexts ? 'h-[calc(100vh-80px)] md:h-[calc(100vh-88px)]' : 'h-[calc(100vh-48px)] md:h-[calc(100vh-56px)]'"
    >
        @if ($main->timer)
            <div class="h-full flex items-end justify-center">
                <div class="flex bg-[#131313] shadow-timer px-[10px] py-[24px] rounded-[14px] mb-[32px] md:mb-[42px]">
                    <div class="flex items-center flex-col px-[16px] md:px-[24px] border-r-[0.5px] border-[#F7F7F7]">
                        <p x-text="days" class="font-semibold text-white text-[16px] md:text-[38px] md:leading-[39px]">
                            00
                        </p>
                        <span class="text-[#999999] text-xs md:text-sm">Дней</span>
                    </div>

                    <div class="flex items-center flex-col px-[16px] md:px-[24px] border-r-[0.5px] border-[#F7F7F7]">
                        <p x-text="hours" class="font-semibold text-white text-[16px] md:text-[38px] md:leading-[39px]">
                            00
                        </p>
                        <span class="text-[#999999] text-xs md:text-sm">Часов</span>
                    </div>

                    <div class="flex items-center flex-col px-[16px] md:px-[24px] border-r-[0.5px] border-[#F7F7F7]">
                        <p x-text="minutes"
                            class="font-semibold text-white text-[16px] md:text-[38px] md:leading-[39px]">
                            00</p>
                        <span class="text-[#999999] text-xs md:text-sm">Минут</span>
                    </div>

                    <div class="flex items-center flex-col px-[16px] md:px-[24px]">
                        <p x-text="seconds"
                            class="font-semibold text-white text-[16px] md:text-[38px] md:leading-[39px]">
                            00</p>
                        <span class="text-[#999999] text-xs md:text-sm">Секунды</span>
                    </div>
                </div>
            </div>

            <style>
                .shadow-timer {
                    box-shadow: 1px 1px 24px 6px #242424;
                }
            </style>
        @else
            @if ($main->one_plus_one)
                <div
                    class="relative w-full h-full grid items-end md:items-center justify-items-center grid-cols-1 md:grid-cols-2 grid-rows-1">
                    <div class="col-start-2 mb-[42px] md:mb-0 px-[16px] md:px-0">
                        <h2
                            class="text-[64px] md:text-[230px] font-bold md:leading-[1] tracking-[-4px] md:tracking-[-20px]">
                            1 +
                            1 = 3</h2>
                        <span class="text-[#999999]">*При покупке 2 вещей, третья по наименьшей стоимости идет в
                            подарок</span>
                        <a href="{{ $main->button_link }}"> <x-button-black
                                class="mt-[24px]">{{ $main->button_text }}
                            </x-button-black></a>
                    </div>
                </div>
            @else
                <div class="relative w-full h-full flex items-center flex-col justify-end">
                    <div class="w-full h-[280px] px-4 md:px-[6vw] xl:px-[12vw] 2xl:px-[18vw] pb-[40px] md:pb-[47px] bg-gradient-to-b from-transparent via-[rgba(107,103,103,0.15)] via-[rgba(54,51,51,0.30)] to-[rgba(0,0,0,0.50)]">
                        <div class="flex items-center flex-col justify-end h-full max-w-[400px] text-center mx-auto">
                            <h2 class="text-[28px] md:text-[32px] text-white font-almeria mb-2 leading-[33px]">{!! $main->main_text !!}</h2>
                            <span class="text-[14px] text-white font-almeria block mb-4 md:mb-6">{{ $main->span_text }}</span>
                            <x-button-black
                                href="{{ $main->button_link }}"
                                class="w-[240px] flex"
                            >
                                {{ $main->button_text }}
                            </x-button-black>
                        </div>
                    </div>

                </div>
            @endif
        @endif
    </div>
</div>
