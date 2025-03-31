@section('title', 'Сменить пароль')

<div class="flex flex-col items-center justify-center h-full mt-3 mb-3">
    <div class="flex flex-col w-full max-w-[440px] px-5">
        <div>
            <x-link href="{{ route('profile') }}" class="inline-flex items-center">
                <x-icons.arrow-left-angle />
                <span class="ml-2">Назад</span>
            </x-link>
        </div>
        <h1 class="text-[28px] text-left mb-6">Восстановление пароля</h1>

        <div class="w-full" x-data="{ countdown: 60, emailResent: false, timer: null }">
            @if ($isResetPasswordCodeSend)
                <div class="space-y-6">
                    <p class="main-text">
                        Введите код из письма, который мы выслали на вашу почту {{ $email }}
                    </p>
                    <label for="code" class="relative">
                        <div class="w-full overflow-hidden rounded-2xl border bg-white text-sm relative cursor-text transition-colors duration-200 ease-in-out @error('code') border-red @enderror">
                            <input
                                wire:model.lazy="code"
                                id="code"
                                type="text"
                                required
                                autofocus
                                placeholder="Код из письма"
                                class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('code') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                            />
                        </div>

                        @error('code')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                        <p
                            x-show="emailResent"
                            x-text="'Повторная отправка через ' + countdown + ' секунд'"
                            class="mt-2 text-sm small-text text-center">
                        </p>
                    </label>
                    <x-button-black
                        wire:click="checkCodeAndLogin"
                        class="w-full"
                    >
                        Cменить пароль
                    </x-button-black>

                    <x-button
                        class="w-full"
                        x-bind:disabled="emailResent"
                        wire:click="reSendCode"
                        @click="if (timer) { clearInterval(timer); }
                            emailResent = true;
                            countdown = 60;
                            timer = setInterval(() => countdown--, 1000);
                            setTimeout(() => { emailResent = false; clearInterval(timer); timer = null; }, 60000);"
                    >
                        Отправить код повторно
                    </x-button>
                </div>
            @else
                <form wire:submit.prevent="sendResetPasswordCode" class="space-y-6">
                    <label class="relative">
                        <div class="w-full overflow-hidden rounded-2xl border bg-white text-sm relative cursor-text transition-colors duration-200 ease-in-out @error('email') border-red @enderror">
                            <input
                                placeholder="Email"
                                wire:model.lazy="email"
                                id="email"
                                type="email"
                                required
                                class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('email') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                            />
                        </div>
                        @error('email')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                    </label>


                    <div class="w-full flex items-center justify-center">
                        <x-button-black
                            type="submit"
                            class="w-full"
                            @click="if (timer) { clearInterval(timer); }
                              emailResent = true;
                              countdown = 60;
                              timer = setInterval(() => countdown--, 1000);
                              setTimeout(() => { emailResent = false; clearInterval(timer); timer = null; }, 60000);"

                        >
                            Сбросить
                        </x-button-black>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
