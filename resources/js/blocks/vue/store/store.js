import basket from "./modules/basket";
import favorite from "./modules/favorite";

import {createStore} from "vuex";

export default createStore({
    modules: {
        basket,
        favorite
    }
})
