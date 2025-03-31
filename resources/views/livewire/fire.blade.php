<div x-data="favoriteButton">
    <button
        @click="updateIsAuthorized"
        wire:click.prevent="setFavouriteProduct"
        class="{{ $this->getButtonClasses($isActive, $isFavourite) }}"
    >
        @if ($isFavourite)
            <x-icons.favourite-solid class="text-red w-full max-w-6 max-h-6 h-full" />
        @else
            <x-icons.favourite-empty class="w-full max-w-6 max-h-6 h-full" />
        @endif
    </button>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('favoriteButton', () => ({
            isAuthorized: true,

            updateIsAuthorized() {
                this.isAuthorized = @json(Auth::check());
                if (!this.isAuthorized)
                    setTimeout(() => { this.isAuthorized = true }, 5000)
            },
        }));
    });
</script>
