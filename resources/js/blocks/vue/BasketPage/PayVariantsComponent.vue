<template>
    <div class="cart__main-block-wrapper">
        <h3 class="cart__block-title">ВЫБЕРИТЕ СПОСОБ ОПЛАТЫ</h3>
        <div class="cart__payment-method-list delivery-list">
            <div class="cart__payment-method-item">
                <input id="radio-4" type="radio" name="radio1" value="1" v-model="pay">
                <label for="radio-4">
                    <p>Картой на сайте</p> <span>Оплатить сейчас</span>
                    <div class="payment-method-item__img-container">
                        <img class="payment-method-item__img" src="/images/cart/mir.svg" alt="ic">
                        <img class="payment-method-item__img" src="/images/cart/visa.svg" alt="ic">
                    </div>
                </label>
            </div>
            <div class="cart__payment-method-item">
                <input id="radio-5" type="radio" name="radio1" value="2" v-model="pay">
                <label for="radio-5">
                    <p>Через СБП</p> <span>Оплатить сейчас</span>
                    <img class="payment-method-item__img-container" src="/images/cart/sbp.svg"
                         alt="ic">
                </label>
            </div>
            <div class="cart__payment-method-item" v-if="delivery === 1">
                <input id="radio-6" type="radio" name="radio1" value="3" v-model="pay">
                <label for="radio-6">
                    <p>При получении</p>
                    <span> Оплата картой, наличными или СБП в пункте выдачи</span></label>
            </div>
        </div>
    </div>
</template>

<script>

import {mapGetters} from "vuex";

export default {
    name: "PayVariantsComponent",

    data() {
        return {

        }
    },
    mounted() {

    },
    computed: {
        pay: {
            get() {
                return this.$store.state.basket.pay
            },
            set(value) {
                this.$store.commit('basket/setPay', value)
            }
        },
        delivery: {
            get() {
                return this.$store.state.basket.delivery
            }
        }
    },

    watch: {
        delivery(val) {
            if (val !== 1 && this.pay === 3) {
                this.pay = 1
            }
        }
    }
}
</script>

