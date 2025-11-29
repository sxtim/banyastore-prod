<template>
    <div id="cookieBanner" class="cookie-banner" v-if="isView">
        <span>На нашем сайте мы используем cookie-файлы</span>
        <button id="cookieOkBtn" @click="acceptCookies">OK</button>
    </div>
</template>

<script>
export default {
    name: "CookieComponent",
    data() {
        return {
            isView: false
        }
    },
    mounted() {
        const isAccepted = localStorage.getItem('cookieAccepted');

        if (!isAccepted) {
            this.isView = true;
        }
    },


    methods: {
        acceptCookies() {
            localStorage.setItem('cookieAccepted', '1');
            this.isView = false;
        }
    },
}
</script>

<style scoped lang="scss">
.cookie-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #2b2f3a;
    color: #fff;
    padding: 14px 20px;
    display: flex;
    justify-content: center; /* Центрируем содержимое */
    align-items: center;
    gap: 16px; /* Отступ между текстом и кнопкой */
    font-family: sans-serif;
    font-size: 14px;
    z-index: 9999;
}

.cookie-banner button {
    padding: 6px 14px;
    background: #4f8cff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}
</style>
