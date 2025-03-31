<div>
    <!-- SIZES -->
    <div class="mt-2 flex flex-wrap gap-x-4 md:gap-x-6 gap-y-2">
        @foreach ($variants as $variant)
            <div wire:key="variant-{{ $variant->id }}">
                <x-button-outlined
                    wire:click="selectVariant('{{ $selectedColor }}', '{{ $variant->id }}')"
                    disabled="{{ $variant->amount < 1 }}"
                    active="{{ $variant->id == $selectedVariant }}"
                    square
                    size="lg"
                    class="{{ strlen($variant->size->name) > 3 ? '!w-[80px] !md:w-[100px]' : '' }}"
                >
                    {{ $variant->size->name }}
                </x-button-outlined>
            </div>
        @endforeach
    </div>
</div>
