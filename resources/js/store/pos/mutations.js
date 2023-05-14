export default {
    StoreConfigCash(state, payload) {
        state.configCash = payload;
    },
    CloseCash(state, payload) {
        state.Controlo.state = payload;
    },
}
