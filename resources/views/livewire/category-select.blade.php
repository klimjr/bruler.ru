<div
    class="mt-[24px] w-full h-[330px] md:h-[42vw] flex md:justify-center gap-x-[4px] md:gap-x-[8px] overflow-x-auto no-scrollbar">
    <a href="{{ route('collection.filter', ['category' => [$categories[0]['id']]]) }}"
        class="w-[250px] md:w-full shrink-0 md:shrink bg-[#F7F7F7] relative">
        <img
            class="w-full h-full object-cover object-left"
            loading="lazy"
            decoding="async"
            src="/storage/{{ $categories[0]['image'] }}"
        />
        <div class="absolute inset-0 bg-[#0000000A]"></div>
        <span
            class="absolute left-[16px] md:left-[26px] bottom-[15px] md:bottom-[40px] text-[18px] md:text-[28px]">{{ $categories[0]['name'] }}</span>
    </a>
    <a href="{{ route('collection.filter', ['category' => [$categories[1]['id']]]) }}"
        class="w-[250px] md:w-full shrink-0 md:shrink bg-[#F7F7F7] relative">
        <img
            class="w-full h-full object-cover object-left"
            loading="lazy"
            decoding="async"
            src="/storage/{{ $categories[1]['image'] }}"
        />
        <div class="absolute inset-0 bg-[#0000000A]"></div>
        <span
            class="absolute left-[16px] md:left-[26px] bottom-[15px] md:bottom-[40px] text-[18px] md:text-[28px]">{{ $categories[1]['name'] }}</span>
    </a>
    <a href="{{ route('collection.filter', ['category' => [$categories[4]['id']]]) }}"
        class="w-[250px] md:w-full shrink-0 md:shrink bg-[#F7F7F7] relative">
        <img
            class="w-full h-full object-cover object-left"
            loading="lazy"
            decoding="async"
            src="/storage/{{ $categories[4]['image'] }}"
        />
        <div class="absolute inset-0 bg-[#0000000A]"></div>
        <span
            class="absolute left-[16px] md:left-[26px] bottom-[15px] md:bottom-[40px] text-[18px] md:text-[28px]">{{ $categories[4]['name'] }}</span>
    </a>
</div>
