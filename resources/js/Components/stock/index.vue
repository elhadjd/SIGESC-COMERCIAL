<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <span>
               <h2>Stock</h2>
            </span>
         </div>
         <div class="Header-right">
            <span>
            </span>
         </div>
      </div>
      <div class="Container">
         <div class="Header-Cards">

            <div class="carde">
               <p>{{dados.totalRequest}}</p>
               <i class="fa fa-shopping-bag shop" aria-hidden="true"></i>
               <span>Total de quantidade solicitadas</span>
            </div>
            <div class="carde">
               <p>{{dados.totalReject}}</p>
               <i class="fa fa-ban text-danger shop" aria-hidden="true"></i>
               <span>Total de quantidade devolvidos</span>
            </div>
            <div class="carde">
               <p>{{dados.totalConcluded}}</p>
               <i class="fa fa-check concluida" aria-hidden="true"></i>
               <span>Total de quantidade comprados</span>
            </div>
         </div>
         <div class="Container-cards">
            <div class="Container-left">
               <div class="cards">
                  <div class="card">
                     <div class="TotalVenda">
                        <span></span>
                        <div>
                           <h2>Produto mais vendido</h2>
                           <div class="product">
                              <font-awesome-icon icon="fa-solid fa-star" />
                              <strong>{{dados.bestSeller?.quantity}}</strong>
                              <img :src="'/produtos/image/'+dados.bestSeller?.image" alt="">
                              <span>{{dados.bestSeller?.nome}}</span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="card">
                     <div class="TotalGasto">
                        <span></span>
                        <div>
                           <h2>Produto mais vendido</h2>
                           <div class="product">
                              <strong>{{dados.lessSold?.quantity}}</strong>
                              <img :src="'/produtos/image/'+dados.lessSold?.image" alt="">
                              <span>{{dados.lessSold?.nome}}</span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="card">
                     <div class="TotalCusto">
                        <span></span>
                        <div>
                           <h2>Produto com mais lucro</h2>
                           <div class="product">
                              <font-awesome-icon icon="fa-solid fa-chart-line" />
                              <strong>{{dados.productWithMoreProfit?.gain+"%"}}</strong>
                              <img :src="'/produtos/image/'+dados.productWithMoreProfit?.image" alt="">
                              <span>{{dados.productWithMoreProfit?.nome}}</span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="card">
                     <div class="TotalLucro">
                        <span></span>
                        <div>
                           <h2>Produto com menos lucro</h2>
                           <div class="product">
                              <font-awesome-icon icon="fa-solid fa-chart-gantt" />
                              <strong>{{dados.productWithLessProfit?.gain+"%"}}</strong>
                              <img :src="'/produtos/image/'+dados.productWithLessProfit?.image" alt="">
                              <span>{{dados.productWithLessProfit?.nome}}</span>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="testar" :class="modalList ? 'modalList':''">
                  <div class="ListOrders">
                     <div class="InputSearch">
                        <span v-if="!modalList" class="p-input-icon-right w-100">
                        <i class="pi pi-search" />
                        <input type="text" @keyup="FilterOrder" placeholder="Pesquisar...">
                        </span>
                        <div v-if="modalList">
                           <button @click="filterProduct('buy')">Produtos Vendidos</button>
                           <font-awesome-icon @click="filterProduct('all')" icon="fa-solid fa-rotate-right" />
                        </div>
                        <font-awesome-icon v-if="modalList" @click="showList(false)" icon="fa-solid fa-arrows-split-up-and-left" />
                        <font-awesome-icon v-else @click="showList(true)" icon="fa-solid fa-arrow-up-right-from-square" />
                     </div>
                     <list-out-product id="PrintList"/>
                     <div v-if="modalList" class="buttons">
                        <button @click="exportToPDF">Imprimir</button>
                     </div>
                  </div>
               </div>
            </div>
            <div class="Container-right">
               <calendar @progres="progres" @RelatorByMonth="RelatorByMonth"/>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import html2pdf from "html2pdf.js";
import moment from 'moment'
import listOutProduct from '@/Components/stock/listOutProduct.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import calendar from './calendar.vue'
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import { useStore } from 'vuex';
import useEventsBus from '@/Eventbus';
const currencyFormat = new Intl.NumberFormat('PT-AO',{style:'currency',currency:"AOA"})
const modalList = ref(false)
const store = useStore()
const {emit} = useEventsBus()
const emits = defineEmits(['message']);
const dados = ref({
    bestSeller: [],
    lessSold: [],
    productWithMoreProfit: [],
    productWithLessProfit: [],
    Boughts: [],
    sold: [],
    products: [],
    storeProducts: [],
    totalRequest: 0,
    totalReject: 0,
    totalConcluded: 0,
    spent: 0
})

const datas = ref({
    inicio: null,
    fin: null,
    month: null
})
const dropdown = ref({
    state: false,
    filterChoose: null
})

const progres = (()=>{
    store.state.pos.StateProgress = !store.state.pos.StateProgress
})

const showList = ((value)=>{
    modalList.value = value
    emit('modalGrand',value)
})

const RelatorByMonth = (async (data,inicio ,fin,month)=>{
    datas.value.inicio = inicio
    datas.value.fin = fin
    datas.value.month = month
    dados.value.Boughts = filterList(data.list,'Entrada','Compra')
    dados.value.sold = filterList(data.list,'Vendido por PDV','Vendido por Faturação')
    dados.value.spent = data.spent.operations
    dados.value.storeProducts = data.products
    filterProducts(data.products,dados.value.sold)
})

const filterList = ((data,type,type1)=>{
    const filter = data.filter((data)=>{
        return String(data.movement_type.name).toLowerCase() == type.toLowerCase()
        || String(data.movement_type.name).toLowerCase() == type1.toLowerCase();
    })
    return filter
})

const filterQuantity = ((list)=>{
    const filter = list.filter((list)=>{
        return list.motive === 'Annulado';
    })
    filter.forEach((item)=>{
        dados.value.totalReject += item.quantity
    })
    dados.value.totalConcluded = dados.value.totalRequest - dados.value.totalReject
})

const filterProducts = ((products,solds)=>{
    products.forEach(product => {
        product.quantity = 0;
        product.gain = 0;
        product.inventory = []
        const filter = solds.filter((sold)=>{
            return sold.produtos_id === product.id
        })
        product.inventory = filter
        let sumCost = 0
        let sumSold = 0
        product.inventory.forEach(inventory => {
            product.quantity += Number(inventory.quantity)
            sumCost += Number(inventory.price_cost) * Number(inventory.quantity)
            sumSold += Number(inventory.price_sold) * Number(inventory.quantity)
        });
        product.patrimonio = sumSold
        product.total_cust = sumCost
        var gain = Number(product.preçovenda - product.preçocust)
        var percent = gain * 100 / product.preçocust
        if (Number(percent)) {
            product.gain = Math.floor(percent)
        }
        dados.value.totalRequest += product.quantity
    });
    var maxGain = Math.max(...products.map(product=>product.gain));
    dados.value.productWithMoreProfit = products.find((product)=>{
        return product.gain === maxGain;
    })
    var minGain = Math.min(...products.map(product=>product.gain));
    dados.value.productWithLessProfit = products.find((product)=>{
        return product.gain === minGain;
    })
    var maxQuantity = Math.max(...products.map(product=>product.quantity));
    dados.value.bestSeller = products.find((product)=>{
        return product.quantity === maxQuantity;
    })
    var minQuantity = Math.min(...products.map(product=>product.quantity));
    dados.value.lessSold = products.find((product)=>{

        return product.quantity === minQuantity;
    })
    filterQuantity(solds)
    products.sort(ordenar)
    dados.value.products = products
    emit('productOutList',dados.value.products,dados.value.spent)
})

const FilterOrder = ((event)=>{
    const filter = dados.value.storeProducts.filter((product)=>{
        return String(product.nome).toLowerCase().includes(event.target.value.toLowerCase());
    })
    return filterProducts(filter,dados.value.sold)
})

function ordenar(a,b){
    return(b.quantity - a.quantity);
}

const filterProduct = ((event)=>{
    if (event == 'buy') {
        const filter = dados.value.products.filter((product)=>{
            return product.quantity > 0
        })
        return emit('buyProduct',filter,dados.value.spent)
    }
    return emit('buyProduct',dados.value.products,dados.value.spent)
})
const exportToPDF = (()=> {
  const item = document.getElementById("PrintList");
  var opt = {
    margin: 0,
    filename: !datas.value.inicio ? new Date().getMonth() : datas.value.inicio+
    ' Ate '+datas.value.fin+" de "+datas.value.month,
    html2canvas: { scale: 2},
  };
  html2pdf().set(opt).from(item).save();
})
</script>

<style scoped lang="scss">
.testar{
    width: 100%;
    height: 100%;
    overflow-y: auto
}
@import '../../../assets/stock/scss/menuStock';
</style>
