@section('title', 'войти в профиль')

<div>
    <div class="mt-6 px-6 sm:w-full sm:max-w-fit md:mt-10">
        <form wire:submit.prevent="authenticate" class="space-y-3">
            <label for="email" class="block leading-5 space-y-3">
                <div class="flex items-center w-full h-[40px] rounded-[10px] border-[1.5px]">
                    <input placeholder="Почта" wire:model.lazy="email" id="email" type="text"
                        class="bg-transparent w-full rounded-[10px] border-none @error('email') border-red text-red-900 placeholder-red focus:border-red focus:ring-red @enderror">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </label>
            <label for="password" class="block leading-5 space-y-3">
                <div class="flex items-center w-full h-[40px] rounded-[10px] border-[1.5px]">
                    <input placeholder="Пароль" wire:model.lazy="password" id="password" type="password"
                        class="bg-transparent w-full rounded-[10px] border-none @error('password') border-red text-red-900 placeholder-red focus:border-red focus:ring-red @enderror">
                    <div class="uppercase font-semibold pr-2">иконка</div>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </label>
            <div>
                <a href="{{ route('password-reset') }}"
                    class="underline small-text text-[#4B4B4B] flex  justify-center">
                    Забыли пароль?
                </a>
            </div>
            <div class="w-full flex justify-center">
                <x-button-black type="submit" class="w-full md:!w-[175px] !bg-primary active:!bg-secondary">
                    Войти
                </x-button-black>
            </div>
        </form>

        <div class="flex justify-center gap-1 text-xs text-[#757575] my-3">
            <div class="opacity-50">
                Нет аккаунта?
            </div>
            <a class="underline">Регистрация</a>
        </div>
    </div>
    <div class="flex flex-col text-center items-center text-[#757575] mb-5">
        <div class="flex items-center gap-3">
            <x-icons.tg-account />
            <div class="border-y-[1.5px] border-[#F2F2F4] w-full py-4">Войти с помощью <span
                    class="text-primary">Telegram</span></div>
        </div>

        <div class="flex items-center gap-3">
            <x-icons.vk-account />
            <div class="border-y-[1.5px] border-[#F2F2F4] w-full py-4">Войти с помощью <span
                    class="text-primary">Вконтакте</span></div>
        </div>
    </div>
</div>
