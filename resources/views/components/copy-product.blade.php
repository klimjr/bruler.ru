<style>
    .copy-title-button {
        width: 37px;
        padding: 6px;
        border-radius: .5rem;
        border-width: 1px;
        cursor: pointer;
    }
</style>

<div class="copy-title-button">
    <img src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/copy.svg') }}">
</div>

<script>
    if (typeof window.copyButtonInitialized === 'undefined') {
        window.copyButtonInitialized = false;
    }

    document.addEventListener("DOMContentLoaded", () => {
        if (!window.copyButtonInitialized) {
            const grids = document.querySelectorAll('[data-product]');

            grids.forEach(element => {
                let button = element.querySelector('.copy-title-button');
                element.children[0].classList.add('items-end');
                button.dataset.id = element.dataset.product;
                button.addEventListener('click', function () {
                    copyToClipboard(this.dataset.id);
                });
            });

            window.copyButtonInitialized = true; // Обновляем состояние выполнения
        }
    });

    function copyToClipboard(product) {
        let copyText = document.querySelector(`.product-select-${product}`).querySelector('select');
        
        navigator.clipboard.writeText(copyText.innerText)
            .then(() => {
                new window.FilamentNotification()
                    .title('Название товара скопировано!')
                    .icon('heroicon-o-document-text')
                    .iconColor('success')
                    .send();
            })
            .catch(err => {
                console.error('Failed to copy text: ', err);
            });
    }
</script>