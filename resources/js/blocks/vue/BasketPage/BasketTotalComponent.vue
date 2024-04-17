<template>
    <div class="cart__total-container">
        <div class="cart__total-row">
            <span class="cart__total-cell-title cart__total-goods">Товары:</span>
            <span class="cart__total-cell-value cart__total-goods-sum">{{ oldPrice }} ₽ </span>
        </div>
        <div class="cart__total-row">
            <span class="cart__total-cell-title cart__total-discount">Скидка:</span>
            <span class="cart__total-cell-value cart__total-discount-sum">{{ discount }} ₽</span>
        </div>
        <div class="cart__total-row">
            <span class="cart__total-cell-title cart__total">Итого:</span>
            <span class="cart__total-cell-value cart__total-sum">{{ price }} ₽</span>
        </div>
    </div>
</template>

<script>

import {mapGetters} from "vuex";

export default {
    name: "BasketTotalComponent",

    data() {
        return {

        }
    },
    mounted() {

    },
    computed: {
        ...mapGetters({
            oldPrice: "basket/getTotalOldPrice",
            price: "basket/getTotalPrice",
            discount: "basket/getTotalDiscount",
        })
    },

    methods: {

    },
}
</script>

