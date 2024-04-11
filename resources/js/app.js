import $ from 'jquery';
require('./bootstrap');
import  './modules/sliders';
import  './modules/product-detail-tabs';
import store from "./blocks/vue/store/store";

import {createApp} from 'vue';
import BasketPageComponent from "./blocks/vue/BasketPage/BasketPageComponent";


if (document.getElementById('basket-page')) {
    createApp({
        components: {
            BasketPageComponent,
        }
    }).use(store)
        .mount("#basket-page")
}
