<template>
    <div class="auth-form__wrapper">
        <form class="auth-form form-container">
            <div>
                <div class="label-box">
                    <label for="user-tel">Телефон</label>
                </div>
                <div class="input-box auth-form__phone">
                    <input v-model="phone" class="form-phone" type="tel" name="tel" id="user-tel" axlength="12"/>
                </div>
                <div class="label-box">
                    <label for="password">Пароль</label>
                </div>
                <div class="input-box auth-form__phone">
                    <input v-model="password" class="form-phone" type="password" name="password"/>
                </div>
                <div class="auth-form__button btn" @click="sendForm">Войти</div>
            </div>
            <div v-if="errorText" style="color:red">
                {{ errorText }}
            </div>
            <span class="auth-form__reg-text">Указывая номер телефона, вы принимаете условия
          <br>
          <a href="/agree-text">пользовательского соглашения</a></span>
            <div class="auth-form__reg-box">
                <p class="auth-form__reg-text">Если Вы впервые на сайте, заполните, пожалуйста, регистрационную форму.</p>
                <a href="/register" class="auth-form__reg-link btn btn-white">Зарегистрироваться</a>
            </div>
        </form>
    </div>

</template>

<script>
import axios from "axios";

export default {
    name: "LoginComponent",
    data() {
        return {
            phone: '',
            password: '',
            errorText: ''
        }
    },

    methods: {
        sendForm() {
            this.errorText = ''
            axios.post(window.location.origin + `/login`, {
                password: this.password,
                phone: this.phone
            }).then(response => {
                if (response.data.status === 'success') {
                    location.href = '/personal'
                } else {
                    this.errorText = 'Неправильный телефон или пароль'
                }
            }).catch(error => {
                for (let key in error.response.data.errors) {
                    this.errorText += error.response.data.errors[key]+' '
                }
            });
        }
    }
}
</script>

<style scoped lang="scss">
.carts-product__button_disabled {
    pointer-events: none;
}

</style>
