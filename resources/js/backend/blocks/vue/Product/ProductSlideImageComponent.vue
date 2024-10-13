<template>
    <div v-if="viewImg">
        <img :src="imgSrc" alt="" class="img-fluid" >
        <span class="btn-del-slide-img" @click="delImg">
            Удалить
        </span>
    </div>
</template>

<script>

import axios from "axios";

export default {
    name: "ProductSlideImageComponent",
    props: {
        imgSrc: {
            type: String,
            required: true
        },
        id: {
            type: Number,
            required: true
        },
        link: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            viewImg: true
        }
    },

    methods: {
        delImg() {
            axios.post(this.link, {
                id: this.id
            }).then(response => {
                if (response.data.status === 'success') {
                    this.viewImg = false
                }
            })
        }
    },
}
</script>

<style scoped lang="scss">
.btn-del-slide-img {
    text-decoration: underline;
    cursor:pointer;
    color: #03a2ff
}
.img-fluid {
    max-width:150px;
}
</style>
