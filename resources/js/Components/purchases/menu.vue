<template>
  <div class="principal">
    <div class="Header">
        <div class="Header-left">
            <div>
                <h2>Lista das compras</h2>
            </div>
            <div>
               <button @click="NewPurchase">Criar Compra
                <i v-if="loading=='new'" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
               </button>
            </div>
        </div>
        <div class="Header-right">
            <span class="p-input-icon-right w-100">
                <i class="pi pi-search" />
                <input type="text" @keyup="SearchInvoice" placeholder="Pesquisar...">
            </span>
            <pagination v-if="loading != 'start'" @page="getPage" :object="Orders.list"/>
        </div>
    </div>
    <div class="Container">
        <div v-if="loading == 'start'" class="progress"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i></div>
        <order v-else @message="message" :Orders="Orders"/>
    </div>
  </div>
</template>

<script setup>
import progress from '@/Components/confirmation/progress.vue'
import pagination from '@/Layouts/paginations/paginate.vue'
import order from './Orders.vue'
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import useEventsBus from '@/Eventbus';
import {Search} from '@/composable/public/search'
const {emit} = useEventsBus();

const emits = defineEmits(['modulos','message'])
const Orders = ref({
    list: [],
    store: [],
    search:[]
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
    }).catch((erro) => {
      console.log(erro);
    }).finally(()=>{
        loading.value = null
    });
}

const message = ((message,tipo)=>{
    emits('message',message,tipo)
})

const SearchInvoice = ((event)=>{
    emit('PerquisarEncomenda',event.target.value)
})

</script>

<style lang="scss" scoped>
@import "../../../assets/Compra/css/menu";
</style>
