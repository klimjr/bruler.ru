<div>
    <div class="text !text-[20px] !text-primary mt-4 md:mt-7">
        товары в наборе:
    </div>
    <div class="flex flex-col gap-5 mt-2">
        @foreach ($productsInSet as $idx => $product_set)
            @if (count($product_set) > 1)
                <div class="custom-select max-w-[300px]">
                    <span class="text-xs text-[#ccc]">Выберите товар из списка</span>
                    <div
                        class="selected-option flex items-center border-[1px] border-[#ccc] p-2.5 bg-white cursor-pointer">
                        <img class="w-[30px] mr-2" src="/storage/{{ $product_set[$product_set['selected']]['image'] }}"
                            alt="{{ $product_set[$product_set['selected']]['name_en'] }}" />
                        <span>{{ $product_set[$product_set['selected']]['name_en'] }}</span>
                        <svg class="ml-auto w-[20px]" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" class="bi bi-caret-down" viewBox="0 0 16 16">
                            <path
                                d="M3.204 5h9.592L8 10.481 3.204 5zm-.753.659 4.796 5.48a1 1 0 0 0 1.506 0l4.796-5.48c.566-.647.106-1.659-.753-1.659H3.204a1 1 0 0 0-.753 1.659z" />
                        </svg>
                    </div>
                    <div class="options">
                        @foreach ($product_set as $k => $product_set_info)
                            @if ($k != 'selected')
                                <div class="option" data-value="{{ $product_set_info['id'] }}"
                                    wire:click="selectProduct({{ $idx }}, {{ $k }})">
                                    <img class="w-[30px] mr-2" src="/storage/{{ $product_set_info['image'] }}"
                                        alt="{{ $product_set_info['name_en'] }}" />
                                    <span>{{ $product_set_info['name_en'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <span class="text-xs text-[#ccc]">Выберите размер</span>
                    <div
                        class="sizes border-black border-[2px] rounded-[9px] w-fit flex button-text-letter !text-[24px] !text-primary">
                        @php
                            $selectedVariant = array_key_exists(
                                'selectedVariant',
                                $product_set[$product_set['selected']],
                            )
                                ? $product_set[$product_set['selected']]['selectedVariant']
                                : null;
                        @endphp

                        @foreach ($product_set[$product_set['selected']]['variants'] as $variant)
                            <div wire:key="variant-{{ $variant->id }}">
                                <div wire:click="selectVariant('{{ $variant->id }}', {{ $idx }}, '{{ $product_set['selected'] }}', '{{ $variant->amount }}')"
                                    class="
              @if ($variant->amount < 1) inactive cursor-not-allowed @else cursor-pointer @endif
              @if ($variant->id == $selectedVariant) active @endif
              @if (strlen($variant->size->name) > 4) text-lg px-3 @else w-[59px] @endif
              size h-[49px] flex justify-center items-center @if (!$loop->last) border-r-[2px] @endif border-black">
                                    {{ $variant->size->name }}</div>
                            </div>
                        @endforeach
                    </div>

                </div>
            @else
                <div>
                    <div class="flex items-center border-[1px] border-[#ccc] p-2.5 bg-white max-w-[300px]">
                        <img class="w-[30px] mr-2" src="/storage/{{ $product_set[0]['image'] }}"
                            alt="{{ $product_set[0]['name_en'] }}" />
                        <span>{{ $product_set[0]['name_en'] }}</span>
                    </div>
                    @if (array_key_exists('variants', $product_set[0]) && count($product_set[0]['variants']) > 1)
                        <span class="text-xs text-[#ccc]">Выберите размер</span>
                        <div
                            class="sizes border-black border-[2px] rounded-[9px] w-fit flex button-text-letter !text-[24px] !text-primary">
                            @php
                                $selectedVariantProduct = array_key_exists('selectedVariant', $product_set[0])
                                    ? $product_set[0]['selectedVariant']
                                    : null;
                            @endphp

                            @foreach ($product_set[0]['variants'] as $variantSl)
                                <div wire:key="variant-{{ $variantSl->id }}">
                                    <div wire:click="selectVariant('{{ $variantSl->id }}', {{ $idx }}, '0', '{{ $variantSl->amount }}')"
                                        class="
              @if ($variantSl->amount < 1) inactive cursor-not-allowed @else cursor-pointer @endif
              @if ($variantSl->id == $selectedVariantProduct) active @endif
              @if (strlen($variantSl->size->name) > 4) text-lg px-3 @else w-[59px] @endif
              size h-[49px] flex justify-center items-center @if (!$loop->last) border-r-[2px] @endif border-black">
                                        {{ $variantSl->size->name }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    <style>
        .custom-select {
            position: relative;
            display: inline-block;
        }

        .options {
            display: none;
            position: absolute;
            left: 0;
            width: 100%;
            border: 1px solid #ccc;
            background: #fff;
            z-index: 10;
        }

        .option {
            display: flex;
            align-items: center;
            padding: 10px;
            cursor: pointer;
        }

        .option:hover {
            background: #f0f0f0;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const customSelects = document.querySelectorAll('.custom-select');

            customSelects.forEach(select => {
                const selectedOption = select.querySelector('.selected-option');
                const options = select.querySelector('.options');

                selectedOption.addEventListener('click', function() {
                    options.style.display = options.style.display === 'block' ? 'none' : 'block';
                });

                const optionElements = options.querySelectorAll('.option');

                optionElements.forEach(option => {
                    option.addEventListener('click', function() {
                        const img = option.querySelector('img').src;
                        const text = option.querySelector('span').innerText;
                        const value = option.getAttribute('data-value');

                        selectedOption.querySelector('img').src = img;
                        selectedOption.querySelector('span').innerText = text;
                        selectedOption.setAttribute('data-value', value);

                        options.style.display = 'none';
                    });
                });
            });

            document.addEventListener('click', function(event) {
                customSelects.forEach(select => {
                    const options = select.querySelector('.options');
                    if (!select.contains(event.target)) {
                        options.style.display = 'none';
                    }
                });
            });
        });
    </script>

</div>
