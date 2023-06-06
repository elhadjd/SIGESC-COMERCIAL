<template>
  <div class="Principal h-100">
    <div class="Footer">
      <div class="TitlsListas d-flex w-100 text-secondary">
        <div>Orden</div>
        <div>fornecedor</div>
        <div>Responsavel</div>
        <div>Data da encomenda</div>
        <div>Data de vencimento</div>
        <div class="text-right">Total de mercadoria</div>
        <div class="text-right">Total da fatura</div>
        <div>Estado de pagamento</div>
        <div>Estado da fatura</div>
      </div>
      <div class="FormLista">
        <div @click="showOrder(order)" class="d-flex ListaFaturas" v-for="order in Orders?.list.data"
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
                <label for="">Total de faturas</label>
                <span>{{formatMoney(Totals.totalOrders)}}</span>
            </div>
            <div>
                <label for="">Resto a pagar</label>
                <span>{{formatMoney(Totals.restPayable)}}</span>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted,ref, watch } from "@vue/runtime-core";
import useEventsBus from '@/Eventbus';

const {bus , emit} = useEventsBus();

const emits = defineEmits(['message'])

const props = defineProps({
    Orders: Object,
});

const Totals = ref({
    totalOrders:0,
    restPayable:0
})

const Dinheiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'});
onMounted(()=>{
    sumOrders(props.Orders.list.data)
})
watch(()=>bus.value.get('PerquisarEncomenda'), (payloads) => {
    const payload = payloads[0]
    const FilterSearch = props.Orders.store.filter(object=>{
        return String(object.supplier?.name).toLowerCase().includes(payload.toLowerCase())
        || String(object.total).includes(payload)
        || String(object.state).toLowerCase().includes(payload.toLowerCase())
    })
    if (FilterSearch.length<=0) {
        emits('message','Nemhuma fatura encontrada','info')
    }else{
        props.Orders.list.data = FilterSearch
        sumOrders(props.Orders.list.data)
    }
});

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
@import "../../../assets/faturacao/css/ListaFaturas";
.FormLista{
    height: 90% !important;
}
</style>
