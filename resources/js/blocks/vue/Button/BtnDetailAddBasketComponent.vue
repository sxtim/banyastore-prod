<template>
    <button class="product-detail__btn-add-to-cart btn btn-medium" @click="addBasket" v-if="checkBasket() === false"> В корзину</button>
    <button class="product-detail__btn-add-to-cart btn btn-medium" v-else> В корзине</button>
</template>

<script>

import {mapGetters} from "vuex";

export default {
    name: "BtnDetailAddBasketComponent",

    data() {
        return {
            isActive: false,
        }
    },
    props: {
        productId: {
            type: Number,
            required: true,
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
        addBasket() {
            this.$store.dispatch('basket/addProduct', {
                productId: this.productId
            });
        },
        checkBasket() {
            let check = false;
            this.products.forEach(product => {
                if (product.productId === this.productId) {
                    check = true
                }
            });

            return check;
        }
    },
}
</script>
