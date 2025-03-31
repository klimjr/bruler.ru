<div>
    <x-filament::card>
        <div>
            @if (isset($categoriesAndProducts) && $categoriesAndProducts)
                @foreach ($categoriesAndProducts as $categoryName => $products)
                    @if (count($products) >= 1)
                        <div class="flex flex-col space-y-4 mt-5 first:mt-0">
                            <h2 class="text-[18px] md:text-[28px] text-center">{{ $categoryName }}</h2>
                            <div wire:sortable="updateOrder" class="grid grid-cols-4 gap-4">
                                @foreach ($products as $product)
                                    @if ($product->show)
                                        <div wire:sortable.item="{{ $product->id }}" wire:key="product-{{ $product->id }}">
                                            <div wire:sortable.handle class="relative bg-white rounded-lg shadow-sm overflow-hidden cursor-move">
                                                <div class="aspect-w-1 aspect-h-1 h-64">
                                                    <img src="{{ $product->image_url }}"
                                                         alt="{{ $product->name }}"
                                                         class="object-cover h-full w-full"
                                                         >
                                                </div>
                                                <div class="p-4 text-center">
                                                    <h3 class="text-sm font-semibold text-gray-900 truncate">
                                                        {{ $product->name }}
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </x-filament::card>

    @push('scripts')
        <script>
            document.addEventListener('livewire:load', function () {
                window.livewire.on('sorted', function () {
                    // Можно добавить какое-то действие после сортировки
                    console.log('Sorting completed');
                });
            });
        </script>
    @endpush
</div>
