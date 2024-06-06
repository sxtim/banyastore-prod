<template>
    <div>
        <select v-model="statusId">
            <option v-for="(item,index) in statuses"
                    :key="item.id"
                    :value="item.id"
            >
                {{ item.name }}
            </option>
        </select>
         <span class="link-save-backend-vue" @click="updateStatus">Сохранить</span>
    </div>
</template>

<script>

import axios from "axios";

export default {
    name: "UpdateStatusOrderComponent",
    props: {
        defaultStatusId: {
            type: Number,
            required: true,
        },
        link: {
            type: String,
            required: true,
        },
        statuses: {
            type: Object,
            required: true,
        }
    },
    data() {
        return {
            statusId: ''
        }
    },
    mounted() {
        if (this.defaultStatusId) {
            this.statusId = this.defaultStatusId
        }
    },


    methods: {
        updateStatus() {
            axios.post(this.link , {
                status_id: this.statusId
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
