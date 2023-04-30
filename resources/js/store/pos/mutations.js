export default {
    ArmazenarDados(state, payload) {
        state.Empresa = payload.data;
        state.user = payload.user;
    },

    StoreConfigCash(state, payload) {
        state.configCash = payload;
    },
    CloseCash(state, payload) {
        state.Controlo.state = payload;
    },
}
