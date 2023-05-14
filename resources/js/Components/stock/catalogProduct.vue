<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <span class="title">
               <h2>
                  Catalogo dos produtos
               </h2>
            </span>
         </div>
         <div class="Header-right">
            <span class="p-input-icon-right w-100">
            <i class="pi pi-search" />
            <input type="search" @keyup="filterSearch((e)=>e.target.value)" class="search" placeholder="Digite nome ou preço do artigo">
            </span>
            <div class="filter-product">
               <div class="filter">
                  <span @click="dropdown.state == true ? dropdown.state = false :  dropdown.state = true" class="dropdown-toggle">Filtrar</span>
                  <div v-if="dropdown.state" class="drop">
                     <span @click="filterSearch('qtd','>')" class="drop-item">Stock insuficiente</span>
                     <span @click="filterSearch('qtd')" class="drop-item">Stock Disponivel</span>
                     <span @click="filterSearch('quantidade')" class="drop-item">Produtos com desconto</span>
                  </div>
               </div>
               <button @click="exportToPDF">Imprimir</button>
            </div>
         </div>
      </div>
      <div id="Container" class="Container">
         <div class="Title-list">
            <div class="title-item">Imagem</div>
            <div class="title-item">Nome do produto</div>
            <div class="title-item">
               Preço Unitario
               <span @click="Order('preçovenda')">
                  <font-awesome-icon icon="fa-solid fa-up-long" />
                  <font-awesome-icon icon="fa-solid fa-down-long" />
               </span>
            </div>
            <div class="title-item">Preço de disconto</div>
            <div class="title-item">
               stock
               <span @click="Order('qtd')">
                  <font-awesome-icon icon="fa-solid fa-up-long" />
                  <font-awesome-icon icon="fa-solid fa-down-long" />
               </span>
            </div>
            <div class="title-item">Estado</div>
         </div>
         <div class="list_items">
            <div v-for="product in products.list" :key="product.id" class="rows">
               <div class="content-item">
                  <img class="image-product" :src="'/produtos/image/'+product.image" alt="">
               </div>
               <div class="content-item">{{product.nome}}</div>
               <div class="content-item currency">{{currencyFormat.format(product.preçovenda)}}</div>
               <div class="content-item currency">
                  {{
                  product.quantidade > 0 ?
                  ">= "+product.quantidade+" Unidades "+currencyFormat.format(product.preco_de_desconto)
                  : currencyFormat.format(product.preçovenda)+' '
                  }} <i v-if="product.quantidade <= 0 "> Este produto não tem disconto</i>
               </div>
               <div :class="product.qtd > 0 ? 'available' : 'unavailable'" class="content-item currency">{{product.qtd > 0 ? "Stock disponivel" : "Stock não disponivel"}}</div>
               <div :class="product.estado == 0 ? 'available' : 'unavailable'" class="content-item currency">{{product.estado == 0 ? "Disponivel" : 'Não disponivel'}}</div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import html2pdf from "html2pdf.js";
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import moment from 'moment'
const currencyFormat = new Intl.NumberFormat('PT-AO',{style: "currency",currency: "AOA"})
const products = ref({
    list:[],
    store: []
})

const orderBy = ref({
    qtd: null,
    preçovenda: null,
    nome: null,
    type: null,
})

const dropdown = ref({
    state: false,
    filterChoose: null
})

const Order = ((ord)=>{
    orderBy.value.type = ord
    if (ord == 'preçovenda') {
        if (orderBy.value[ord] == null || orderBy.value[ord] == 'mine') {
            products.value.list.sort(orderMax)
            return orderBy.value[ord] = 'max'
        }
        products.value.list.sort(orderMine)
        return orderBy.value[ord] = 'mine'
    }else if(ord == 'qtd'){
        if (orderBy.value[ord] == null || orderBy.value[ord] == 'mine') {
            products.value.list.sort(orderStockMax)
            return orderBy.value[ord] = 'max'
        }
        products.value.list.sort(orderStockMine)
        return orderBy.value[ord] = 'mine'
    }
})
function orderStockMax(a,b){
    return (b.qtd - a.qtd);
}
function orderStockMine(a,b){
    return (a.qtd - b.qtd);
}
function orderMax(a,b){
    return(b.preçovenda - a.preçovenda);
}
const orderMine = (a,b)=>{
    return (a.preçovenda - b.preçovenda);
}

const FormatDate = ((data) =>{
    return moment(data).format('DD-MM-YYYY')
}) 

onMounted(()=>{
    axios.get('getCatalog')
    .then((response) => {
        products.value.list = response.data
        products.value.store = response.data
    }).catch((err) => {
        console.log(err);
    });
})

const filterSearch = ((event, type = null)=>{
    if (event == 'quantidade'|| event == 'qtd') {
        const filter = products.value.store.filter((product)=>{
            if (type) return product[event] <= 0;
            return product[event] > 0;
        })
        products.value.list = filter
    }else{
        const filter = products.value.store.filter((product)=>{
            return String(product.nome).toLowerCase().includes(event.toLowerCase())
        })
        products.value.list = filter  
    }
      
})

const exportToPDF = (()=> {
  const item = document.getElementById("Container");
  var opt = {
    margin: 0,
    filename: new Date(),
    html2canvas: { scale: 2},
  };

  html2pdf().set(opt).from(item).save();
})
</script>

<style scoped>
@import '../../../assets/stock/scss/catalogProduct.css';
</style>
