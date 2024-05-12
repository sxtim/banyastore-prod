import axios from 'axios'
import {commit} from "lodash/seq";

export default {
    namespaced: true,
    state: () => ({
        products: []
    }),
    mutations: {
        setProducts(state, products) {
            state.products = products;
        },
    },
    actions: {
        initFavorite({commit}){
            axios.post(window.location.origin + `/ajax/favorite/get-data`).then(response => {
                commit('setProducts', response.data.products);
            });
        },
        favoriteProduct({dispatch,commit}, productId) {
            axios.post(window.location.origin + `/ajax/favorite/product`, {
                product_id: productId.productId
            }).then(response => {console.log(response.data)
                if (response.data.status === 'success') {
                    dispatch('initFavorite');
                }

                if (response.data.status === 'no auth') {
                 //   location.href = '/'
                }

            });
        }
    },
    getters: {
        getProducts(state) {
            return state.products;
        },
        getCount(state) {
            let count = 0;
            state.products.forEach(product => {
                count++;
            });

            return count
        }
    }
};
