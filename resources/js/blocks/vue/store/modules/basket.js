import axios from 'axios'

export default {
    namespaced: true,
    state: () => ({
        products: [],
        count: 0
    }),
    mutations: {
        setProducts(state, products) {
            state.products = products;
        },
        setCount(state, count) {
            state.count = count;
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
    }
};
