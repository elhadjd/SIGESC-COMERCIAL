import { computed, ref } from "vue"
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
    const loading = ref<number>(10)
    const {t,locale} = useI18n()
    console.log(locale.value);

    const {
        submit,
    } = ProductServices(emits)
    const buttonsData:ButtonsTs[] = [
        {
            name: t('words.new') + ' ' + t('words.article'),
            className: '',
            ClickFunction: (async()=> {
                loading.value = 1
                await createProduct()
                loading.value = 10
            }),
            type: 'button',
            condition: false
        },
        {
            name: t('words.save'),
            className: '',
            ClickFunction: async()=>{
                loading.value = 1
                await submit()
                loading.value = 10
            } ,
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
    return {buttonsData,loading}
}
