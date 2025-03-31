<x-order.section>
    <x-slot:header>
        <h3 class="md:text-[18px] font-bold">Получатель</h3>
    </x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-md:mb-4">

        <x-order.input
            id="name"
            name="name"
            label="Имя"
            type="text"
            caption=""
        />
        <x-order.input
            id="lastName"
            name="lastName"
            label="Фамилия"
            type="text"
            caption=""
            :rules="['required']"
        />
        <x-order.input
            id="phone"
            name="phone"
            label="Телефон"
            type="tel"
            phone
            data-phone-pattern
            caption=""
            :rules="['required']"
        />
        <x-order.input
            id="email"
            name="email"
            label="Email"
            caption=""
            type="email"
        />
        @if ($this->orderType === \App\Models\Product::TYPE_CERTIFICATE)
            <x-order.input
                id="target_email"
                name="target_email"
                label="Эл.почта получателя сертификата"
                caption=""
                type="email"
            />
        @endif
    </div>
</x-order.section>
