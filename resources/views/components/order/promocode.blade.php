<div
    class="mt-4 border-b border-grey-200"
    :class="{ 'pb-3': !isPromoCodeInput }"
>
    <button
        type="button"
        class="flex items-center cursor-pointer"
        @click="togglePromoCodeInput"
    >
        <span class="inline-block mr-1">Ввести промокод</span>
        <x-icons.arrow-down-angle
            x-bind:class="isPromoCodeInput ? 'transform scale-y-[-1] transition-transform duration-300' : 'transform scale-y-100 transition-transform duration-300'"
        />
    </button>
    <div
        x-show="isPromoCodeInput"
        x-collapse
        class="overflow-hidden"
    >
        <div
            class="pt-3 w-full"
            :class="{ 'pb-6': isPromoCodeInput }"
        >
            <livewire:promocode-check-redesign/>
        </div>
    </div>
</div>
