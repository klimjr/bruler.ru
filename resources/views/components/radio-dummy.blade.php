@props([
'active' => false,
])

<div class="radio" :class="{ 'radio_active': {{ $active ? 'true' : 'false' }} }">
    {{ $slot }}
</div>

<style>
    .radio {
        width: 20px;
        height: 20px;
        border-radius: 12px;
        border: 1px solid var(--color-111);
        background: #ffffff;
        transition: border-width 0.2s, border-color 0.2s;
    }
    .radio.radio_active {
        border: 6px solid var(--black);
    }

    @media (hover: hover) {
        .radio:hover {
            border-width: 2px;
        }
        .radio.radio_active:hover {
            border: 6px solid var(--grey-300);
        }
    }



</style>
