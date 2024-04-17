import axios from 'axios'

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
        pay: 1
    }),
    mutations: {
        setProducts(state, products) {
            state.products = products;
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
    },
    actions: {
        initBasket({commit}){
            axios.post(window.location.origin + `/ajax/basket/get-basket`).then(response => {
                commit('setProducts', response.data.items);
            });
        },
        initCount({commit}){
            axios.post(window.location.origin + `/ajax/basket/get-count`).then(response => {
                commit('setCount', response.data.count);
            });
        },
        addProduct({commit}, productId) {
            axios.post(window.location.origin + `/ajax/basket/add`, {
                product_id: productId.productId
            }).then(response => {
                commit('setCount', response.data.count);
            });
        },
        incrementProduct({dispatch, commit, state, store}, productId) {
            axios.post(window.location.origin + `/ajax/basket/increment`, {
                product_id: productId.productId
            }).then(response => {
                commit('setProducts', response.data.items);
                commit('setCount', response.data.count);
            });
        },
        decrementProduct({dispatch, commit, state}, productId) {
            axios.post(window.location.origin + `/ajax/basket/decrement`, {
                product_id: productId.productId
            }).then(response => {
                commit('setProducts', response.data.items);
                commit('setCount', response.data.count);
            });
        },
        removeProduct({dispatch, commit, state}, productId) {
            axios.post(window.location.origin + `/ajax/basket/remove`, {
                product_id: productId.productId
            }).then(response => {
                commit('setProducts', response.data.items);
                commit('setCount', response.data.count);
            });
        },
        updateProduct({dispatch, commit, state}, {productId, quantity}) {
            axios.post(window.location.origin + `/basket/update`, {
                product_id: productId,
                quantity: quantity
            }).then(response => {
                commit('setProducts', response.data.items);
                commit('setCount', response.data.count);
            });
        }
    },
    getters: {
        getProducts(state) {
            return state.products;
        },
        getCount(state) {
            return state.count;
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
    }
};
