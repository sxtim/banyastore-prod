<template>
    <div class="auth-form__wrapper">
        <form class="auth-form form-container">
            <div v-if="isSendPhone === false">
                <div class="label-box">
                    <label for="user-tel">Телефон</label>
                </div>
                <div class="input-box auth-form__phone">
                    <input v-model="phone" class="form-phone" type="tel" name="tel" id="user-tel" axlength="12"/>
                </div>
                <div class="input-box">
                    <button class="auth-form__btn-sms" @click="sendPhone">Получить код</button>
                </div>
            </div>
            <div v-else>
                <div class="label-box">
                    <label for="input-sms-pass" >Код из СМС</label>
                </div>
                <div class="input-box">
                    <input id="input-sms-pass" type="text" v-model="sms" name="user-password" placeholder="" minlength="8" />
                </div>
                <div class="auth-form__button btn">Войти</div>
            </div>
            <div v-if="errorText" style="color:red">
                {{ errorText }}
            </div>
            <span class="auth-form__reg-text">Указывая номер телефона, вы принимаете условия
          <br>
          <a href="#!">пользовательского соглашения</a></span>
            <div class="auth-form__reg-box">
                <p class="auth-form__reg-text">Если Вы впервые на сайте, заполните, пожалуйста, регистрационную форму.</p>
                <a href="signup-old.html" class="auth-form__reg-link btn btn-white">Зарегистрироваться</a>
            </div>
        </form>
    </div>

</template>

<script>

import {mapGetters} from "vuex";
import axios from "axios";


export default {
    name: "LoginComponent",
    data() {
        return {
            isSendPhone: false,
            phone: '',
            sms: '',
            errorText: ''
        }
    },
    mounted() {
    },
    computed: {

    },

    methods: {
        sendPhone(e) {
            this.errorText = ''
            axios.post(window.location.origin + `/ajax/send-phone`, {
                phone: this.phone
            }).then(response => {
                if (response.data.status === 'success') {
                    this.isSendPhone = true
                } else {
                    this.errorText = 'Что-то пошло не так!'
                }
            })

            e.preventDefault();
        },

        sendSms() {
            this.errorText = ''
            axios.post(window.location.origin + `/ajax/send-sms`, {
                sms: this.sms,
                phone: this.phone
            }).then(response => {
                if (response.data.status === 'success') {
                    location.href = '/'
                } else {
                    this.errorText = 'Неправильный код!'
                }
            })
        }
    },
}
</script>

<style scoped lang="scss">
.carts-product__button_disabled {
    pointer-events: none;
}

</style>
