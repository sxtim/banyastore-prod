<template>
    <div>
        <div v-if="isShow === false">
            <div>
                {{ userName }} <span class="link-save-backend-vue" @click="isShow = true">Редактировать</span>
            </div>
        </div>
        <div v-else>
            <input type="number" v-model="userId"/>
            <span class="link-save-backend-vue" @click="updateUser">Сохранить</span>
        </div>
    </div>
</template>

<script>

import axios from "axios";

export default {
    name: "UpdateUserOrderComponent",
    props: {
        defaultUserName: {
            type: String,
            required: false,
        },
        link: {
            type: String,
            required: true,
        }
    },
    data() {
        return {
            isShow: false,
            userName: '',
            userId: ''
        }
    },
    mounted() {
        if (this.defaultUserName) {
            this.userName = this.defaultUserName
        }
    },


    methods: {
        updateUser() {
            axios.post(this.link , {
                user_id: this.userId
            }).then(response => {
                if (response.data.status === 'success') {
                    this.userName = response.data.name
                    this.isShow = false
                }
            });
        }
    },
}
</script>

<style scoped lang="scss">
.link-save-backend-vue {
    color: #03a2ff;
    cursor: pointer;
    margin-left: 10px;
    font-size: 14px;
    text-decoration: underline;
}

</style>
