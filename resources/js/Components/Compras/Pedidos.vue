<template>
  <div class="Principal h-100">
    <div class="Footer">
      <div class="TitlsListas d-flex w-100 text-secondary">
        <div>Orden</div>
        <div>fornecedor</div>
        <div>Responsavel</div>
        <div>Data da encomenda</div>
        <div class="text-right">Total de mercadoria</div>
        <div class="text-right">Total da fatura</div>
        <div>Estado de pagamento</div>
        <div>Estado da fatura</div>
      </div>
      <div class="FormLista">
        <div @click="CompraPedidoFormulario(listaPedidos)" class="d-flex ListaFaturas" v-for="listaPedidos in listaPedidos"
        :key="listaPedidos.id">
          <div>{{'Compra/00'+listaPedidos.id}}</div>
          <div>{{listaPedidos.empresa}}</div>
          <div>{{listaPedidos.apelido}}</div>
          <div>{{listaPedidos.created_at}}</div>
          <div class="text-right">{{Dinheiro.format(Number(listaPedidos.total) + Number(listaPedidos.total_desconto) - Number(listaPedidos.total_iva) - Number(listaPedidos.total_gastos))}}</div>
          <div class="text-right">{{Dinheiro.format(listaPedidos.total)}}</div>
          <div :class="listaPedidos.estado">
            <span>{{listaPedidos.estado}}</span>
          </div>
          <div :class="listaPedidos.estado">
            <span class="">{{listaPedidos.estado}}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="RodaPe">
        <div class="Totals">
            <div>
                <label for="">Total de faturas</label>
                <span>{{Dinheiro.format(TotalCompras)}}</span>
            </div>
            <div>
                <label for="">Resto a pagar</label>
                <span>{{Dinheiro.format(RestoPagar)}}</span>
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

const TotalCompras = ref()

const StoreCompra = ref([])

const RestoPagar = ref()

const listaPedidos = ref([]);

const Dinheiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'});

watch(()=>bus.value.get('PerquisarEncomenda'), (payloads) => {
    const payload = payloads[0]
    const FilterSearch = StoreCompra.value.filter(object=>{
        return String(object.empresa).toLowerCase().includes(payload.toLowerCase())
        || String(object.total).includes(payload)
        || String(object.estado).toLowerCase().includes(payload.toLowerCase())
    })
    if (FilterSearch.length<=0) {
        emits('message','Nemhuma fatura encontrada','info')
    }else{
        listaPedidos.value = FilterSearch
    }
});

onMounted(() => {
    axios.get("/BuscarCompras").then((response) => {
        RestoPagar.value = response.data.RestoPagar
        TotalCompras.value = response.data.TotalCompras;
        listaPedidos.value = response.data.compras
        StoreCompra.value = response.data.compras
    }).catch((erro) => {
      console.log(erro);
    });
});

const CompraPedidoFormulario = (Compra) => {
    emit('CompraPedidoFormulario',Compra)
}
</script>

<style lang="scss" scoped>
@import "../../../assets/faturacao/css/ListaFaturas";
.FormLista{
    height: 90% !important;
}
</style>
