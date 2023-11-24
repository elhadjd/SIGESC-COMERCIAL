<template>
  <div class="MenuProdutos">

    <div v-if="product.StateFormShow" class="novo_prod w-100 h-100">
        <Produto @closeForm="OnMounted" :key="productComponentKey"/>
    </div>

    <div v-else class="principal">
        <div class="Header">
            <div class="Header-left">
                <span><h2>Lista de artigos</h2></span>
                <button @click="createProduct">Novo produto
                    <i v-if="loading=='newP'" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
                </button>
            </div>
            <div class="Header-right">
                <span class="p-input-icon-right w-100">
                    <i class="pi pi-search" />
                    <input type="search" v-model="form.nome"
                    placeholder="Digite nome ou preço do artigo" @keyup="SearchProduct">
                </span>
                <pagination v-if="loading != 'start'" @page="getPage" :object="Products.list"/>
            </div>
        </div>
        <div class="Container">
            <Progress v-if="loading == 'start'"/>
            <label @click="showProduct(true,product)" v-for="product in Products.list.data" :key="product.id" class="formProduct">
                <div class="d-flex">
                    <div class="image"><img :src="'/produtos/image/'+product.image" alt="" class="rounded float-right"></div>
                    <div class="div_preco_qtd">
                        <div class="nameProduct"><strong>{{product.nome}}</strong></div>
                        <div class="d-flex">
                            <div class="preco_qtd">
                                <div><strong>Preço :</strong> {{FormetDineiro.format(product.preçovenda)}} </div>
                                <div><strong>Stock :</strong> {{product.stock_sum_quantity != null ?product.stock_sum_quantity  +',00Un(s)':0 +',00Un(s)'}}</div>
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

import { onMounted, ref,onUpdated, reactive, computed } from '@vue/runtime-core';
import axios from 'axios';
import { useForm } from '@inertiajs/inertia'
import Produto from './product/product.vue';
import pagination from '@/Layouts/paginations/paginate.vue'
import { useStore } from 'vuex';
import Progress from '@/components/confirmation/progress.vue'
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

import {ProductsServices} from './services/productsServices';
const FormetDineiro = new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA',})
const store = useStore()
const {
    SearchProduct,
    Products,
    search,
    loading,
    getPage,
    form,
    showMessage,
    createProduct,
    productComponentKey,
    showProduct,
} = ProductsServices()
const product = computed(()=> store.getters['Product/product'])
const toast = useToast()
const OnMounted = onMounted(async() => {
    loading.value = 'start'
    await axios.get('/products/100/1')
    .then((Response) => {
        Products.list = Response.data;
        Products.search = Response.data.data;
        localStorage.setItem('listStorePaginate',JSON.stringify(Response.data))
        if (form.nome != null || form.nome != "") return SearchProduct()
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        loading.value = null
        showProduct(false)
    });
})
</script>

<style scoped lang="scss">
@import '../../../assets/produtos/css/menu.scss';
</style>
