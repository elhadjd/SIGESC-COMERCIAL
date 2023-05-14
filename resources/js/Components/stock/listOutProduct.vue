<template>
    <div class="list">
        <div class="title" v-if="modalList">
            <span>Code</span>
            <span>Nome</span>
            <span class="text-end">Preço de Custo</span>
            <span class="text-end">Preço de Venda</span>
            <span class="text-end">Quantidade disponivel</span>
            <strong>Quantidade Vendido</strong>
        </div>
        <div v-for="product in dados.products" :key="product.id" :class="!modalList?'Itemse':'item'">
            <span class="Items">{{product.id}}</span>
            <span class="Items">{{product.nome}}</span>
            <span class="text-end Items" v-if="modalList">{{currencyFormat.format(product.preçocust)}}</span>
            <span class="text-end Items" v-if="modalList">{{currencyFormat.format(product.preçovenda)}}</span>
            <span class="text-end Items">{{product.stock_sum_quantity+",00Un(s)"}}</span>
            <strong class="Items">{{product.quantity+",00Un(s)"}}</strong>
        </div>
        <div  v-if="modalList" class="footer">
            <div>
                <span>Total custo</span>
                <strong>{{currencyFormat.format(totals.totalCost)}}</strong>
            </div>
            <div>
                <span>Total de Venda</span>
                <strong>{{currencyFormat.format(totals.totalSold)}}</strong>
            </div>
            <div>
                <span>Total de Gastos</span>
                <strong>{{currencyFormat.format(totals.totalSpent)}}</strong>
            </div>
            <div>
                <span>Total de lucro</span>
                <strong>{{currencyFormat.format(totals.totalProfit - totals.totalSpent)}}</strong>
            </div>
        </div>
    </div>
</template>

<script setup>
const currencyFormat = new Intl.NumberFormat('PT-AO',{style:'currency',currency:"AOA"})
const modalList = ref(false)
import useEventsBus from '@/Eventbus';
import { onMounted, ref, watch } from '@vue/runtime-core';

const {bus} = useEventsBus()
const dados = ref({
    products: [],
    storeProducts: []
})

const totals = ref({
    totalCost: 0,
    totalSold: 0,
    totalSpent: 0,
    totalProfit: 0,
})


watch(()=>bus.value.get('productOutList'),(payload)=>{
    dados.value.products = payload[0]
    sumSold(dados.value.products,payload[1])
})

watch(()=>bus.value.get('modalGrand'),(value)=>{
    modalList.value = value[0]
})
watch(()=>bus.value.get('buyProduct'),(products)=>{
    dados.value.products = products[0]
})


const sumSold = ((event,spent)=>{
    totals.value.totalCost = 0
    totals.value.totalSold = 0
    totals.value.totalSpent = 0
    totals.value.totalProfit = 0
    event.forEach(product => {
        totals.value.totalCost += Number(product.total_cust)
        totals.value.totalSold += Number(product.patrimonio)
    });
    totals.value.totalSpent = spent
    totals.value.totalProfit = Number(totals.value.totalSold) - Number(totals.value.totalCost)
})


</script>

<style scoped>
@import '../../../assets/stock/scss/list.css';
</style>
