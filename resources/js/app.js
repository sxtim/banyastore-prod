import $ from 'jquery';
require('./bootstrap');
import mobileNav from './modules/mobile-nav';
mobileNav();
import  './modules/sliders';
import  './modules/product-detail-tabs';
import store from "./blocks/vue/store/store";

//
import {createApp} from 'vue';
import BasketIconComponent from "./blocks/vue/Header/BasketIconComponent";
import FavoriteIconComponent from "./blocks/vue/Header/FavoriteIconComponent";
import BasketPageComponent from "./blocks/vue/BasketPage/BasketPageComponent";
import BtnAddBasketComponent from "./blocks/vue/Button/BtnAddBasketComponent";
import BtnDetailAddBasketComponent from "./blocks/vue/Button/BtnDetailAddBasketComponent";
import BtnFavoriteProductComponent from "./blocks/vue/Button/BtnFavoriteProductComponent";
import BtnDetailFavoriteProductComponent from "./blocks/vue/Button/BtnDetailFavoriteProductComponent";
import LoginComponent from "./blocks/vue/Auth/LoginComponent";
import RegisterComponent from "./blocks/vue/Auth/RegisterComponent";

//
if (document.getElementById('header')) {
    createApp({
        components: {
            BasketIconComponent,
            FavoriteIconComponent
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
            BtnDetailAddBasketComponent,
            BtnFavoriteProductComponent,
            BtnDetailFavoriteProductComponent
        }
    }).use(store)
        .mount("#catalog")
}

if (document.getElementById('login-form')) {
    createApp({
        components: {
            LoginComponent
        }
    }).use(store)
        .mount("#login-form")
}

if (document.getElementById('register-form')) {
    createApp({
        components: {
            RegisterComponent
        }
    }).use(store)
        .mount("#register-form")
}

