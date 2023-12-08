import { computed } from "vue"
import { ProductsServices } from "../productsServices"
import { ProductServices } from "./productServices"
import { useStore } from "vuex"
import { useI18n } from "vue-i18n"

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
    const {t} = useI18n()
    const {
        submit,
    } = ProductServices(emits)
    const buttonsData:ButtonsTs[] = [
        {
            name: t('words.new') + ' ' + t('words.article'),
            className: '',
            ClickFunction: (async()=> await createProduct()),
            type: 'button',
            condition: false
        },
        {
            name: t('words.save'),
            className: '',
            ClickFunction: async()=> await submit(),
            type: 'submit',
            condition: product.value.data.estado == 'active' ? true: false
        },
        {
            name: t('words.close'),
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
