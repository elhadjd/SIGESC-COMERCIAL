<template>
  <div>
    <div class="text-secondary">
        <div class="InputPesquisar">

            <span class="p-input-icon-right d-flex w-100">
                <strong class="TipoFiltro">{{ TipoPesquisar.tipo }}</strong>
                <i class="pi pi-search" />
                <input @keyup.enter="PesquisarOrden" v-model="NumeroOrden" type="search" class="Pesquisar" :placeholder="TipoPesquisar.placeholder"/>
            </span>
        </div>
    </div>
    <div class="d-flex w-50">
        <Filtros @Filtro="filtro"/>
        <Agrupar @ListaDefault="PassarLista" :Agrupar="Agrupare"/>
    </div>
  </div>
</template>

<script setup>
import { onMounted , ref , reactive, watch} from '@vue/runtime-core';
import Agrupar from '@/components/FiltrosPesquisas/Agrupar.vue'
import Filtros from '@/components/FiltrosPesquisas/filtros.vue'
import { useStore } from 'vuex';
const props = defineProps({
    Filtros: null,
    Agrupar: null
})

const emits = defineEmits('ListaDefault')
const TipoPesquisar = ref({
    colun: 'id',
    tipo: 'Id da Fatura',
    placeholder: 'Resultado por id da fatura'
});
const ListaDefault = ref([])
const NumeroOrden = ref();
const store = useStore();
const Agrupare = reactive(props.Filtros.Agrupar)

onMounted(()=>{
    axios.get('/PDV/getOrders')
    .then((Response) => {
        ListaDefault.value = Response.data
        PassarLista(ListaDefault.value)
    }).catch((err) => {
        console.log(err);
    });
})

const PassarLista = ((list)=>{
    emits('ListaDefault',list)
})

const filtro = ((event)=>{
    if (event == 'Id da Fatura') {
        TipoPesquisar.value.colun = 'id',
        TipoPesquisar.value.placeholder = 'Resultado por id da fatura'
    } else if(event == 'Total da Fatura') {
        TipoPesquisar.value.colun = 'total'
        TipoPesquisar.value.placeholder = 'Resultado por total da fatura'
    }else if(event == 'Total maior Igual') {
        TipoPesquisar.value.colun = 'TotalMaior'
        TipoPesquisar.value.placeholder = 'Resultado por orden total maior'
    }else if(event == 'Total menor Igual') {
        TipoPesquisar.value.colun = 'TotalMenor'
        TipoPesquisar.value.placeholder = 'Resultado por orden total menor'
    }
    TipoPesquisar.value.tipo = event
})

const PesquisarOrden = (()=>{
    axios.get('/PDV/getOrders?IdOrden=' + NumeroOrden.value+'&colun='+TipoPesquisar.value.colun)
    .then((Response) => {
        ListaDefault.value = Response.data
        PassarLista(ListaDefault.value)
    }).catch((err) => {
    console.log(err);
    });

})
</script>

<style scoped lang="scss">
@import '../../../assets/FiltrosPesquisas/css/Agrupar';
</style>
