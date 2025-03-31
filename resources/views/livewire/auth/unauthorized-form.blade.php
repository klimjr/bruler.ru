<div class="flex flex-col w-full max-w-[440px] px-5 space-y-6">
    <h1 class="text-[28px] text-left">Войти</h1>

    <form wire:submit.prevent="authenticate" class="space-y-6">
        <label for="email" class="relative block">
            <div class="w-full overflow-hidden rounded-2xl border bg-white text-sm relative cursor-text transition-colors duration-200 ease-in-out @error('email') border-red @enderror">
                <input
                    placeholder="Email"
                    wire:model.lazy="email"
                    id="email"
                    type="email"
                    class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('email') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                >
            </div>
            @error('email')
            <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
            @enderror
        </label>

        <label for="password" class="relative block">
            <div class="flex items-center w-full overflow-hidden rounded-2xl border bg-white text-sm relative cursor-text transition-colors duration-200 ease-in-out @error('password') border-red text-red placeholder-red focus:border-red focus:ring-red @enderror"
                x-data="{ show: false }"
            >
                <input
                    placeholder="Пароль"
                    wire:model.lazy="password"
                    id="password"
                    :type="show ? 'text' : 'password'"
                    class="w-full h-12 pl-4 pr-6 py-1 bg-transparent border-0 @error('password') text-red placeholder-red focus:border-red focus:ring-red @enderror""
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
            <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
            @enderror
        </label>

        <!-- Ссылка на восстановление пароля -->
        <div>
            <x-link href="{{ route('password-reset') }}">
                Забыли пароль?
            </x-link>
        </div>

        <!-- Кнопка отправки формы -->
        <div class="w-full flex flex-col items-center justify-center">
            <x-button-black type="submit" class="w-full">
                Войти
            </x-button-black>
        </div>
    </form>

    <div class="flex items-center w-full">
        <div class="flex-grow border-t border-grey-200 w-full"></div>
        <span class="mx-4 text-color-111">или</span>
        <div class="flex-grow border-t border-grey-200 w-full"></div>
    </div>

    <div class="flex flex-col space-y-6">
        <x-button-outlined
            href="{{ url('/api/oauth/yandex') }}"
            size="md"
            class="w-full space-x-2"
        >
            <x-icons.new.ya-color/>
            <span>Войти с Яндекс ID</span>
        </x-button-outlined>

        <x-button-outlined
            href="{{ url('/api/oauth/vk') }}"
            size="md"
            class="w-full space-x-2"
        >
            <x-icons.new.vk-color/>
            <span>Войти через VK</span>
        </x-button-outlined>

        <x-button-outlined
            href="{{ url('/api/oauth/telegram') }}"
            size="md"
            class="w-full space-x-2"
        >
            <x-icons.new.tg-color/>
            <span>Войти через Telegram</span>
        </x-button-outlined>
    </div>

    <div class="flex justify-center text-xs">
        <span class="text-color-111 mr-1">Нет аккаунта?</span>
        <x-link href="{{ route('register', ['back_url' => url()->current()]) }}">Зарегистрироваться</x-link>
    </div>
</div>
