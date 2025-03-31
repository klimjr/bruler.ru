@props([
'name',
'label',
'caption' => '',
'value' => ''
])

<div class="relative"
     x-data="{
        errors: @json($errors->toArray()),
        focused: false,
        show: false,
        filled: false,
        hasError(fieldName) {
            return this.errors[fieldName] && this.errors[fieldName].length > 0;
        }"
     x-init="
        $nextTick(() => {
            filled = $refs.input.value !== '';
        });
    ">
    <div
        class="w-full rounded-2xl border bg-white h-12 px-4 py-1 text-sm relative cursor-text transition-colors duration-200 ease-in-out"
        :class="{
            'border-green-200': $refs.input === document.activeElement,
            'border-gray-200': !filled && $refs.input !== document.activeElement,
            'border-red': hasError('{{ $name }}')
            }"
        @click="$refs.input.focus()"
    >
        <!-- Поле ввода -->
        <input
            x-ref="input"
            id="{{ $name }}"
            :type="show ? 'text' : 'password'"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $attributes->merge(['class' => 'peer w-full text-sm bg-transparent border-0 p-0 translate-y-[18px] placeholder:text-transparent outline-none pr-10']) }}
            placeholder=" "
            @input="filled = $refs.input.value !== ''"
            @focus="$nextTick(() => filled = true)"
            @blur="$nextTick(() => filled = $refs.input.value !== '')"
        />

        <!-- Метка -->
        <label
            for="{{ $name }}"
            class="absolute left-4 text-gray-400 text-xs transition-all duration-200 ease-in-out"
            :class="{
                'top-1': filled || $refs.input === document.activeElement,
                'top-4 text-sm': !(filled || $refs.input === document.activeElement)
            }"
        >
            {{ $label }}
        </label>

        <!-- Кнопка переключения видимости пароля -->
        <button class="absolute right-4 top-1/2 -translate-y-1/2 text-color-111 cursor-pointer" @click.prevent="show = !show">
            <template x-if="!show">
                <x-icons.close-eye class="w-5 h-5 text-gray-400 hover:text-gray-600 transition duration-200"/>
            </template>
            <template x-if="show">
                <x-icons.open-eye class="w-5 h-5 text-gray-400 hover:text-gray-600 transition duration-200"/>
            </template>
        </button>
    </div>

    <!-- Подсказка или сообщение об ошибке -->
    @if (!$errors->has($name) && $caption)
        <span class="absolute left-4 bottom-[-18px] text-gray-400 text-xs">{{ $caption }}</span>
    @endif

    @error($name)
    <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
    @enderror
</div>
