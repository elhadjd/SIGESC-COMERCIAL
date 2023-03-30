<template>
   <div class="Principal">
      <div class="Header">
         <div class="titulo">
            <h1>{{ $store.state.titulo }}</h1>
            <button class="BotaoCrear" @click="Abrir_form_faturacao">Criar Fatura</button>
         </div>
         <div class="filtros">
            <span class="p-input-icon-right w-100">
            <i class="fa fa-search"></i>
            <input @keyup="SearchInvoice" type="text" placeholder="Pesquisar...">
            </span>
         </div>
      </div>
      <div class="Footer">
         <div class="TitlsListas d-flex w-100 text-secondary">
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
         <div class="FormLista">
            <div class="d-flex ListaFaturas"
                @click="onRowSelect(item)"
                v-for="item in Invoices.ListaFaturas" 
                :key="item.id
            ">
               <div>{{'FT'+item.created_at.substring(3, item.created_at.length - 11)+'/00'+item.id}}</div>
               <div>{{item.user.surname}}</div>
               <div>{{item.client.surname}} </div>
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
import { ref, onMounted,defineEmits, reactive } from 'vue';
import { mapState ,useStore } from "vuex"
import moment from 'moment'

const Invoices = ref({
   StoreInvoice: [],
   TotalFaturas: 0,
   RestoALiquidar: 0,
   ListaFaturas: []
})

const emits = defineEmits(['fatura', 'message'])

onMounted(() => {
   getInvoices();
})

const getInvoices = () => {
   axios.get('getInvoices').then((Response) => {
      Invoices.value.StoreInvoice = Response.data
      Invoices.value.ListaFaturas = Response.data
      SumInvoice(Invoices.value.ListaFaturas)
   }).catch((err) => {
      console.log(err);
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

const Abrir_form_faturacao = () => {
   axios.get('/NovaFatura').then((result) => {
      emits('fatura', result.data);
   }).catch((err) => {
      console.log(err);
   });
}

const SearchInvoice = ((event) => {
   const FilterSearch = Invoices.value.StoreInvoice.filter(object => {
      return String(object.apelido).toLowerCase().includes(event.target.value.toLowerCase()) ||
         String(object.created_at).toLowerCase().includes(event.target.value.toLowerCase()) ||
         String(object.TotalFatura).includes(event.target.value) ||
         String(object.estado).toLowerCase().includes(event.target.value.toLowerCase())
   })
   if (FilterSearch.length <= 0) {
      emits('message', 'Nenhuma fatura foi encontrada', 'info')
   } else {
      Invoices.value.ListaFaturas = FilterSearch
      return SumInvoice(FilterSearch)
   }
})

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
