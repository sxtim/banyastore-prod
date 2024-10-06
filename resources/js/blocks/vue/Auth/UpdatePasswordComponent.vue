<template>
    <div>
        <div class="login-section__title-container">
            <h1 class="login-section__title title-s">Сброс пароля</h1>
            <p class="login-section__subtitle">Пожалуйста, укажите Ваш email и новый пароль</p>
        </div>
        <div class="auth-form__wrapper">
            <form class="auth-form form-container">
                <div>
                    <div class="label-box">
                        <label for="user-mail">Email</label>
                    </div>
                    <div class="input-box auth-form__phone">
                        <input v-model="email" class="form-phone" type="email" name="email" id="user-mail" />
                    </div>
                    <div class="label-box">
                        <label for="password">Пароль</label>
                    </div>
                    <div class="input-box auth-form__phone">
                        <input v-model="password" class="form-phone" type="password" name="password"/>
                    </div>
                    <div class="label-box">
                        <label for="password_confirmation">Подтвердите пароль</label>
                    </div>
                    <div class="input-box auth-form__phone">
                        <input v-model="passwordConfirmation" class="form-phone" type="password" name="password_confirmation"/>
                    </div>
                    <div class="auth-form__button btn" @click="sendForm">Обновить</div>
                </div>
                <div v-if="errorText" style="color:red">
                    {{ errorText }}
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import ResetPasswordComponent from "./ResetPasswordComponent";

export default {
    name: "UpdatePasswordComponent",
    data() {
        return {
            email: '',
            password: '',
            passwordConfirmation: '',
            errorText: ''
        }
    },
    props: {
        token: {
            type: String,
            required: true,
        }
    },
    methods: {
        sendForm() {
            this.errorText = ''
            axios.post(window.location.origin + `/update-password`, {
                password: this.password,
                token: this.token,
                email: this.email,
                password_confirmation: this.passwordConfirmation
            }).then(response => {
                if (response.data.status === 'success') {
                    location.href = '/login'
                } else {
                    this.errorText = 'Неправильный email или ссылка устарела'
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
