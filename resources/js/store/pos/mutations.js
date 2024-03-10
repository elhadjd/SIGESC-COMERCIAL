export default {
    StoreConfigCash(state, payload) {
        state.configCash = payload;
    },
    CloseCash(state, payload) {
        state.Controlo.state = payload;
    },
    setOrder(state,order){
        state.order = order
    },
    setPaymentMethods(state,methods){
        state.methods = methods
    }
}
