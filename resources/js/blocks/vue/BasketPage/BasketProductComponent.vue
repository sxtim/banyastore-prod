<template>
    <div class="cart__wrapper-card">
        <article class="cart-card" v-for="product in products">
            <img class="cart-card__picture" :src="product.image" alt="cart-img">
            <div class="cart-card__desc">
                <h2 class="cart-card__title">
                    {{ product.name }}
                </h2>
                <div class="cart-card__price-container">
                    <span class="cart-card__price">
                        {{ product.price * product.quantity }} ₽
                    </span>
                    <span class="cart-card__price-old" v-if="product.oldPrice > product.price">
                        {{ product.oldPrice * product.quantity }} ₽
                    </span>
                </div>
                <div class="cart-card__count count">
                    <div class="count__wrap">
                        <button type="button" class="count__minus" :class="{ 'minus-disabled': product.quantity === 1 }" @click="decrement(product.productId, product.quantity)"></button>
                        <div class="count__current">
                            {{ product.quantity }}
                        </div>
                        <button type="button" class="count__plus" @click="increment(product.productId)"></button>
                    </div>
                </div>
                <div class="cart-card__btns">
                    <div class="card__fav">
                        <button class="card__btn-add-to-fav">
                            <svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill="transparent"
                                      d="M20.8413 3.55044C20.3305 3.03944 19.7241 2.63409 19.0566 2.35752C18.3892 2.08096 17.6738 1.93861 16.9513 1.93861C16.2288 1.93861 15.5134 2.08096 14.8459 2.35752C14.1785 2.63409 13.572 3.03944 13.0613 3.55044L12.0013 4.61044L10.9413 3.55044C9.90959 2.51875 8.51031 1.93915 7.05128 1.93915C5.59225 1.93915 4.19297 2.51875 3.16128 3.55044C2.12959 4.58213 1.54999 5.98141 1.54999 7.44044C1.54999 8.89947 2.12959 10.2987 3.16128 11.3304L4.22128 12.3904L12.0013 20.1704L19.7813 12.3904L20.8413 11.3304C21.3523 10.8197 21.7576 10.2133 22.0342 9.54579C22.3108 8.87834 22.4531 8.16293 22.4531 7.44044C22.4531 6.71795 22.3108 6.00254 22.0342 5.33508C21.7576 4.66763 21.3523 4.0612 20.8413 3.55044V3.55044Z"
                                      stroke="#8a8a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <button class="card__btn-delete" @click="remove(product.productId)">
                        <svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M3.75 4.8125V17.875C3.75 18.7866 4.14525 19.6611 4.848 20.306C5.5515 20.9502 6.5055 21.3125 7.5 21.3125C10.1145 21.3125 13.8855 21.3125 16.5 21.3125C17.4945 21.3125 18.4485 20.9502 19.152 20.306C19.8547 19.6611 20.25 18.7866 20.25 17.875V4.8125H22.5C22.914 4.8125 23.25 4.5045 23.25 4.125C23.25 3.7455 22.914 3.4375 22.5 3.4375H1.5C1.086 3.4375 0.75 3.7455 0.75 4.125C0.75 4.5045 1.086 4.8125 1.5 4.8125H3.75ZM18.75 4.8125V17.875C18.75 18.4223 18.513 18.9468 18.0908 19.3332C17.6693 19.7203 17.097 19.9375 16.5 19.9375C13.8855 19.9375 10.1145 19.9375 7.5 19.9375C6.903 19.9375 6.33075 19.7203 5.90925 19.3332C5.487 18.9468 5.25 18.4223 5.25 17.875V4.8125H18.75Z"
                                  fill="#8a8a8a" stroke-width="2"/>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M8.25 2.0625H15.75C16.164 2.0625 16.5 1.7545 16.5 1.375C16.5 0.9955 16.164 0.6875 15.75 0.6875H8.25C7.836 0.6875 7.5 0.9955 7.5 1.375C7.5 1.7545 7.836 2.0625 8.25 2.0625Z"
                                  fill="#8a8a8a"/>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M9 8.25V16.5C9 16.8795 9.336 17.1875 9.75 17.1875C10.164 17.1875 10.5 16.8795 10.5 16.5V8.25C10.5 7.8705 10.164 7.5625 9.75 7.5625C9.336 7.5625 9 7.8705 9 8.25Z"
                                  fill="#8a8a8a"/>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M13.5 8.25V16.5C13.5 16.8795 13.836 17.1875 14.25 17.1875C14.664 17.1875 15 16.8795 15 16.5V8.25C15 7.8705 14.664 7.5625 14.25 7.5625C13.836 7.5625 13.5 7.8705 13.5 8.25Z"
                                  fill="#8a8a8a"/>
                        </svg>
                    </button>
                </div>
            </div>
        </article>
    </div>
</template>

<script>

import {mapGetters} from "vuex";

export default {
    name: "BasketProductComponent",

    data() {
        return {

        }
    },
    mounted() {

    },
    computed: {
        ...mapGetters({
            products: "basket/getProducts",
        })
    },

    methods: {
        increment(productId) {
            this.$store.dispatch('basket/incrementProduct', {
                productId: productId
            });
        },
        decrement(productId, quantity) {
            if (quantity > 1) {
                this.$store.dispatch('basket/decrementProduct', {
                    productId: productId
                });
            }
        },
        remove(productId) {
            this.$store.dispatch('basket/removeProduct', {
                productId: productId
            });
        },
    },
}
</script>

