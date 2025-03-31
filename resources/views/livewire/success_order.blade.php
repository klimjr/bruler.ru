<div class="pt-[125px] pb-[100px] md:pb-[160px] md:pt-[225px]" x-init="createDataLayerPurchase()" x-data='{
        order_id: 0,
        order_products: [],
        promocode: null,
        createDataLayerPurchase() {
             // if (document.cookie.split(";").some((item) => item.trim().startsWith("YMPurchaseSaved"))) return false
            this.order_id = {{ !is_null(request("order_id")) ? request("order_id") : 0 }}
            this.promocode = "{{ !is_null(request("promocode")) ? request("promocode") : 0 }}"
            this.order_products = {{ !is_null(json_encode(request("order_products"))) ? json_encode(request("order_products")) : [] }}
            if (!this.order_products || this.order_products.length === 0 || this.order_id === 0) return false

            let actionField = {
                "id" : this.order_id,
            }

            if (this.promocode != 0) actionField.coupon = this.promocode

            window.dataLayer.push({
                "ecommerce": {
                    "currencyCode": "RUB",
                    "purchase": {
                        "actionField": actionField,
                        "products": this.order_products.map((product) => {
                            if (!product.variant) return null;
                            return {
                                id: product.id,
                                name: product.name,
                                price: product.price,
                                variant: product.variant.toString(),
                                quantity: product.quantity,
                             }
                        })
                    }
                }
            })
            let expireDate = new Date()
            expireDate.setTime(expireDate.getTime() + (60 * 60 * 1000))
            document.cookie = `YMPurchaseSaved=true; expires=${expireDate.toUTCString()}; path=/`
        }
     }'>
    <div class="flex justify-end items-center px-6 pb-3 md:pb-24 lg:px-24 hide_in_mobile">
        <a class="h2 flex items-center space-x-2" href="/">
            <span>вернуться к покупкам</span>
            <x-icons.arrow-right-long/>
        </a>
    </div>
    <div class="flex flex-col items-center justify-center space-y-4">
        <h2 class="h1">cпасибо за заказ!</h2>
        <p class="main-text">Мы свяжемся с вами в ближайшее время!</p>
    </div>
</div>
