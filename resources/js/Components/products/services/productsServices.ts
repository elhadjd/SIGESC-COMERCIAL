import { Product } from "@/types/product";
import { computed, reactive, ref } from "vue";
import {serviceMessage} from '../../../composable/public/messages'
import {Search} from '@/composable/public/search'
import axios from "axios";
import { useStore } from "vuex";
import { ProductServices } from "./product/productServices";
export const ProductsServices = (()=>{
    const {loadProduct} = ProductServices()
    const store = useStore()
    const {showMessage} = serviceMessage()
    const loading = ref(null)
    const productStore = computed(()=>store.getters['Product/product'])
    const Products:{list:{data:Product[]},search:Product[]} = reactive({
        list: {
            data: []
        },
        search:[]
    });
    const {getFilter} = Search(Products,loading);
    const form:{colun1:string,colun2:string,table:string,nome:string,estado:string} = reactive({
        colun1: 'nome',
        colun2: 'preçovenda',
        table: 'produtos',
        nome: null,
        estado: 'active'
    })

    const makeDiscount = ((price:any,discount:number)=>{
        const priceReturn = price/100*discount
        return price - priceReturn
    })

    const SearchProduct = (async ()=>{
        if (form.nome === null || form.nome === "") {
            Products.list.data = JSON.parse(localStorage.getItem('listStorePaginate')).data
            Products.search = JSON.parse(localStorage.getItem('listStorePaginate')).data
            return false
        }
        if (search().length < 4) {
            await getFilter(`/search/produtos/nome/${form.nome}`)
            Products.list.data = search()
        } else {
            Products.list.data = search()
        }
    })

    const search = () => {
        return Products.search.filter(object=>{
            if (Number(form.nome)) {
                return String(object.preçovenda).toLowerCase().includes(form.nome.toLowerCase())
            } else {
                return String(object.nome).toLowerCase().includes(form.nome.toLowerCase())
            }
        })
    }

    const getPage = ((data: any)=>{
        Products.list = data
        Products.search = data.data
        if (form.nome != null) return SearchProduct()
        localStorage.setItem('listStorePaginate',JSON.stringify(data))
    })

    const showProduct = ((state:boolean,product?: Product)=>{
        productStore.value.productComponentKey += 1
        if (product) loadProduct(product)
        store.commit('Product/changeStateForm',state)
    })

    const createProduct = (async()=>{
        if (loading.value == 'newP') return
        loading.value = 'newP'
        showProduct(false)
        await axios.post('/new_product')
        .then((Response) => {
            if (Response.data.message) return showMessage(Response.data.message,Response.data.type)
            return showProduct(true,Response.data.product)
        }).catch((err)=>{
            return showMessage('Aconteceu erro no sistema por favor tenta novamente','error');
        }).finally(()=>{
            loading.value = null
        })
    })

    return {
        SearchProduct,
        search,
        Products,
        loading,
        getPage,
        form,
        showMessage,
        createProduct,
        showProduct,
        makeDiscount
    }
})
