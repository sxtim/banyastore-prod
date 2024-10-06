<template>
    <div>
        <form class="auth-form form-container">
            <div>
                <div class="label-box">
                    <label for="user-mail">Email</label>
                </div>
                <div class="input-box auth-form__phone">
                    <input v-model="email" class="form-phone" type="email" name="user-mail" id="user-mail" />
                </div>
                <div class="auth-form__button btn" @click="sendForm">Отправить</div>
            </div>
            <div v-if="successText" style="color:green">
                {{ successText }}
            </div>
            <div v-if="errorText" style="color:red">
                {{ errorText }}
            </div>
        </form>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "ResetPasswordComponent",
    data() {
        return {
            email: '',
            errorText: '',
            successText: ''
        }
    },

    methods: {
        sendForm() {
            this.errorText = ''
            this.successText = ''
            axios.post(window.location.origin + `/forgot-password`, {
                email: this.email
            }).then(response => {
                if (response.data.status === 'success') {
                    this.email = ''
                    this.successText = 'Мы отправили на Ваш email данные по восстановлению пароля'
                } else {
                    this.errorText = 'Что-то пошло не так'
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

</style>
