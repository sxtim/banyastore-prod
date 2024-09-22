import axios from 'axios'
import {commit} from "lodash/seq";

export default {
    namespaced: true,
    state: () => ({
        name: '',
        city: '',
        phone: '',
        street: '',
        mail: '',
        house: '',
        products: [],
        count: 0,
        delivery: 1,
        pay: 1,
        errors: []
    }),
    mutations: {
        setProducts(state, products) {
            state.products = []
            for (let k in products) {
                state.products.push(products[k])
            }
            // state.products = products;
        },
        setCount(state, count) {
            state.count = count;
        },
        setDelivery(state, delivery) {
            state.delivery = delivery;
        },
        setPay(state, pay) {
            state.pay = pay;
        },
        setName(state, name) {
            state.name = name;
        },
        setPhone(state, phone) {
            state.phone = phone;
        },
        setCity(state, city) {
            state.city = city;
        },
        setStreet(state, street) {
            state.street = street;
        },
        setMail(state, mail) {
            state.mail = mail;
        },
        setHouse(state, house) {
            state.house = house;
        },
        setErrors(state, errors) {
            state.errors = errors;
        },
    },
    actions: {
        store({state, commit}) {
            axios.post(window.location.origin + `/ajax/order/store`, {
                name: state.name,
                phone: state.phone,
                mail: state.mail,
                payment_variant_id: state.pay,
                delivery_variant_id: state.delivery,
                city_name: state.city,
                street: state.street,
                house: state.house
            }).then(response => {
                if (response.data.status === 'success') {
                    location.href = response.data.link
                } else {
                    commit('setErrors', ['Что-то пошло не так'])
                }
            }).catch(error => {
                commit('setErrors', error.response.data.errors)
            });

        },
        initBasket({commit}){
            axios.post(window.location.origin + `/ajax/basket/get-basket`).then(response => {
                commit('setProducts', response.data.items);
            });
        },
        addProduct({commit}, productId) {
            axios.post(window.location.origin + `/ajax/basket/add`, {
                product_id: productId.productId
            }).then(response => {
                commit('setProducts', response.data.items);
            });
        },
        incrementProduct({dispatch, commit, state, store}, productId) {
            axios.post(window.location.origin + `/ajax/basket/increment`, {
                product_id: productId.productId
            }).then(response => {
                commit('setProducts', response.data.items);
            });
        },
        decrementProduct({dispatch, commit, state}, productId) {
            axios.post(window.location.origin + `/ajax/basket/decrement`, {
                product_id: productId.productId
            }).then(response => {
                commit('setProducts', response.data.items);
            });
        },
        removeProduct({dispatch, commit, state}, productId) {
            axios.post(window.location.origin + `/ajax/basket/remove`, {
                product_id: productId.productId
            }).then(response => {
                commit('setProducts', response.data.items);
            });
        },
        updateProduct({dispatch, commit, state}, {productId, quantity}) {
            axios.post(window.location.origin + `/basket/update`, {
                product_id: productId,
                quantity: quantity
            }).then(response => {
                commit('setProducts', response.data.items);
            });
        }
    },
    getters: {
        getProducts(state) {
            return state.products;
        },
        getCount(state) {
            let count = 0;
            for (let k in state.products) {
                count += state.products[k].quantity;
            }

            return count
        },
        getDelivery(state) {
            return state.delivery;
        },
        getPay(state) {
            return state.pay;
        },
        getName(state) {
            return state.name;
        },
        getCity(state) {
            return state.city;
        },
        getStreet(state) {
            return state.street;
        },
        getMail(state) {
            return state.mail;
        },
        getHouse(state) {
            return state.house;
        },
        getTotalOldPrice(state) {
            let total = 0;
            state.products.forEach(product => {
                total += product.quantity * product.oldPrice;
            });

            return total
        },
        getTotalPrice(state) {
            let total = 0;
            state.products.forEach(product => {
                total += product.quantity * product.price;
            });

            return total
        },
        getTotalDiscount(state) {
            let total = 0;
            state.products.forEach(product => {
                total += product.quantity * product.discount;
            });

            return total
        },
        getErrors(state) {
            return state.errors
        }
    }
};
