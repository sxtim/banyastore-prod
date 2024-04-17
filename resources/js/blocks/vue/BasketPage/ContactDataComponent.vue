<template>
    <div class="cart__main-block-wrapper">
        <div class="cart__user-data">
            <h3 class="cart__block-title">Проверьте контактные данные</h3>
            <div class="user-data">

                <div class="user-data__form-container">
                    <form class="user-data__form user-form">
                        <label class="user-form__name">Имя</label>
                        <input
                            type="text"
                            name="name"
                            minlength="3"
                            axlength="20"
                            v-model="name"
                        />
                        <label class="user-form__phone">Телефон</label>
                        <input
                            type="tel"
                            name="reference"
                            id="user-form-tel"
                            axlength="12"
                            v-model="phone"
                        />
                        <label class="user-form__email">Почта</label>
                        <input
                            type="email"
                            v-model="mail"
                        />
                        <!--            <label class="i'm not robot">I'm not Robot</label>-->
                        <!--            <input type="checkbox" name="I'm not Robot" id="I'm not Robot" required/>-->

                    </form>
                    <form class="user-data__form user-form">
                        <label class="user-form__city">Город</label>
                        <input
                            type="text"
                            minlength="3"
                            axlength="20"
                            v-model="city"
                        />
                        <label class="user-form__street">Улица</label>
                        <input
                            type="text"
                            v-model="street"
                        />
                        <label class="user-form__build">Строение/Дом</label>
                        <input
                            type="text"
                            v-model="house"
                        />
                        <!--            <label class="i'm not robot">I'm not Robot</label>-->
                        <!--            <input type="checkbox" name="I'm not Robot" id="I'm not Robot" required/>-->
                    </form>

                </div>
                <!--              <button class="user-form-btn btn btn-medium" type="submit">Сохранить</button>-->
            </div>
        </div>
    </div>
</template>

<script>

import {mapGetters} from "vuex";

export default {
    name: "ContactDataComponent",

    data() {
        return {

        }
    },
    mounted() {

    },
    computed: {
        name: {
            get() {
                return this.$store.state.basket.name
            },
            set(value) {
                this.$store.commit('basket/setName', value)
            }
        },
        phone: {
            get() {
                return this.$store.state.basket.phone
            },
            set(value) {
                this.$store.commit('basket/setPhone', value)
            }
        },
        mail: {
            get() {
                return this.$store.state.basket.mail
            },
            set(value) {
                this.$store.commit('basket/setMail', value)
            }
        },
        city: {
            get() {
                return this.$store.state.basket.city
            },
            set(value) {
                this.$store.commit('basket/setCity', value)
            }
        },
        street: {
            get() {
                return this.$store.state.basket.street
            },
            set(value) {
                this.$store.commit('basket/setStreet', value)
            }
        },
        house: {
            get() {
                return this.$store.state.basket.house
            },
            set(value) {
                this.$store.commit('basket/setHouse', value)
            }
        },
    },

    methods: {

    },
}
</script>

