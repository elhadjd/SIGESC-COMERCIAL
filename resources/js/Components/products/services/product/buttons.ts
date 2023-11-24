import { computed } from "vue"
import { ProductsServices } from "../productsServices"
import { ProductServices } from "./productServices"
import { useStore } from "vuex"

interface ButtonsTs{
    name:string,
    type: string,
    ClickFunction:() => void,
    className: string,
    condition: boolean
}
export const ButtonsServices = (emits)=>{
    const {createProduct,showProduct} = ProductsServices()
    const store = useStore()
    const product = computed(()=> store.getters['Product/product'])

    const {
        submit,
    } = ProductServices(emits)
    const buttonsData:ButtonsTs[] = [
        {
            name: 'Novo produto',
            className: '',
            ClickFunction: (async()=> await createProduct()),
            type: 'button',
            condition: false
        },
        {
            name: 'Guardar',
            className: '',
            ClickFunction: async()=> await submit(),
            type: 'submit',
            condition: product.value.data.estado == 'active' ? true: false
        },
        {
            name: 'Fechar',
            className: 'mx-1 botao_descartar',
            ClickFunction: ()=>closeForm(),
            type: 'button',
            condition: true
        }
    ]
    const closeForm = (async()=>{
        showProduct(false)
        emits('closeForm')
    })
    return {buttonsData}
}
