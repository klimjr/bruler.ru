<div
    class="mt-4 border-b border-grey-200"
    :class="{ 'pb-3': !isCertificateInput }"
>
    <button
        type="button"
        class="flex items-center cursor-pointer"
        @click="toggleCertificateInput"
    >
        <span class="inline-block mr-1">Ввести сертификат</span>
        <x-icons.arrow-down-angle
            x-bind:class="isCertificateInput ? 'transform scale-y-[-1] transition-transform duration-300' : 'transform scale-y-100 transition-transform duration-300'"
        />
    </button>
    <div
        x-show="isCertificateInput"
        x-collapse
        class="overflow-hidden"
    >
        <div
            class="pt-3 w-full"
            :class="{ 'pb-6': isCertificateInput }"
        >
            <livewire:certificate-check :certificate="$certificate"  />
        </div>
    </div>
</div>
