<template>
    <div class="cart__main-block-wrapper">
        <h3 class="cart__block-title">ВЫБЕРИТЕ СПОСОБ ДОСТАВКИ</h3>
        <div class="cart__delivery-list delivery-list">
            <div class="cart__delivery-item ">
                <input id="radio-1" type="radio" name="radio" value="1" v-model="delivery">
                <label class="delivery-list__item" for="radio-1">
                    <img src="/images/product-detail/delivery-ic-01.svg" alt="ic">
                    <p>Самовывоз из пункта выдачи:<br> МО, г. Домодедово, мкр. Белые столбы, ул. Авенариуса С 6</p>
                </label>
            </div>
            <div class="cart__delivery-item">
                <input id="radio-2" type="radio" name="radio" value="2" v-model="delivery">
                <label class="delivery-list__item" for="radio-2"><img src="/images/product-detail/delivery-ic-02.svg"
                                                                      alt="ic">
                    <p>Доставка ТК до терминала:
                        <br> СДЭК, ПЭК, Байкал - сервис или Деловые линии.</p></label>
            </div>
            <div class="cart__delivery-item">
                <input id="radio-3" type="radio" name="radio" value="3" v-model="delivery">
                <label class="delivery-list__item" for="radio-3"><img src="/images/product-detail/delivery-ic-03.svg"
                                                                      alt="ic">
                    <p>Адресная доставка
                        <br> транспортной компанией или курьером</p></label>
            </div>
        </div>
    </div>
</template>

<script>

import {mapGetters} from "vuex";

export default {
    name: "DeliveryComponent",

    data() {
        return {

        }
    },
    mounted() {

    },
    computed: {
        delivery: {
            get() {
                return this.$store.state.basket.delivery
            },
            set(value) {
                this.$store.commit('basket/setDelivery', value)
            }
        },
    },

    methods: {

    },
}
</script>

