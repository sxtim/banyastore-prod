<template>
    <table class="table table-wh table-hover table-bordered">
        <tr>
            <td class="text-center" colspan="5">Продукты</td>
        </tr>
        <tr>
            <td>
                Название
            </td>
            <td>
                Кол-во
            </td>
            <td>
                Цена за шутку
            </td>
            <td>
                Цена всего
            </td>
            <td>

            </td>
        </tr>
        <tr v-for="position in positions">
            <td>
                <span>
                    {{ position.name }}
                </span>
            </td>
            <td>
                <span>
                    {{ position.quantity }}
                </span>
            </td>
            <td>
                <span v-if="editingProductId !== position.product_id">
                    {{ position.price }}
                </span>
                <input v-else type="number" v-model="price"/>
            </td>
            <td>
                <span>
                    {{position.totalPrice }}
                </span>
            </td>
            <td>
                <span v-if="editingProductId !== position.product_id" @click="edit(position.product_id, position.price)"
                class="link-save-backend-vue "
                >
                    Редактировать
                </span>
                <span v-else @click="save(position.product_id)" class="link-save-backend-vue ">
                    Сохранить
                </span>
            </td>
        </tr>
    </table>
</template>

<script>

import axios from "axios";

export default {
    name: "UpdatePriceOrderComponent",
    props: {
        positions: {
            type: Object
        },
        link: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            editingProductId: 0,
            price: 9999999999
        }
    },

    methods: {
        edit(productId, productPrice) {
            this.editingProductId = productId
            this.price = Math.round(productPrice)
        },
        save(productId) {
            axios.post(this.link , {
                product_id: productId,
                price: this.price
            }).then(response => {
                if (response.data.status === 'success') {
                    location.reload()
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
