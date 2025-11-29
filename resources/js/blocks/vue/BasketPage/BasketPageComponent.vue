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
                    <div class="agree-wrapper">
                        <label class="agree-label">
                            <input type="checkbox" v-model="checkAgree">
                            <span class="custom-checkbox"></span>
                            <div class="agree-text">
                                я ознакомлен(-а) с
                                <a href="/docs/privacy-policy.docx">Политикой обработки персональных данных</a>
                                и даю свое
                                <a href="/docs/agreement.docx">согласие на обработку</a> персональных данных
                            </div>
                        </label>
                        <div class="agree-error" v-if="errorAgree">
                            Необходимо Ваше согласие
                        </div>
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
            checkAgree: false,
            errorAgree: false
        }
    },
    mounted() {
    },
    computed: {
        ...mapGetters({
            products: "basket/getProducts",
            errors: "basket/getErrors"
        })
    },

    methods: {
        store() {
            this.errorAgree = false
            if (this.checkAgree) {
                this.$store.dispatch('basket/store');
            } else {
                this.errorAgree = true
            }
        }
    },
}
</script>

<style scoped lang="scss">
.carts-product__button_disabled {
    pointer-events: none;
}
.agree-wrapper {
    font-family: 'Arial', sans-serif;
    font-size: 14px;
    color: #333;
    margin: 16px 0;

    a {
        color: #4f8cff;
        text-decoration: none;
        &:hover {
            text-decoration: underline;
        }
    }
}

.agree-label {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
    position: relative;

    input[type="checkbox"] {
        display: none; // скрываем стандартный чекбокс
    }

    .custom-checkbox {
        width: 18px;
        height: 18px;
        border: 2px solid #ccc;
        border-radius: 4px;
        background: #fff;
        flex-shrink: 0;
        position: relative;
        transition: all 0.2s ease;

        &:after {
            content: '';
            position: absolute;
            top: 2px;
            left: 6px;
            width: 4px;
            height: 8px;
            border: solid #4f8cff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            opacity: 0;
            transition: opacity 0.2s ease;
        }
    }

    input[type="checkbox"]:checked + .custom-checkbox:after {
        opacity: 1;
    }

    .agree-text {
        line-height: 1.4;
        color: #333;
    }
}

.agree-error {
    color: #e74c3c;
    font-size: 13px;
    margin-top: 4px;
}
</style>
