@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
@endphp

<div x-data="profileData" class="mt-[20px] mb-[20px] relative">
    <!-- Modal Backdrop -->
    <div x-cloak x-show="isModalOpen" class="fixed inset-0 bg-black-opacity z-40" @click="isModalOpen = false"></div>

    @if (Auth::user())
        <!-- Modal Content -->
        <div
            x-cloak
            x-show="isModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 z-50 flex items-center justify-center"
        >
            <div id="profile-edit" class="relative bg-white rounded-[15px] px-[15px] py-[30px] w-[430px]">
                <!-- Close Button -->
                <div class="absolute top-[15px] right-[15px] cursor-pointer" @click="isModalOpen = false">
                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.60743 18.3925L18.3925 6.60743M6.60742 6.60742L18.3925 18.3925" stroke="#757575"
                              stroke-width="1.875" stroke-linecap="square" />
                    </svg>
                </div>

                <div class="text-3xl font-normal text-center mb-10">Изменить профиль</div>
                <form wire:submit.prevent="save" class="space-y-4 md:space-y-6">
                    <label for="last_name" class="relative block">
                        <div class="w-full overflow-hidden rounded-2xl border bg-white text-sm relative cursor-text transition-colors duration-200 ease-in-out @error('last_name') border-red @enderror">
                            <input
                                placeholder="Фамилия"
                                wire:model.blur="last_name"
                                id="last_name"
                                type="text"
                                data-text-pattern
                                class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('last_name') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                            >
                        </div>
                        @error('last_name')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                    </label>

                    <label for="name" class="relative block">
                        <div class="w-full overflow-hidden rounded-2xl border bg-white text-sm relative cursor-text transition-colors duration-200 ease-in-out @error('name') border-red @enderror">
                            <input
                                placeholder="Имя"
                                wire:model.blur="name"
                                id="name"
                                type="text"
                                data-text-pattern
                                class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('name') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                            >
                        </div>
                        @error('name')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                    </label>

                    <label for="formatted_birthday" class="relative block">
                        <div class="w-full overflow-hidden rounded-2xl border text-sm relative cursor-text transition-colors duration-200 ease-in-out bg-grey-200 pointer-events-none">
                            <input
                                placeholder="Дата рождения"
                                value="{{ $formatted_birthday }}"
                                id="formatted_birthday"
                                type="text"
                                disabled
                                class="w-full h-12 px-4 py-1 bg-transparent border-0"
                            >
                        </div>
                    </label>


                    <label for="phone" class="relative block">
                        <div class="w-full overflow-hidden rounded-2xl border bg-white text-sm relative cursor-text transition-colors duration-200 ease-in-out @error('phone') border-red @enderror">
                            <input
                                placeholder="Телефон"
                                wire:model.blur="phone"
                                id="phone"
                                type="text"
                                data-phone-pattern
                                class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('phone') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                            >
                        </div>
                        @error('phone')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                    </label>

                    <label for="email" class="relative block">
                        <div class="w-full overflow-hidden rounded-2xl border bg-white text-sm relative cursor-text transition-colors duration-200 ease-in-out @error('email') border-red @enderror">
                            <input
                                placeholder="Почта"
                                wire:model.blur="email"
                                id="email"
                                type="email"
                                class="w-full h-12 px-4 py-1 bg-transparent border-0 @error('email') text-red placeholder-red focus:border-red focus:ring-red @enderror"
                            >
                        </div>
                        @error('email')
                        <span class="absolute left-4 bottom-[-18px] text-red text-xs">{{ $message }}</span>
                        @enderror
                    </label>

                    <x-button-black class="w-full" type="submit">Сохранить</x-button-black>
                </form>
                @if ($isSaved)
                    <div class="text-center text-green-600 mt-4">Профиль успешно сохранён!</div>
                @endif
                @if ($isValidationError)
                    <div class="text-center text-red-600 mt-4">Произошла ошибка. Проверьте данные и попробуйте снова.
                    </div>
                @endif
            </div>
        </div>
        <!-- Modal Content end -->

        <div class="px-5 md:px-6 space-y-4 md:space-y-6">
            <div class="relative flex max-md:flex-col items-center md:justify-between bg-grey-100 p-4 md:p-6 max-md:space-y-6">
                <div class="flex max-md:flex-col items-center max-md:space-y-2">
                    @if ($image)
                        <div class="flex-shrink w-[82px] h-[82px] md:mr-6">
                            <img class="object-cover rounded-full w-full h-full" src="{{ $image }}" />
                        </div>
                    @else
                        <div class="flex-shrink w-[82px] h-[82px] bg-grey-200 rounded-full flex items-center justify-center md:mr-6 text-[32px] font-bold text-color-111">
                            {{ $this->getInitials() }}
                        </div>
                    @endif
                    <div class="flex flex-col space-y-3 md:space-y-2 max-md:text-center">
                        <div class="text-[28px] font-semibold">{{ $name }} {{ $last_name }}</div>
                        <div class="text-sm">Дата рождения: {{ $formatted_birthday }}</div>
                        <div class="text-base">{{ $phone }} / {{ $email }}</div>
                    </div>
                </div>

                <x-button-outlined
                    size="md"
                    class="max-md:w-full max-md:max-w-[350px]"
                    @click="isModalOpen = true"
                >
                    Редактировать
                </x-button-outlined>
            </div>

            <div class="space-y-3 md:space-y-4">
                <div
                    x-data="{ isOpen: false }"
                    class="w-full overflow-hidden"
                >
                    <div
                        class="cursor-pointer flex justify-between items-center bg-grey-100  transition p-4 md:p-6"
                        @click="isOpen = !isOpen"
                    >
                        <p class="text-lg font-bold">Программа лояльности</p>
                        <x-icons.arrow-down-angle
                            class="transform transition-transform duration-300"
                            x-bind:class="{ 'scale-y-[-1]': isOpen }"
                        />
                    </div>

                    <div
                        x-show="isOpen "
                        x-collapse
                        class="bg-grey-100 px-4 md:px-6 pb-4 md:pb-6"
                    >
                        <div class="max-md:flex max-md:justify-center">
                            <div class="w-full max-w-[400px] h-auto md:max-w-[300px] relative rounded-lg overflow-hidden text-center">
                                <div class="absolute flex flex-col justify-between text-white p-4 font-semibold w-full h-full z-20">
                                    <div class="flex items-center justify-between">
                                        <x-icons.bruler-shadow />
                                        <a class="cursor-pointer" href="{{ route('loyalty') }}">
                                            <x-icons.info-shadow />
                                        </a>
                                    </div>
                                    <div class="flex items-end justify-between">
                                        <div>
                                            <div class="text-[14px]">Ваши баллы</div>
                                            <div class="text-lg">{{ $points }}</div>
                                        </div>
                                        <div class="text-2xl">Кешбэк {{ $cashback }}%</div>
                                    </div>
                                </div>
                                @if($cashback === 3)
                                    <x-icons.cards.card1 class="w-full h-full z-0" />
                                @elseif($cashback === 5)
                                    <x-icons.cards.card2 class="w-full h-full z-0" />
                                @elseif($cashback === 7)
                                    <x-icons.cards.card3 class="w-full h-full z-0" />
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    x-data="{ isOpen: false }"
                    class="w-full overflow-hidden"
                >
                    <div
                        class="cursor-pointer flex justify-between items-center bg-grey-100  transition p-4 md:p-6"
                        @click="isOpen = !isOpen"
                    >
                        <p class="text-lg font-bold">Активные заказы</p>
                        <x-icons.arrow-down-angle
                            class="transform transition-transform duration-300"
                            x-bind:class="{ 'scale-y-[-1]': isOpen }"
                        />
                    </div>

                    <div
                        x-show="isOpen "
                        x-collapse
                        class="bg-grey-100 px-4 md:px-6 pb-4 md:pb-6"
                    >
                        @if($currentOrders)
                            @foreach ($currentOrders as $order)
                                <div class="flex items-center justify-center text-center h-[270px] relative mb-[40px]">
                                    <img class="absolute top-0 w-full h-full rounded-[20px] object-cover"
                                         src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/bg-profile-order.png') }}" />
                                    <div class="z-10">
                                        <div class="mb-[20px] md:mb-[60px]">
                                            <p class="font-normal text-[20px] md:text-[30px] text-[#272727]">Заказ №{{ $order['idOrder'] }}
                                            </p>
                                            <p class="font-medium text-[16px] md:text-[20px] text-[#757575]">Ваш заказ
                                              {{ $order['deliveryStatusDescription'] }}</p>
                                        </div>

                                        <div class="flex items-center gap-x-[20px] md:gap-x-[40px]">
                                            <div class="w-[40px] md:w-[60px] h-[40px] md:h-[60px]">
                                                @switch($order['deliveryStatus'])
                                                    @case(0)
                                                        <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/0-active.svg') }}" />
                                                    @break
                                                    @default
                                                        @if ($order['deliveryStatus'] > 0)
                                                            <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/0-black.svg') }}" />
                                                        @else
                                                            <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/0-grey.svg') }}" />
                                                        @endif
                                                @endswitch
                                            </div>

                                            <div class="w-[40px] md:w-[60px] h-[40px] md:h-[60px]">
                                                @switch($order['deliveryStatus'])
                                                    @case(1)
                                                        <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/1-active.svg') }}" />
                                                    @break
                                                    @default
                                                        @if ($order['deliveryStatus'] > 1)
                                                            <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/1-black.svg') }}" />
                                                        @else
                                                            <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/1-grey.svg') }}" />
                                                        @endif
                                                @endswitch
                                            </div>

                                            <div class="w-[40px] md:w-[60px] h-[40px] md:h-[60px]">
                                                @switch($order['deliveryStatus'])
                                                    @case(2)
                                                        <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/2-active.svg') }}" />
                                                    @break
                                                    @default
                                                        @if ($order['deliveryStatus'] > 2)
                                                            <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/2-black.svg') }}" />
                                                        @else
                                                            <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/2-grey.svg') }}" />
                                                        @endif
                                                @endswitch
                                            </div>

                                            <div class="w-[40px] md:w-[60px] h-[40px] md:h-[60px]">
                                                @switch($order['deliveryStatus'])
                                                    @case(3)
                                                        <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/3-active.svg') }}" />
                                                    @break
                                                    @default
                                                        @if ($order['deliveryStatus'] > 3)
                                                            <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/3-black.svg') }}" />
                                                        @else
                                                            <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/3-grey.svg') }}" />
                                                        @endif
                                                @endswitch
                                            </div>

                                            <div class="w-[40px] md:w-[60px] h-[40px] md:h-[60px]">
                                                @switch($order['deliveryStatus'])
                                                    @case(4)
                                                        <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/4-active.svg') }}" />
                                                    @break
                                                    @default
                                                        @if ($order['deliveryStatus'] > 4)
                                                            <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/4-black.svg') }}" />
                                                        @else
                                                            <img class="w-full h-full" src="{{ Vite::asset('resources/images/delivery-icons/4-grey.svg') }}" />
                                                        @endif
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="py-20 text-center">
                                <p class="text-color-111 text-lg md:text-[28px] font-bold md:font-semibold">Активных заказов нет</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div
                    x-data="{ isOpen: false }"
                    class="w-full overflow-hidden"
                >
                    <div
                        class="cursor-pointer flex justify-between items-center bg-grey-100  transition p-4 md:p-6"
                        @click="isOpen = !isOpen"
                    >
                        <p class="text-lg font-bold">История покупок</p>
                        <x-icons.arrow-down-angle
                            class="transform transition-transform duration-300"
                            x-bind:class="{ 'scale-y-[-1]': isOpen }"
                        />
                    </div>

                    <div
                        x-show="isOpen "
                        x-collapse
                        class="bg-grey-100 px-4 md:px-6 pb-4 md:pb-6"
                    >
                        <div wire:ignore>
                            @if (!empty($orders_history))
                                <div class="space-y-2">
                                    @foreach ($orders_history as $order)
                                        <div class="space-y-2 {{ !$loop->last ? 'border-b border-grey-200 mb-4 pb-3' : '' }}">
                                            <livewire:product-history
                                                :created="$order->created_at"
                                                :order="$order->id"
                                                :products="$order->products ?? collect([])"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-20 text-center">
                                    <p class="text-color-111 text-lg font-bold md:text-[28px] md:font-semibold">
                                        Покупок нет
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('logout') }}" class="flex items-center text-base font-bold bg-grey-100  transition p-4 md:p-6">
                    <span class="mr-1">Выйти</span>
                    <x-icons.exit />
                </a>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center">
            <livewire:auth.unauthorized-form />
        </div>
    @endif
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('profileData', () => ({
            isModalOpen: false
        }));
    });
</script>


