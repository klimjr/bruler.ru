@section('title', 'Create a new account')


<div class="flex flex-col items-center my-6">
    <div class="flex flex-col w-full max-w-[440px] px-5">
        <div>
            <x-link href="{{ route('profile') }}" class="inline-flex items-center">
                <x-icons.arrow-left-angle />
                <span class="ml-2">Назад</span>
            </x-link>
        </div>
        <h1 class="text-[28px] text-left mb-6">Регистрация</h1>

        <div x-data="{ countdown: 60, emailResent: false, timer: null }">
            @if (!$isEmailCodeSend)
                <form wire:submit.prevent="register" class="space-y-6">
                    <label for="name" class="relative block">
                        <div class="flex items-center w-full h-[40px] md:h-[50px] rounded-2xl overflow-hidden border @error('name') border-red @enderror">
                            <input
                                placeholder="Имя"
                                wire:model.lazy="name"
                                id="name"
                                type="text"
                                class="bg-transparent w-full h-full border-none @error('last_name') placeholder-red border-red text-red @enderror"
                            />
                        </div>
                        @error('name')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                    </label>

                    <label for="last_name" class="relative block">
                        <div class="flex items-center w-full h-[40px] md:h-[50px] rounded-2xl overflow-hidden border @error('last_name')  border-red @enderror">
                            <input placeholder="Фамилия" wire:model.lazy="last_name" id="last_name" type="text"
                                   class="bg-transparent w-full h-full border-none @error('last_name') placeholder-red border-red text-red @enderror">
                        </div>
                        @error('last_name')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                    </label>

                    <label for="birthday" class="relative block">
                        <div class="flex items-center w-full h-[40px] md:h-[50px] rounded-2xl overflow-hidden border @error('birthday') border-red @enderror">
                            <input type="date" id="birthday" placeholder="Дата рождения" wire:model.lazy="birthday"
                                   class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('birthday') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                            >
                        </div>
                        @error('birthday')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                    </label>

                    <label for="email" class="block leading-5">
                        <div class="flex items-center w-full h-[40px] md:h-[50px] rounded-2xl overflow-hidden border @error('email') border-red text-red placeholder-red  @enderror">
                            <input
                                placeholder="E-mail"
                                wire:model.lazy="email"
                                id="email"
                                type="email"
                                class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('email') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                            />
                        </div>
                        <p class="text-xs text-[#757575]">Пожалуйста, используйте почтовые сервисы Yandex или Mail</p>
                        @error('email')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                    </label>

                    <label for="phone" class="relative block">
                        <div class="flex items-center w-full h-[40px] md:h-[50px] rounded-2xl overflow-hidden border @error('phone') border-red text-red placeholder-red  @enderror">
                            <input
                                data-phone-pattern
                                placeholder="Телефон"
                                maxlength="17"
                                wire:model.lazy="phone"
                                   id="phone" type="tel"
                                   class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('phone') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                            />
                        </div>
                        @error('phone')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                    </label>

                    <div
                        class="relative block"
                        x-data="{show: false}"
                    >
                        <label class="flex items-center w-full h-[40px] md:h-[50px] rounded-2xl overflow-hidden border @error('password') border-red @enderror">
                            <input
                                placeholder="Пароль"
                                wire:model.lazy="password"
                                id="password"
                                :type="show ? 'text' : 'password'"
                                class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('password') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                            />
                        </label>
                        @error('password')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror

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

                    <div class="relative block" x-data="{show: false}">
                        <label class="flex items-center w-full h-[40px] md:h-[50px] rounded-2xl overflow-hidden border @error('passwordConfirmation') border-red @enderror">
                            <input
                                placeholder="Повторите пароль"
                                wire:model.lazy="passwordConfirmation"
                                id="passwordConfirmation"
                                :type="show ? 'text' : 'password'"
                                class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('passwordConfirmation') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                            />
                        </label>

                        @error('passwordConfirmation')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
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

                    <x-button-black
                        type="submit"
                        class="w-full"
                        @click="
                            if (timer) { clearInterval(timer); }
                            emailResent = true;
                            countdown = 60;
                            timer = setInterval(() => countdown--, 1000);
                            setTimeout(() => { emailResent = false; clearInterval(timer); timer = null; }, 60000);
                        "
                    >
                        Создать аккаунт
                    </x-button-black>
                </form>
            @else
                <div class="space-y-3">
                    <p class="main-text">
                        Введите код, который мы выслали на электронную почту {{ $email }}
                    </p>
                    <label for="code" class="relative block">
                        <input wire:model.lazy="code" id="code" type="text" autofocus placeholder="код из письма"
                               class="form-input_underline @error('code') border-red text-red-900 placeholder-red focus:border-red focus:ring-red @enderror" />
                        @error('code')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                        <p x-show="emailResent" x-text="'Повторная отправка через ' + countdown + ' секунд'"
                           class="mt-2 text-sm small-text text-center"></p>
                    </label>

                    <x-button-black
                        wire:click="checkCodeAndRegister" >
                        Подтвердить
                    </x-button-black>

                    <x-button-outlined
                        x-bind:disabled="emailResent"
                        wire:click="reSendCode"
                        @click="if (timer) { clearInterval(timer); }
                            emailResent = true;
                            countdown = 60;
                            timer = setInterval(() => countdown--, 1000);
                            setTimeout(() => { emailResent = false; clearInterval(timer); timer = null; }, 60000);"
                    >
                        Отправить код повторно
                    </x-button-outlined>
                </div>
            @endif
        </div>
    </div>
</div>
