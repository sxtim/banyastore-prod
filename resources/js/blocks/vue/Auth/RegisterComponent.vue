<template>
    <div class="auth-form__wrapper">
        <form id="reg-form" action="" class="reg-form form-container">
            <div>
                <div class="label-box">
                    <label for="user-name">Имя</label>
                </div>
                <div class="input-box">
                    <input id="user-name" type="text" name="user-name" placeholder="" v-model="name"/>
                </div>
                <div class="label-box">
                    <label for="user-surname">Фамилия</label>
                </div>
                <div class="input-box">
                    <input id="user-surname" type="text" name="user-surname" placeholder="" v-model="surname"/>
                </div>
                <div class="label-box">
                    <label for="user-tel">Телефон</label>
                </div>
                <div class="input-box">
                    <input class="form-phone" type="tel" name="tel" id="user-tel" v-model="phone"/>
                </div>
                <div class="label-box">
                    <label for="user-email">Электронная почта</label>
                </div>
                <div class="input-box">
                    <input id="user-email" type="email" name="email" placeholder="" v-model="email"/>
                </div>
            </div>
            <div v-if="isSendPhone === true">
                <div class="label-box">
                    <label for="input-sms-pass" >Код из СМС</label>
                </div>
                <div class="input-box">
                    <input id="input-sms-pass" type="text" v-model="sms" name="user-password" placeholder="" minlength="8" />
                </div>
            </div>
            <button class="reg-form__btn-reg btn" @click="registerBtn">Зарегистироваться</button>
            <div v-if="errorText" style="color:red">
                {{ errorText }}
            </div>
            <p class="registration-form__text">Нажимая кнопку «Зарегистрироваться», я даю свое согласие на обработку моих
                персональных данных, в соответствии с Федеральным законом от 27.07.2006 года №152-ФЗ «О персональных данных»,
                на условиях и для целей, определенных в Согласии на обработку персональных данных</p>
            <a href="/login" class="registration-form__log-link btn-white btn">Авторизация</a>
        </form>
    </div>

</template>

<script>

import {mapGetters} from "vuex";
import axios from "axios";


export default {
    name: "RegisterComponent",
    data() {
        return {
            isSendPhone: false,
            name: '',
            surname: '',
            email: '',
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
        registerBtn(e) {
            this.isSendPhone === true ? this.registerBtn() : this.sendPhone(e)
        },

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
            axios.post(window.location.origin + `/ajax/register-sms`, {
                sms: this.sms,
                name: this.name,
                surname: this.surname,
                email: this.email,
                phone: this.phone
            }).then(response => {
                if (response.data.status === 'success') {
                    location.href = '/'
                } else {
                    this.errorText = 'Неправильный код!'
                }
            }).catch(error => {
                for (let key in error.response.data.errors) {
                    this.errorText += error.response.data.errors[key]+ ' '
                }
            });
        }
    },
}
</script>

<style scoped lang="scss">
.carts-product__button_disabled {
    pointer-events: none;
}

</style>
