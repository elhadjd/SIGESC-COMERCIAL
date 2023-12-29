<template>
    <div class="principal">
        <div class="Header">
            <div class="Header-left">
                <div>
                    <h2>{{$t('words.purchase')}}s</h2>
                </div>
                <div>
                <button @click="NewPurchase">{{$t('words.create')}}
                    <i v-if="loading=='new'" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
                </button>
                </div>
            </div>
            <div class="Header-right">
                <span class="p-input-icon-right w-100">
                    <i class="pi pi-search" />
                    <input type="text" @keyup="SearchInvoice" :placeholder="$t('words.search')">
                </span>
                <pagination v-if="loading != 'start'" @page="getPage" :object="Orders.list"/>
            </div>
        </div>
        <div class="Container">
            <div class="list">
                <div v-if="loading == 'start'" class="progress"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i></div>
                <div class="Title">
                    <div>{{$t('words.order')}}</div>
                    <div>{{$t('words.provider')}}</div>
                    <div>{{$t('words.user')}}</div>
                    <div class=" truncate">{{$t('phrases.orderDate')}}</div>
                    <div class=" truncate">{{$t('phrases.dueDate')}}</div>
                    <div class="text-right truncate">{{$t('words.subTotal')}}</div>
                    <div class="text-right">{{$t('words.total')}}</div>
                    <div>{{$t('words.missing')}}</div>
                    <div>{{$t('words.state')}}</div>
                </div>
                <div class="list_items">
                    <div @click="showOrder(order)" class="rows" v-for="order in Orders?.list.data"
                        :key="order.id">
                        <div>{{'Compra/00'+order.id}}</div>
                        <div>{{order.supplier?.name}}</div>
                        <div>{{order.user?.surname}}</div>
                        <div>{{formatDate(order.DateOrder)}}</div>
                        <div>{{formatDate(order.DateDue)}}</div>
                        <div class="text-right">{{formatMoney(Number(order.total) + Number(order.totalDiscount) - Number(order.totalTax) - Number(order.TotalSpending))}}</div>
                        <div class="text-right">{{formatMoney(order.total)}}</div>
                        <div class="Pagar">
                            <span>{{formatMoney(order.restPayable)}}</span>
                        </div>
                        <div :class="order.state">
                            <span class="">{{order.state}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="RodaPe">
                <div class="Totals">
                    <div>
                        <label for="">{{`${$t('words.total')} ${$t('words.of')} ${$t('words.order')}s `}}</label>
                        <span>{{formatMoney(Totals.totalOrders)}}</span>
                    </div>
                    <div>
                        <label for="">{{$t('words.missing')}}</label>
                        <span>{{formatMoney(Totals.restPayable)}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import progress from '@/Components/confirmation/progress.vue'
import pagination from '@/Layouts/paginations/paginate.vue'
import { onMounted, ref,watch } from "@vue/runtime-core";
import axios from "axios";
import useEventsBus from '@/Eventbus';
import {Search} from '@/composable/public/search'
const {emit,bus} = useEventsBus();

const emits = defineEmits(['modulos','message'])
const Orders = ref({
    list: [],
    store: [],
    search:[]
})

const Totals = ref({
    totalOrders:0,
    restPayable:0
})

const {getFilter} = Search(Orders.value)

const loading = ref(null)
const NewPurchase = (()=>{
    if (loading.value == 'new') return
    loading.value = 'new'
    axios.post('NewPurchase')
    .then((Response) => {
        emits('modulos','compra', Response.data)
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        loading.value = null
    });
})

onMounted(async() => {
    await getOrder()
});

const getPage = ((data)=>{
    Orders.value.list = data
    Orders.value.store = data.data
})

async function getOrder() {
    loading.value = 'start'
    await axios.get("getPurchases").then((response) => {
        Orders.value.list = response.data
        Orders.value.store = response.data.data
        sumOrders(Orders.value.list.data)
    }).catch((erro) => {
      console.log(erro);
    }).finally(()=>{
        loading.value = null
    });
}

const message = ((message,tipo)=>{
    emits('message',message,tipo)
})

const SearchInvoice = ((e)=>{
    const FilterSearch = Orders.value.store.filter(object=>{
        return String(object.supplier?.name).toLowerCase().includes(e.target.value.toLowerCase())
        || String(object.total).includes(e.target.value)
        || String(object.state).toLowerCase().includes(e.target.value.toLowerCase())
        || String(object.user.name).toLowerCase().includes(e.target.value.toLowerCase())
    })
    if (FilterSearch.length<=0) {
        emits('message','Nemhuma fatura encontrada','info')
    }else{
        Orders.value.list.data = FilterSearch
        sumOrders(Orders.value.list.data)
    }
})

const sumOrders = ((Orders)=>{
    Totals.value.restPayable = 0,
    Totals.value.totalOrders = 0
    Orders?.forEach(order => {
        Totals.value.restPayable += Number(order.restPayable)
        Totals.value.totalOrders += Number(order.total)
    });
})

const showOrder = (Compra) => {
    emit('CompraPedidoFormulario',Compra)
}

</script>
<style lang="scss" scoped>
@import "../../../assets/Compra/css/menu";
</style>
