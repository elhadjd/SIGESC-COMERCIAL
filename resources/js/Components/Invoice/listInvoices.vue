<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <h2>{{ $store.state.titulo }}</h2>
            <button class="BotaoCrear" @click="newInvoice">Criar Fatura
                <i v-if="loading == 'new'" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
            </button>
         </div>
         <div class="Header-right">
            <span class="p-input-icon-right w-100">
                <i class="fa fa-search"></i>
                <input @keyup="SearchInvoice" type="text" placeholder="Pesquisar...">
            </span>
            <div>
                <div>
                    <i @click="listType = 'list'" class="fa fa-list"></i>
                    <i @click="listType = 'large'" class="fa fa-th-large" aria-hidden="true"></i>
                </div>
                <pagination v-if="loading != 'start'" @page="getPage" :object="Invoices.list "/>
            </div>
         </div>
      </div>
      <div class="Container">
        <invoices-th v-if="listType == 'large'" @showInvoice="onRowSelect" :Invoices="Invoices"/>
        <div v-if="listType == 'list'" class="list">
            <div class="Title text-secondary">
                <div>Orden</div>
                <div>Usuario</div>
                <div>Cliente</div>
                <div>Data da Encomenda</div>
                <div>Data de Vencimento</div>
                <div class="text-right">Total sem desconto</div>
                <div class="text-right">Total da fatura</div>
                <div>Resto a pagar</div>
                <div>Estado da fatura</div>
            </div>

            <div class="list_items">
                <div class="rows"
                    @click="onRowSelect(item)"
                    v-for="item in Invoices.list.data"
                    :key="item.id
                ">
                <div>{{item.orderNumber+item.id}}</div>
                <div>{{item.user.surname}}</div>
                <div>{{item.client?.surname}} </div>
                <div>{{ item.DateOrder != null ? formateDate(item.DateOrder) : formateDate(item.created_at) }} </div>
                <div>{{item.DateDue != null ? formateDate(item.DateDue) : 'Não definido'}} </div>
                <div class="text-end">{{FrmatDinheiro.format(item.TotalMerchandise)}} </div>
                <div class="text-end">{{FrmatDinheiro.format(item.TotalInvoice)}}</div>
                <div>
                    <span>
                    {{item.RestPayable >=1 ? FrmatDinheiro.format(item.RestPayable): FrmatDinheiro.format(0)}}
                    </span>
                </div>
                <div :class="item.state">
                    <span class="">
                    {{item.state}}
                    </span>
                </div>
                </div>
            </div>
        </div>

         <div class="RodaPe">
            <div class="Totals">
               <div>
                  <label for="">Total de faturas</label>
                  <span>{{FrmatDinheiro.format(Invoices.TotalFaturas)}}</span>
               </div>
               <div>
                  <label for="">Resto a pagar</label>
                  <span>{{FrmatDinheiro.format(Invoices.RestoALiquidar)}}</span>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import pagination from '@/Layouts/paginations/paginate.vue'
import { mapState ,useStore } from "vuex"
import moment from 'moment'
import { Search } from '@/composable/public/search';
import invoicesTh from './invoicesTh.vue'

const Invoices = ref({
   search: [],
   TotalFaturas: 0,
   RestoALiquidar: 0,
   list: []
})
const loading = ref(null)
const listType = ref('large')
const emits = defineEmits(['fatura', 'message'])

onMounted(async() => {
   await getInvoices();
})

const getPage = ((data)=>{
    Invoices.value.search = data.data
    Invoices.value.list = data
    localStorage.setItem('listStorePaginate',JSON.stringify(data))
})

const {getFilter} = Search(Invoices.value,loading);

const getInvoices = async () => {
    store.state.pos.StateProgress = true;
    await axios.get('getInvoices').then((Response) => {
      Invoices.value.search = Response.data.data
      Invoices.value.list = Response.data
      localStorage.setItem('listStorePaginate',JSON.stringify(Response.data))
      SumInvoice(Invoices.value.list.data)
   }).catch((err) => {
      console.log(err);
   }).finally(()=>{
    store.state.pos.StateProgress = false;
   });
}

const formateDate = ((data) => {
   return moment(data).format('DD-MM-YYYY')
})


let FrmatDinheiro = ref(new Intl.NumberFormat('pt-AO', {
   style: 'currency',
   currency: 'AOA'
}))
const store = useStore();


const onRowSelect = (event) => {
   emits('fatura', event);
};

const newInvoice = () => {
    loading.value = 'new'
   axios.post('NewOrder').then((response) => {
      emits('fatura', response.data);
   }).catch((err) => {
      console.log(err);
   }).finally(()=>{
    loading.value = null
   });
}

const SearchInvoice = (async(event) => {
    if (event.target.value === null || event.target.value === "") {
        Invoices.value.list.data = JSON.parse(localStorage.getItem('listStorePaginate')).data
        Invoices.value.search = JSON.parse(localStorage.getItem('listStorePaginate')).data
        return false
    }

    if (search(event.target.value).length == 0) {
        await getFilter(`/search/invoices/orderNumber/${event.target.value}`)
        Invoices.value.list.data = search(event.target.value)
    } else {
      Invoices.value.list.data = search(event.target.value)
    }
})

const search = (event) => {
   return Invoices.value.search.filter(object => {
        return String(object.client?.surname).toLowerCase().includes(event.toLowerCase()) ||
            String(object.DateOrder).toLowerCase().includes(event.toLowerCase()) ||
            String(object.TotalInvoice).includes(event) ||
            String(object.state).toLowerCase().includes(event.toLowerCase()) ||
            String(object.orderNumber+object.id).toLowerCase().includes(event.toLowerCase())
    })
}

const SumInvoice = ((Invoice) => {
   Invoices.value.TotalFaturas = 0
   Invoices.value.RestoALiquidar = 0
   Invoice.forEach(Invoice => {
      Invoices.value.TotalFaturas += Number(Invoice.TotalInvoice)
      Invoices.value.RestoALiquidar += Number(Invoice.RestPayable)
   });
})
</script>
<style scoped lang="scss">
@import '../../../assets/faturacao/css/ListaFaturas.scss';
</style>
