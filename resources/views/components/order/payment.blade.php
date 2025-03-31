<x-order.section>
    <x-slot:header>
        <h3 class="md:text-[18px] font-bold">Способ оплаты</h3>
    </x-slot:header>

    <div
        x-data="paymentOrder"
        class="space-y-4 relative"
        :class="{'loading': isLoading}"
    >
        @foreach ($paymentTypes as $type)
            <div
                wire:click="set('selectedPaymentType', '{{ $type['id'] }}')"
                x-on:click="handlePaymentSelection('{{ $type['id'] }}')"
            >
                <x-order.radio-element
                    id="{{ $type['id'] }}"
                    activeId="{{ $selectedPaymentType }}"
                >
                    {{ $type['label'] }}
                    <x-slot:end>
                        <img
                            src="{{ asset('/storage/' . $type['icon']) }}"
                            alt="{{ $type['label'] }}"
                        />
                    </x-slot:end>
                </x-order.radio-element>
            </div>
        @endforeach
        <input
            wire:model="selectedPaymentType"
            value="{{ old('payment_type') }}"
            type="hidden"
            name="payment_type"
        >

        @error('payment_type')
        <p class="mt-2 text-sm text-red">{{ $message }}</p>
        @enderror

        <div x-show="isLoading" class="absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center">
             <div class="loader"></div>
        </div>
    </div>

</x-order.section>

<style>
    .loading {
        opacity: 30%;
        pointer-events: none;
    }

    .loader {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #000000;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('paymentOrder', () => ({
            isLoading: false,

            handlePaymentSelection(id) {
                if (!this.isLoading) {
                    this.isLoading = true;
                    this.$wire.set('selectedPaymentType', id).then(() => {
                        this.isLoading = false;
                    });
                }

                console.log('handlePaymentSelection', this.isLoading)
            }
        }));
    });
</script>
