import $ from 'jquery';
require('./bootstrap');
import  './modules/sliders';
import  './modules/product-detail-tabs';
import store from "./blocks/vue/store/store";

import {createApp} from 'vue';
import BasketIconComponent from "./blocks/vue/Header/BasketIconComponent";
import BasketPageComponent from "./blocks/vue/BasketPage/BasketPageComponent";
import BtnAddBasketComponent from "./blocks/vue/Button/BtnAddBasketComponent";

if (document.getElementById('header')) {
    createApp({
        components: {
            BasketIconComponent,
        }
    }).use(store)
        .mount("#header")
}

if (document.getElementById('basket-page')) {
    createApp({
        components: {
            BasketPageComponent,
        }
    }).use(store)
        .mount("#basket-page")
}

if (document.getElementById('catalog')) {
    createApp({
        components: {
            BtnAddBasketComponent,
        }
    }).use(store)
        .mount("#catalog")
}
