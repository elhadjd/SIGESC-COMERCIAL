import { createStore } from "vuex";
import VuexPersistence from "vuex-persist";
import pos from './pos'
import Start from './start'
import publico from './public'
import Product from './product'

const store = createStore({
    modules:{
        pos,
        Start,
        publico,
        Product
    },
    plugins: [
        new VuexPersistence({
            storage: window.localStorage,
        }).plugin,
    ],
});

export default store;
