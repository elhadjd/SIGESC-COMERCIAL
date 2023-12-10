<template>
    <div class="list">
        <div class="title" v-if="modalList">
            <span>Code</span>
            <span>{{$t('words.name')}}</span>
            <span class="text-end">{{$t('words.cost')}}</span>
            <span class="text-end">{{$t('words.price')}}</span>
            <span class="text-end">{{$t('words.stockObj.qtdAvailable')}}</span>
            <strong>{{$t('words.stockObj.qtdSold')}}</strong>
        </div>
        <div v-for="product in dados.products" :key="product.id" :class="!modalList?'Itemse':'item'">
            <span class="Items">{{product.id}}</span>
            <span class="Items">{{product.nome}}</span>
            <span class="text-end Items" v-if="modalList">{{formatMoney(product.preçocust)}}</span>
            <span class="text-end Items" v-if="modalList">{{formatMoney(product.preçovenda)}}</span>
            <span class="text-end Items">{{product.stock_sum_quantity+",00Un(s)"}}</span>
            <strong class="Items">{{product.quantity+",00Un(s)"}}</strong>
        </div>
        <div  v-if="modalList" class="footer">
            <div>
                <span>{{$t('phrases.totalCost')}}</span>
                <strong>{{formatMoney(totals.totalCost)}}</strong>
            </div>
            <div>
                <span>{{$t('phrases.totalSale')}}</span>
                <strong>{{formatMoney(totals.totalSold)}}</strong>
            </div>
            <div>
                <span>{{$t('words.expenses')}}</span>
                <strong>{{formatMoney(totals.totalSpent)}}</strong>
            </div>
            <div>
                <span>{{$t('phrases.totalProfit')}}</span>
                <strong>{{formatMoney(totals.totalProfit - totals.totalSpent)}}</strong>
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
    totals.value.totalSpent = sumSpent(spent)
    totals.value.totalProfit = Number(totals.value.totalSold) - Number(totals.value.totalCost)
})

const sumSpent = ((spent)=>{
    let total = 0;
    spent.forEach((item)=>{
        total += Number(item.amount)
    })
    return total;
})


</script>

<style scoped>
@import '../../../assets/stock/scss/list.css';
</style>
