<template>
    <section class="cart section">
        <div class="container" v-if="products.length > 0">
            <h1 class="cart__title title-s">Корзина</h1>
            <div class="cart__container">
                <div class="cart__wrapper-main">
                    <basket-product-component></basket-product-component>
                    <delivery-component></delivery-component>
                    <pay-variants-component></pay-variants-component>
                    <contact-data-component></contact-data-component>
                </div>
                <div class="cart__sidebar-sticky">
                    <basket-total-component></basket-total-component>
                    <!-- acc single -->
<!--                    <div class="cart-discount__acc js-acc-single">-->
<!--                        <div class="cart-discount__acc-item js-acc-item">-->
<!--                            <div class="cart-discount__acc-title js-acc-single-trigger">Промокод</div>-->
<!--                            <div class="cart-discount__acc-content">-->
<!--                                <form action="" class="cart-discount__form">-->
<!--                                    <input type="text" class="cart-discount__input">-->
<!--                                    <button class="cart__discount-btn btn btn-medium" type="submit">Применить код</button>-->
<!--                                </form>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>&lt;!&ndash; acc-single &ndash;&gt;-->
                    <div class="cart__order-btn btn btn-medium" @click="store">
                        Оформить заказ
                    </div>
                    <div style="color: red" v-for="error in errors">{{ error }}</div>
                </div>
            </div>

        </div>
        <div class="container" v-else>
            <h1 class="cart__title title-s">Корзина пуста</h1>
        </div>
    </section>

</template>

<script>

import {mapGetters} from "vuex";
import BasketProductComponent from "./BasketProductComponent";
import DeliveryComponent from "./DeliveryComponent";
import PayVariantsComponent from "./PayVariantsComponent";
import ContactDataComponent from "./ContactDataComponent";
import BasketTotalComponent from "./BasketTotalComponent";

export default {
    name: "BasketPageComponent",
    components: {
        BasketTotalComponent,
        ContactDataComponent, PayVariantsComponent, DeliveryComponent, BasketProductComponent},
    data() {
        return {

        }
    },
    mounted() {
        this.$store.dispatch('basket/initBasket');
    },
    computed: {
        ...mapGetters({
            products: "basket/getProducts",
            errors: "basket/getErrors"
        })
    },

    methods: {
        store() {
            this.$store.dispatch('basket/store');
        }
    },
}
</script>

<style scoped lang="scss">
.carts-product__button_disabled {
    pointer-events: none;
}

</style>
