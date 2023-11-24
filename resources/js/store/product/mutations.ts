import { Product } from "@/types/product";

export default {
    changeProduct(state: { data: Product; }, product:Product) {
        state.data = product;
    },

    changeStateForm(state: { StateFormShow: boolean; },stateForm: boolean){
        state.StateFormShow = stateForm;
    },

    ChangeDisableButton(state: { stateDisableButtons: any; },payload: any){
        state.stateDisableButtons = payload
    },

    loadMovementsProduct(state: {movementsProduct: {data: string[]}},movements: string[]){        
        state.movementsProduct.data = movements
    }
}
