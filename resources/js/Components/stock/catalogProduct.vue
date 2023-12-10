<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <span class="title">
               <h2>
                  {{$t('words.catalog')}}
               </h2>
            </span>
         </div>
         <div class="Header-right">
            <span class="p-input-icon-right w-100">
            <i class="pi pi-search" />
            <input type="search" @keyup="filterSearch((e)=>e.target.value)" class="search" :placeholder="$t('words.search')">
            </span>
            <div class="filter-product flex space-x-2">
               <div class="filter">
                  <span @click="dropdown.state == true ? dropdown.state = false :  dropdown.state = true" class="dropdown-toggle">{{$t('words.filter')}}</span>
                  <div v-if="dropdown.state" class="drop">
                     <span @click="filterSearch('stock_sum_quantity','>')" class="drop-item">{{$t('words.stockObj.stockUnevailable')}}</span>
                     <span @click="filterSearch('stock_sum_quantity')" class="drop-item">{{$t('words.stockObj.stockAvailable')}}</span>
                     <span @click="filterSearch('quantidade')" class="drop-item">{{$t('words.stockObj.itemDiscount')}}</span>
                  </div>
               </div>
               <button @click="exportToPDF">{{$t('words.print')}}</button>
            </div>
         </div>
      </div>
      <div id="Container" class="Container">
        <div class="list">
            <div class="Title">
                <div class="title-item">{{$t('words.image')}}</div>
                <div class="title-item">{{$t('words.name')}}</div>
                <div class="title-item">
                    {{$t('words.price')}}
                    <span @click="Order('preçovenda')">
                        <font-awesome-icon icon="fa-solid fa-up-long" />
                        <font-awesome-icon icon="fa-solid fa-down-long" />
                    </span>
                </div>
                <div class="title-item">{{$t('words.discount')}}</div>
                <div class="title-item">{{`${$t('words.state')} ${$t('words.of')} stock`}}</div>
                <div class="title-item">
                    stock
                    <span @click="Order('stock_sum_quantity')">
                        <font-awesome-icon icon="fa-solid fa-up-long" />
                        <font-awesome-icon icon="fa-solid fa-down-long" />
                    </span>
                </div>
                <div class="title-item">{{$t('words.state')}}</div>
            </div>
            <div class="list_items">
                <div v-for="product in products.list" :key="product.id" class="rows">
                <div class="content-item">
                    <img class="image-product" :src="'/produtos/image/'+product.image" alt="">
                </div>
                <div class="content-item">{{product.nome}}</div>
                <div class="content-item currency">{{formatMoney(product.preçovenda)}}</div>
                <div class="content-item currency">
                    <i v-if="product.list_price.length <= 0 ">{{$t('words.stockObj.hasDiscount')}}</i>
                </div>
                <div :class="product.stock_sum_quantity > 0 ? 'available' : 'unavailable'" class="content-item currency">{{product.stock_sum_quantity > 0 ? "Stock disponivel" : "Stock não disponivel"}}</div>
                <div :class="product.stock_sum_quantity > 0 ? 'available' : 'unavailable'" class="content-item currency">{{formatMoney(product.stock_sum_quantity)}}</div>
                <div :class="product.estado == 'active' ? 'available' : 'unavailable'" class="content-item currency">{{product.estado == 0 ? "inactive" : 'Não disponivel'}}</div>
                </div>
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
    stock_sum_quantity: null,
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
    }else if(ord == 'stock_sum_quantity'){
        if (orderBy.value[ord] == null || orderBy.value[ord] == 'mine') {
            products.value.list.sort(orderStockMax)
            return orderBy.value[ord] = 'max'
        }
        products.value.list.sort(orderStockMine)
        return orderBy.value[ord] = 'mine'
    }
})
function orderStockMax(a,b){
    return (b.stock_sum_quantity - a.stock_sum_quantity);
}
function orderStockMine(a,b){
    return (a.stock_sum_quantity - b.stock_sum_quantity);
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
    if (event == 'quantidade'|| event == 'stock_sum_quantity') {
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

<style scoped lang="scss">
@import '../../../assets/stock/scss/catalogProduct';
</style>
