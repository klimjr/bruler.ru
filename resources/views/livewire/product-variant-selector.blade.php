<div>
    <!-- SIZES -->
    <div class="sizes gap-2.5 md:gap-[15px] mt-2 border-black w-fit flex flex-wrap button-text-letter font-normal !text-[20px] !text-primary">
        @foreach ($variants as $variant)
            <div wire:key="variant-{{ $variant->id }}">
                <div wire:click="selectVariant('{{ $selectedColor }}', '{{ $variant->id }}')"
                     class="
       @if ($variant->amount < 1) inactive cursor-not-allowed @else cursor-pointer @endif
                     @if ($variant->id == $selectedVariant) active @endif
                     @if (strlen($variant->size->name) > 3) w-[80px] md:w-[100px] @else w-[40px] md:w-[50px] @endif size h-[40px] md:h-[50px] flex justify-center items-center border-2 rounded-[12.5px] border-black">
                    {{ $variant->size->name }}</div>
            </div>
        @endforeach
    </div>
</div>
