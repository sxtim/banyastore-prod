<template>
    <div>
        <div v-if="checkResetPassword === false">
            <div class="login-section__title-container">
                <h1 class="login-section__title title-s">Вход</h1>
                <p class="login-section__subtitle">Пожалуйста, авторизуйтесь</p>
            </div>
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
                    <div class="my-link-green" @click="checkResetPassword = true">
                        Забыл пароль
                    </div>
                    <div class="auth-form__reg-box">
                        <p class="auth-form__reg-text">Если Вы впервые на сайте, заполните, пожалуйста, регистрационную форму.</p>
                        <a href="/register" class="auth-form__reg-link btn btn-white">Зарегистрироваться</a>
                    </div>
                </form>
            </div>
        </div>
        <div v-if="checkResetPassword === true">
            <div class="my-link-green" @click="checkResetPassword = false">
                Назад
            </div>
            <div class="login-section__title-container">
                <h1 class="login-section__title title-s">Восстановление пароля</h1>
                <p class="login-section__subtitle">Пожалуйста, введите свой email</p>
            </div>
            <div class="auth-form__wrapper">
                <reset-password-component></reset-password-component>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import ResetPasswordComponent from "./ResetPasswordComponent";

export default {
    name: "LoginComponent",
    components: {ResetPasswordComponent},
    data() {
        return {
            phone: '',
            password: '',
            errorText: '',
            checkResetPassword: false
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
.my-link-green {
    text-decoration: underline;
    color: var(--green-dark);
    cursor: pointer;
}

</style>
