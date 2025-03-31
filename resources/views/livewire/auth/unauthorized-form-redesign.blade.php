<div class="flex flex-col items-center space-y-5">
    <form
        wire:submit.prevent="authenticate"
        class="w-full space-y-5"
    >
        {{--            <x-ui.input--}}
        {{--                name="email"--}}
        {{--                label="Email"--}}
        {{--                type="email"--}}
        {{--                validationRules='["value => !!value", "value => /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/.test(value)"]'--}}
        {{--                errorMessage="{{ $errors->first('email') }}"--}}
        {{--            />--}}

        {{--            <x-ui.input--}}
        {{--                name="password_confirmation"--}}
        {{--                label="Подтвердите пароль"--}}
        {{--                type="password"--}}
        {{--                validationRules='["value => value === document.querySelector(`#password`).value"]'--}}
        {{--            />--}}



        <label for="email" class="block leading-5 space-y-3">
            <div class="flex items-center w-full h-[40px] md:h-[50px] rounded-[10px] border-2">
                <input
                    placeholder="Почта"
                    wire:model.lazy="email"
                    id="email"
                    type="email"
                    class="bg-transparent w-full h-full rounded-[10px] border-none @error('email') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror"
                >
            </div>
            @error('email')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </label>

        <label for="password" class="block leading-5 space-y-3">
            <div
                class="flex items-center w-full h-[40px] md:h-[50px] rounded-[10px] border-2"
                x-data="{ show: false }"
            >
                <input
                    placeholder="Пароль"
                    wire:model.lazy="password"
                    id="password"
                    :type="show ? 'text' : 'password'"
                    class="bg-transparent w-full h-full rounded-[10px] border-none @error('password') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red @enderror"
                >

                <div class="cursor-pointer pr-2" @click="show = !show">
                    <template x-if="!show">
                        <x-icons.close-eye class="transition duration-200" />
                    </template>
                    <template x-if="show">
                        <x-icons.open-eye class="transition duration-200" />
                    </template>
                </div>
            </div>
            @error('password')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </label>

        <div>
            <x-link href="{{ route('password-reset') }}">
                Забыли пароль?
            </x-link>
        </div>

        <div class="w-full flex flex-col items-center justify-center">
            <x-button-black type="submit" class="w-full">
                Войти
            </x-button-black>


            @if ($itsOrder)
                <x-link href="/order?order_without_auth=1">Быстрая покупка без регистрации</x-link>
            @endif
        </div>
    </form>

    <div class="flex items-center w-full">
        <div class="flex-grow border-t border-grey-200 w-full"></div>
        <span class="mx-4 text-color-111">или</span>
        <div class="flex-grow border-t border-grey-200 w-full"></div>
    </div>

    <div class="flex text-center space-x-3 w-full">
        <x-button-outlined
            href="{{ url('/api/oauth/telegram') }}"
            size="md"
            class="w-full"
        >
            <x-icons.tg-account />

            <span class="ml-2">Telegram</span>
        </x-button-outlined>

        <x-button-outlined
            href="{{ url('/api/oauth/vk') }}"
            size="md"
            class="w-full"
        >
            <x-icons.vk-account />

            <span class="ml-2">Вконтакте</span>
        </x-button-outlined>
    </div>

    <div class="flex justify-center text-xs">
        <span class="text-color-111 mr-1">Нет аккаунта?</span>
        <x-link href="{{ route('register', ['back_url' => url()->current()]) }}">Зарегистрироваться</x-link>
    </div>
</div>
