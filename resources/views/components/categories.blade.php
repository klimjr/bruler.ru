<div>
    <div class="grid md:hidden grid-cols-1 md:grid-cols-4 xl:grid-cols-5 h-fit mt-[80px]">
        @foreach($categories as $category)
            <a
                x-data="{ isOpen: false }"
                x-on:click="isOpen ? window.location.href = '{{ $category->getListUrl() }}' : isOpen = !isOpen"
                @click.away="isOpen = false"
                :class="{'h-fit': isOpen, 'h-[134px]': !isOpen }"
                class="col-span-1 w-full flex justify-center items-center category-item relative"
            >
                <img alt="{{ $category->name }}" :class="{'h-fit !object-contain': isOpen, 'h-[134px]': !isOpen }" class="block absolute z-0 h-full w-full category-item-image object-cover md:object-contain" src="{{ $category->getImageUrlAttribute() }}">
                <div class="h2 !text-white z-1" style="position: absolute; top: 50%; left: 50%; margin-right: -50%; transform: translate(-50%, -50%)">{{ $category->name }}</div>
                <div :class="{'h-full': isOpen, 'hidden': !isOpen }" class="flex overflow-hidden w-full h-full opacity-0">
                    <img alt="{{ $category->name }}" class="object-cover" src="{{ $category->getImageUrlAttribute() }}">
                </div>
            </a>
        @endforeach
    </div>
    <div class="hidden md:grid grid-cols-1 md:grid-cols-4 xl:grid-cols-5 h-[668px] mt-[95px]">
        @foreach($categories as $category)
            <a href="{{ $category->getListUrl() }}" class="col-span-1 w-full flex justify-center items-center category-item relative">
                <img alt="{{ $category->name }}" class="block absolute z-0 h-full w-full category-item-image object-cover md:object-contain" src="{{ $category->getImageUrlAttribute() }}">
                <div class="h2 !text-white z-1 relative">{{ $category->name }}</div>
            </a>
        @endforeach
    </div>
</div>
