export default {
    StartSaveCompany(state, payload) {
        state.start.company = payload
    },
    StartSaveUser(state, payload) {
        state.start.user = payload
    },
    nextStep(state,payload) {
        state.start.step = payload
    },
    BackStep(state,payload) {
        state.start.step = payload
    },

    StartSaveLicense(state,payload){
        state.start.license = payload
    },

    StartSaveTotals(state,payload){
        state.start.totals = payload
    }
}
