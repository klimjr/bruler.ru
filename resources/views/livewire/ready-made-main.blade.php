<div class="mt-4 md:mt-6 w-full">
    @if(count($looks))
        <h2 class="flex justify-center w-full text-[18px] md:text-[28px]">Готовые образы</h2>
        <div class="mt-4 md:mt-6 flex  gap-x-[4px] md:gap-x-[8px] overflow-x-auto no-scrollbar">
            @foreach ($looks as $look)
                <div

                    class="w-[160px] md:w-[300px] h-[300px] md:h-[600px] flex-shrink-0  flex flex-col"
                >
                    <a
                        href="{{ route('look', ['slug' => $look->slug]) }}"
                        class="relative mb-1 flex-grow bg-[#F7F7F7] overflow-hidden group"
                    >
                        <img
                            class="object-cover w-full h-full"
                            loading="lazy"
                            decoding="async"
                            src="/storage/{{ $look->image }}" alt=""
                        />
                        <span class="absolute inset-0 bg-[#0000000A] transition duration-300 ease-in-out group-hover:bg-[rgba(0,0,0,0.24)]"></span>
                    </a>

                    <div class="flex-shrink flex flex-col items-start">
                        <x-link
                            href="{{ route('look', ['slug' => $look->slug]) }}"
                            active
                        >
                            Купить образ
                        </x-link>
                        <p x-data="{
                                            count: {{ $look->product_count }},
                                            productWord(n) {
                                                const cases = [' товар', ' товара', ' товаров'];
                                                return n + ' ' + cases[(n % 100 > 4 && n % 100 < 20) ? 2 : [2, 0, 1, 1, 1, 2][(n % 10 < 5) ? n % 10 : 5]];
                                            }
                                        }"
                           class="inline-flex text-color-111 text-sm mt-0.5"
                        >
                            <span x-text="productWord(count)"></span>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
