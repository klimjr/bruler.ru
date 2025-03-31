<div class="flex flex-col h-full">
    <div class="flex items-center justify-between p-6">
        <p class="relative text-3xl font-bold">
            Корзина
            <span class="absolute text-base left-[100%] pl-1 text-[#999]">{{ $productCount }}</span>
        </p>
        <button @click="closeCart">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="#131313">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.23708 0.508685C1.68339 0.0623718 2.40701 0.0623718 2.85332 0.508685L8.90234 6.55771L14.9514 0.508685C15.3977 0.0623718 16.1213 0.0623718 16.5676 0.508685C17.0139 0.954999 17.0139 1.67862 16.5676 2.12493L10.5186 8.17395L16.5676 14.223C17.0139 14.6693 17.0139 15.3929 16.5676 15.8392C16.1213 16.2855 15.3977 16.2855 14.9514 15.8392L8.90234 9.79019L2.85332 15.8392C2.40701 16.2855 1.68339 16.2855 1.23708 15.8392C0.790765 15.3929 0.790765 14.6693 1.23708 14.223L7.2861 8.17395L1.23708 2.12493C0.790765 1.67862 0.790765 0.954999 1.23708 0.508685Z"/>
            </svg>
        </button>
    </div>
    <div class="overflow-y-auto scroll-hidden h-full grow p-6 z-10">
        <livewire:cart />
    </div>

    @if($productCount > 0)
        <livewire:total-cart-redesign />
    @endif
</div>
