<div class="flex justify-between items-center px-2.5 md:px-[50px] mb-8 md:mb-0">
    <div class="text-base md:text-3xl">{{ $totalFilteredProducts }}
        {{ $totalFilteredProducts % 10 == 1 && $totalFilteredProducts % 100 != 11 ? 'товар' : ($totalFilteredProducts % 10 >= 2 && $totalFilteredProducts % 10 <= 4 && ($totalFilteredProducts % 100 < 10 || $totalFilteredProducts % 100 >= 20) ? 'товара' : 'товаров') }}
    </div>
    <div class="relative flex">
        <div id="overlay" class="fixed inset-0 bg-black-opacity z-20 mt-[55px] hidden"></div>

        <div id="catalogFilter"
            class="absolute right-[-12px] md:right-0 w-screen md:w-[320px] bg-white px-[10px] pt-[15px] pb-[20px] rounded-lg shadow-md z-30 hidden">
            <div class="relative flex items-center justify-between px-[5px] mb-4">
                <div id="closeFilter" class="cursor-pointer">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L13 13M13 1L1 13" stroke="black" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <h2 class="absolute left-1/2 transform -translate-x-1/2 text-sm font-extrabold">Фильтры</h2>
                <a href="{{ route('catalog') }}" class="text-xs font-medium">Сбросить</a>
            </div>
            <form class="max-h-[60vh] overflow-x-hidden overflow-y-auto no-scrollbar relative"
                action="{{ route('collection.filter') }}" method="GET">
                <div class="mb-4">
                    <label for="price-from" class="block text-sm font-extrabold">Цена</label>
                    <div class="flex items-center space-x-2 mt-2">
                        <input type="number" name="price-from" id="price-from"
                            class="w-1/2 p-2 border-2 border-black rounded-[7px]" />
                        <span>-</span>
                        <input type="number" name="price-to" id="price-to"
                            class="w-1/2 p-2 border-2 border-black rounded-[7px]" />
                    </div>
                    <div class="mt-4 px-[8px]">
                        <div class="custom-style-slider w-full" id="slider"></div>
                    </div>
                </div>
                <style>
                    .custom-style-slider {
                        height: 5px;
                        background: #F2F2F4;
                        border: none;
                        box-shadow: none;
                    }

                    .custom-style-slider .noUi-connect {
                        background: #0093C4;
                    }

                    .custom-style-slider .noUi-handle {
                        height: 15px;
                        width: 15px;
                        top: -5px;
                        right: -9px;
                        border-radius: 9px;
                        background: #0093C4;
                        border: none;
                        box-shadow: none;
                    }

                    .custom-style-slider .noUi-handle:before,
                    .noUi-handle:after {
                        content: none;
                    }

                    .no-scrollbar::-webkit-scrollbar {
                        display: none;
                    }

                    .no-scrollbar {
                        -ms-overflow-style: none;
                        /* для Internet Explorer */
                        scrollbar-width: none;
                        /* для Firefox */
                    }
                </style>

                <div class="mb-4">
                    <span class="block text-sm font-extrabold">Наличие</span>
                    <div class="flex flex-col mt-2 space-y-2 text-xs">
                        <label class="flex items-center">
                            <input type="checkbox" name="in_stock"
                                class="mr-2 w-[25px] h-[25px] border-2 border-black rounded-[7px]" />
                            В наличии
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="to_order"
                                class="mr-2 w-[25px] h-[25px] border-2 border-black rounded-[7px]" />
                            Под заказ
                        </label>
                    </div>
                </div>
                <div class="mb-4">
                    <span class="block text-sm font-extrabold">Тип</span>
                    <div class="flex flex-col mt-2 space-y-2 text-xs">
                        @foreach (App\Models\Category::all() as $category)
                            <label class="flex items-center {{ !$loop->last ? 'pb-2 border-b-[1.5px] border-[#F2F2F4]' : '' }}">
                                <input type="checkbox" name="category[]" value="{{ $category->id }}"
                                    class="mr-2 w-[25px] h-[25px] border-2 border-black rounded-[7px]" />
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="mb-4">
                    <span class="block text-sm font-extrabold">Цвет</span>
                    <div class="flex flex-col mt-2 space-y-2 text-xs">
                        @foreach (App\Models\Color::all() as $color)
                            <label class="flex items-center {{ !$loop->last ? 'pb-2 border-b-[1.5px] border-[#F2F2F4]' : '' }}">
                                <input type="checkbox" name="color[]" value="{{ $color->id }}"
                                    class="mr-2 w-[25px] h-[25px] border-2 border-black rounded-[7px]" />
                                {{ $color->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="mb-4">
                    <span class="block text-sm font-extrabold">Технологии</span>
                    <div class="flex flex-col mt-2 space-y-2 text-xs">
                        @foreach (App\Models\Technology::all() as $technology)
                            <label class="flex items-center {{ !$loop->last ? 'pb-2 border-b-[1.5px] border-[#F2F2F4]' : '' }}">
                                <input type="checkbox" name="technology[]" value="{{ $technology->id }}"
                                    class="mr-2 w-[25px] h-[25px] border-2 border-black rounded-[7px]" />
                                {{ $technology->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="mb-4">
                    <span class="block text-sm font-extrabold">Размер</span>
                    <div class="flex flex-wrap mt-2 space-y-2 text-xs">
                        @foreach (App\Models\Size::all() as $size)
                            <label class="flex items-center w-1/2">
                                <input type="checkbox" name="size[]" value="{{ $size->id }}"
                                    class="mr-2 w-[25px] h-[25px] border-2 border-black rounded-[7px]" />
                                {{ $size->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="sticky bottom-0 text-center"
                    style="background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, #FFFFFF 79.68%);">
                    <button type="submit" class="bg-black text-white text-sm font-medium py-3 px-8 rounded-[10px]">Показать
                        товары</button>
                </div>
            </form>
        </div>

        <div id="openFilter" class="cursor-pointer">
            <div class="flex items-center gap-1 md:hidden">
                <div class="text-base underline">Фильтр</div>
                <div
                    class="w-[20px] h-[20px] flex items-center justify-center bg-black rounded-full text-sm text-white">
                    <span id="countFilter">0</span>
                </div>
            </div>

            <svg class="ml-56 hidden md:block" width="31" height="30" viewBox="0 0 31 30" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M11.9283 21.25L4.46973 21.25" stroke="black" stroke-width="2.5" stroke-linecap="round" />
                <path d="M26.9697 8.75H19.4697" stroke="black" stroke-width="2.5" stroke-linecap="round" />
                <path d="M4.48223 8.75L4.46973 8.75" stroke="black" stroke-width="2.5" stroke-linecap="round" />
                <path d="M26.9822 21.25L26.9697 21.25" stroke="black" stroke-width="2.5" stroke-linecap="round" />
                <path
                    d="M19.387 17.5C21.4466 17.5 23.1163 19.1789 23.1163 21.25C23.1163 23.3211 21.4466 25 19.387 25C17.3274 25 15.6577 23.3211 15.6577 21.25C15.6577 19.1789 17.3274 17.5 19.387 17.5Z"
                    stroke="black" stroke-width="2.5" />
                <path
                    d="M11.9285 5C13.9881 5 15.6578 6.67893 15.6578 8.75C15.6578 10.8211 13.9881 12.5 11.9285 12.5C9.86888 12.5 8.19922 10.8211 8.19922 8.75C8.19922 6.67893 9.86887 5 11.9285 5Z"
                    stroke="black" stroke-width="2.5" />
            </svg>
        </div>
    </div>
</div>

<script>
    document.getElementById('openFilter').addEventListener('click', function() {
        document.getElementById('catalogFilter').classList.remove('hidden');
        document.getElementById('overlay').classList.remove('hidden');
    });
    document.getElementById('closeFilter').addEventListener('click', function() {
        document.getElementById('catalogFilter').classList.add('hidden');
        document.getElementById('overlay').classList.add('hidden');
    });
    document.getElementById('overlay').addEventListener('click', function() {
        document.getElementById('catalogFilter').classList.add('hidden');
        document.getElementById('overlay').classList.add('hidden');
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var slider = document.getElementById('slider');

        noUiSlider.create(slider, {
            start: [{{ request()->input('price_from', $minPriceProduct) }},
                {{ request()->input('price_to', $maxPriceProduct) }}
            ],
            connect: true,
            range: {
                'min': {{ $minPriceProduct }},
                'max': {{ $maxPriceProduct }}
            }
        });

        slider.noUiSlider.on('update', function(values, handle) {
            if (handle) {
                document.getElementById('price-to').value = Math.round(values[handle]);
            } else {
                document.getElementById('price-from').value = Math.round(values[handle]);
            }
        });

        document.getElementById('price-from').addEventListener('change', function() {
            slider.noUiSlider.set([this.value, null]);
        });
        document.getElementById('price-to').addEventListener('change', function() {
            slider.noUiSlider.set([null, this.value]);
        });

        const urlParams = new URLSearchParams(window.location.search)
        let countFilter = 0

        function applyCheckboxFilter(name) {
            const values = urlParams.getAll(name + '[]');
            values.forEach(value => {
                countFilter++
                const checkbox = document.querySelector(`input[name="${name}[]"][value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }

        applyCheckboxFilter('color')
        applyCheckboxFilter('size')
        applyCheckboxFilter('category')
        applyCheckboxFilter('technology')

        const inStockCheckbox = document.querySelector('input[name="in_stock"]');
        const toOrderCheckbox = document.querySelector('input[name="to_order"]');

        if (urlParams.has('in_stock')) {
            countFilter++
            inStockCheckbox.checked = true;
        }
        if (urlParams.has('to_order')) {
            countFilter++
            toOrderCheckbox.checked = true;
        }

        const priceFrom = urlParams.get('price-from');
        const priceTo = urlParams.get('price-to');

        if (priceFrom) {
            document.getElementById('price-from').value = priceFrom;
        }
        if (priceTo) {
            document.getElementById('price-to').value = priceTo;
        }

        document.getElementById('countFilter').textContent = countFilter
    });
</script>
