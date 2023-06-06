export default {
    saveCompany(state, payload) {
        state.user = payload.user
        state.company = payload.data
    },
}
