<template>
    <div class="auth-form__wrapper">
        <form class="auth-form form-container">
            <div class="label-box">
                <label for="user-name">Имя</label>
            </div>
            <div class="input-box">
                <input id="user-name" type="text" name="user-name" placeholder="" v-model="name"/>
            </div>
            <div class="label-box">
                <label for="user-tel">Телефон</label>
            </div>
            <div class="input-box auth-form__phone">
                <input v-model="phone" class="form-phone" type="tel" name="tel" id="user-tel" axlength="12"/>
            </div>
            <div class="label-box">
                <label for="feedback-text">Введите сообщение</label>
            </div>
            <div>
                    <textarea
                        class="input-box-text"
                        id="feedback-text"
                        wrap="soft"
                        name="feedback-text"
                        v-model="text"
                        placeholder=""></textarea>
            </div>
            <div class="auth-form__button btn" @click="sendForm">Отправить</div>
            <div style="color: green" v-if="successText">
                {{ successText }}
            </div>
            <div style="color: red" v-if="errorText">
                {{ errorText }}
            </div>
            <span class="auth-form__reg-text">Указывая номер телефона, вы принимаете условия
          <br>
          <a href="/agree-text">пользовательского соглашения</a></span>
        </form>
    </div>

</template>

<script>
import axios from "axios";

export default {
    name: "FeedbackPage",
    data() {
        return {
            text: '',
            phone: '',
            name: '',
            errorText: '',
            successText: ''
        }
    },
    methods: {
        sendForm() {
            this.errorText = ''
            this.successText = ''
            axios.post(window.location.origin + `/ajax/feedback`, {
                name: this.name,
                phone: this.phone,
                mess: this.text
            }).then(response => {
                if (response.data.status === 'success') {
                    this.name = ''
                    this.phone = ''
                    this.text = ''
                    this.successText = 'Ваше обращение успешно оставлено, мы свяжемся с Вами в ближайшее время!'
                } else {
                    this.errorText = 'Неправильный код!'
                }
            }).catch(error => {
                for (let key in error.response.data.errors) {
                    this.errorText += error.response.data.errors[key]+ ' '
                }
            })
        }
    },
}
</script>

<style scoped lang="scss">

</style>
