@props([
'activeId' => null,
'id' => '',
])

<div
    x-data="{sdt: @entangle('selectedDeliveryType'), id: @js($id)}"
{{--    x-on:click="isOpenRadioElement = !isOpenRadioElement"--}}
{{--    @click="isOpenRadioElement = !isOpenRadioElement"--}}
    class="bg-white border rounded-2xl {{ $activeId === $id ? 'border-black' : 'border-grey-200' }}"
>
{{--    <div  x-text="sdt"></div>--}}
    <div
        {{ $attributes }}
        class="flex items-center justify-between cursor-pointer px-4 py-6"
    >
        <div class="flex items-center">
            <x-radio-dummy active="{{ $activeId === $id }}" />

            <p class="ml-2">{{ $slot }}</p>
        </div>

        @isset($end)
            <div class="ml-4">
                {{ $end }}
            </div>
        @endisset
    </div>

    @isset($accordion)
        <div
            x-show="sdt === id"
{{--            x-show="{{ $activeId === $id }}"--}}
            x-collapse
            class="px-4 py-6 border-t h-100"
        >
            {{ $accordion }}
        </div>
    @endisset
</div>
