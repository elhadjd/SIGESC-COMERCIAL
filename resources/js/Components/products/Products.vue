<template>
    <Progress v-if="$store.state.StateProgress"/>
  <div class="MenuProdutos">
    <div v-if="FormSingleProduct.state" class="novo_prod w-100 h-100">
        <Produto :product="FormSingleProduct.product" @descartar="OnMounted"/>
    </div>

    <div v-else class="principal">
        <div class="Header">
            <div class="Header-left">
                <span><h2>Lista de artigos</h2></span>
                <button @click="CriarProduto">Novo produto</button>
            </div>
            <div class="Header-right">
                <span class="p-input-icon-right w-100">
                    <i class="pi pi-search" />
                    <input type="search" v-model="form.nome"
                    placeholder="Digite nome ou preço do artigo" @keyup="Pesquisar">
                </span>
            </div>
        </div>
        <div class="Container">
            <label @click="MostrarProd(produto)" v-for="produto in Products.Products" :key="produto.id" class="formProduct">
                <div class="d-flex">
                    <div class="image"><img :src="'/produtos/image/'+produto.image" alt="" class="rounded float-right"></div>
                    <div class="div_preco_qtd">
                        <div class="nameProduct"><strong>{{produto.nome}}</strong></div>
                        <div class="d-flex">
                            <div class="preco_qtd">
                                <div><strong>Preço :</strong> {{FormetDineiro.format(produto.preçovenda)}} </div>
                                <div><strong>Stock :</strong> {{sumQuantity(produto)+',00Un(s)'}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="strela"><i class="fa fa-star-o text-warning"></i></div>
                </div>
            </label>
        </div>
    </div>
</div>
</template>

<script setup>

import { onMounted, ref,onUpdated, reactive } from '@vue/runtime-core';
import axios from 'axios';
import { useForm } from '@inertiajs/inertia'
import Produto from './product.vue';
import { useStore } from 'vuex';
import Progress from '@/components/confirmation/progress.vue'
const FormetDineiro = new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA',})
const store = useStore()
const FormSingleProduct = reactive({
    state: false,
    product: [],
});

const Products = reactive({
    Products: [],
    StoreProducts: []
})
const form = reactive({
    colun1: 'nome',
    colun2: 'preçovenda',
    table: 'produtos',
    nome: null,
    estado: 'active'
})

const MostrarProd = ((event)=>{
    FormSingleProduct.product = event
    FormSingleProduct.state = true
})
const OnMounted = onMounted(() => {
    store.state.StateProgress = true
    axios.get('/products')
    .then((Response) => {
        Products.Products = Response.data;
        Products.StoreProducts = Response.data;
        store.state.StateProgress = false
        FormSingleProduct.state = false
        if (form.nome != null) return Pesquisar()
    }).catch((err) => {
        store.state.StateProgress = false
        FormSingleProduct.state = false
        console.log(err);
    });
})

const CriarProduto = (()=>{
    store.state.StateProgress = true
    axios.post('/new_product')
    .then((Response) => {
        store.state.StateProgress = false
        FormSingleProduct.product = Response.data.product
        return MostrarProd(FormSingleProduct.product)
    })
})

const Pesquisar = ((event)=>{
    // a filtra a veriavel resultado
    const filtrar = Products.StoreProducts.filter(object=>{
        if (Number(form.nome)) {
            return String(object.preçovenda).toLowerCase().includes(form.nome.toLowerCase())
        } else {
            return String(object.nome).toLowerCase().includes(form.nome.toLowerCase())
        }
    })
    Products.Products  = filtrar;
})

const sumQuantity = ((product)=>{
    let quantity = 0;
    product.stock.forEach(item => {
        quantity += item.quantity
    });
    return quantity;
})

</script>

<style scoped lang="scss">
@import '../../../assets/produtos/css/menu.scss';
</style>
