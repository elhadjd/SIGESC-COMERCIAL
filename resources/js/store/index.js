import { createStore } from "vuex";
import VuexPersistence from "vuex-persist";
import pos from './pos'
import Start from './start'

const store = createStore({
    modules:{
        pos,
        Start
    },
    plugins: [
        new VuexPersistence({
            storage: window.localStorage,
        }).plugin,
    ],
});

export default store;
