<template>
  <div class="MenuProdutos">
    <div v-if="FormSingleProduct.state" class="novo_prod w-100 h-100">
        <Produto :product="FormSingleProduct.product" @descartar="OnMounted"/>
    </div>

    <div v-else class="principal">
        <div class="Header">
            <div class="Header-left">
                <span><h2>Lista de artigos</h2></span>
                <button @click="CriarProduto">Novo produto
                    <i v-if="loading=='newP'" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
                </button>
            </div>
            <div class="Header-right">
                <span class="p-input-icon-right w-100">
                    <i class="pi pi-search" />
                    <input type="search" v-model="form.nome"
                    placeholder="Digite nome ou preço do artigo" @keyup="Pesquisar">
                </span>
                <pagination v-if="loading != 'start'" @page="getPage" :object="Products.list"/>
            </div>
        </div>
        <div class="Container">
            <Progress v-if="loading == 'start'"/>
            <label @click="MostrarProd(product)" v-for="product in Products.list.data" :key="product.id" class="formProduct">
                <div class="d-flex">
                    <div class="image"><img :src="'/produtos/image/'+product.image" alt="" class="rounded float-right"></div>
                    <div class="div_preco_qtd">
                        <div class="nameProduct"><strong>{{product.nome}}</strong></div>
                        <div class="d-flex">
                            <div class="preco_qtd">
                                <div><strong>Preço :</strong> {{FormetDineiro.format(product.preçovenda)}} </div>
                                <div><strong>Stock :</strong> {{product.stock_sum_quantity+',00Un(s)'}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="strela"><i class="fa fa-star-o text-warning"></i></div>
                </div>
            </label>
        </div>
    </div>
</div>
<Toast/>
</template>

<script setup>

import { onMounted, ref,onUpdated, reactive } from '@vue/runtime-core';
import axios from 'axios';
import { useForm } from '@inertiajs/inertia'
import Produto from './product.vue';
import pagination from '@/Layouts/paginations/paginate.vue'
import { useStore } from 'vuex';
import Progress from '@/components/confirmation/progress.vue'
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import {Search} from '@/composable/public/search'

const FormetDineiro = new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA',})
const store = useStore()
const FormSingleProduct = reactive({
    state: false,
    product: [],
});
const loading = ref(null)

const toast = useToast()

const Products = reactive({
    list: [],
    search:[]
});

const {getFilter} = Search(Products,loading);

const form = reactive({
    colun1: 'nome',
    colun2: 'preçovenda',
    table: 'produtos',
    nome: null,
    estado: 'active'
})
const getPage = ((data)=>{
    Products.list = data
    Products.search = data.data
    if (form.nome != null) return Pesquisar()
    localStorage.setItem('listStorePaginate',JSON.stringify(data))

})
const MostrarProd = ((event)=>{
    FormSingleProduct.product = event
    FormSingleProduct.state = true
})
const OnMounted = onMounted(() => {
    loading.value = 'start'
    axios.get('/products/100/1')
    .then((Response) => {
        Products.list = Response.data;
        Products.search = Response.data.data;
        loading.value = null
        localStorage.setItem('listStorePaginate',JSON.stringify(Response.data))
        if (form.nome != null || form.nome != "") return Pesquisar()
    }).catch((err) => {
        loading.value = null
        console.log(err);
    }).finally(()=>{
        loading.value = null
        FormSingleProduct.state = false
    });
})

const CriarProduto = (()=>{
    if (loading.value == 'newP') return
    loading.value = 'newP'
    axios.post('/new_product')
    .then((Response) => {
        if (Response.data.message) return message(Response.data.message,Response.data.type)
        FormSingleProduct.product = Response.data.product
        return MostrarProd(FormSingleProduct.product)
    }).catch((err)=>{
        return message('Aconteceu erro no sistema por favor tenta novamente','error');
    }).finally(()=>{
        loading.value = null
    })
})

const message = ((message,type)=>{
    toast.add({
        severity: type,
        summary:'Informação',
        detail: message,
        life:5000
    })
})

const Pesquisar = (async (event)=>{
    if (form.nome === null || form.nome === "") {
        Products.list.data = JSON.parse(localStorage.getItem('listStorePaginate')).data
        Products.search = JSON.parse(localStorage.getItem('listStorePaginate')).data
        return false
    }
    if (search().length == 0) {
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

</script>

<style scoped lang="scss">
@import '../../../assets/produtos/css/menu.scss';
</style>
