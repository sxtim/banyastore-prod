import $ from "jquery";
require('.././bootstrap');

import EditorJS from "@editorjs/editorjs"
import HeaderEditorJs from "@editorjs/header"
import ImageBlockEditorJs from "./blocks/editorjs/ImageBlock"
import ProductSlideImageComponent from "./blocks/vue/Product/ProductSlideImageComponent"

window.EditorJS = EditorJS
window.HeaderEditorJs = HeaderEditorJs
window.ImageBlockEditorJs = ImageBlockEditorJs

import {createApp} from 'vue';
import UpdateUserOrderComponent from "./blocks/vue/Order/UpdateUserOrderComponent";
import UpdateStatusOrderComponent from "./blocks/vue/Order/UpdateStatusOrderComponent";
import UpdatePriceOrderComponent from "./blocks/vue/Order/UpdatePriceOrderComponent";
import SeoTemplateComponent from "./blocks/vue/Seo/SeoTemplateComponent";

if (document.getElementById('backend-order')) {
    createApp({
        components: {
            UpdateUserOrderComponent,
            UpdateStatusOrderComponent,
            UpdatePriceOrderComponent
        }
    }).mount("#backend-order")
}
if (document.getElementById('seo-template')) {
    createApp({
        components: {
            SeoTemplateComponent
        }
    }).mount("#seo-template")
}

if (document.getElementById('backend-slide-img-product')) {
    createApp({
        components: {
            ProductSlideImageComponent
        }
    }).mount("#backend-slide-img-product")
}
