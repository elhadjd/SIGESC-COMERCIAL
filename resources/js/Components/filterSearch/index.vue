<template>
  <div class="FilterOrder">
        <div>
            <div class="InputPesquisar">

                <span class="p-input-icon-right d-flex w-100">
                    <strong class="TipoFiltro">{{ searchType.type }}</strong>
                    <i class="pi pi-search" />
                    <input @keyup.enter="PesquisarOrden" v-model="NumeroOrden" type="search" class="Pesquisar" :placeholder="searchType.placeholder"/>
                </span>
            </div>
        </div>

        <div>
            <filter-search @Filtro="filtro"/>
            <group-by @ListaDefault="PassarLista"/>
        </div>
  </div>
</template>

<script setup>
import { onMounted , ref , reactive, watch} from '@vue/runtime-core';
import groupBy from './groupBy.vue'
import filterSearch from './filter.vue'
import { useStore } from 'vuex';

const emits = defineEmits(['ListDefault'])
const searchType = ref({
    column: 'number',
    type: 'Numero de Fatura',
    placeholder: 'Resultado por numero de fatura'
});
const ListaDefault = ref([])
const NumeroOrden = ref();
const store = useStore();

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
    emits('ListDefault',list)
})

const filtro = ((event)=>{
    if (event == 'Numero da Fatura') {
        searchType.value.column = 'number',
        searchType.value.placeholder = 'Resultado por numero de fatura'
    } else if(event == 'Total da Fatura') {
        searchType.value.column = 'total'
        searchType.value.placeholder = 'Resultado por total da fatura'
    }else if(event == 'Total maior Igual') {
        searchType.value.column = 'TotalMaior'
        searchType.value.placeholder = 'Resultado por orden total maior'
    }else if(event == 'Total menor Igual') {
        searchType.value.column = 'TotalMenor'
        searchType.value.placeholder = 'Resultado por orden total menor'
    }
    else if(event == 'Cliente') {
        searchType.value.column = 'cliente'
        searchType.value.placeholder = 'Resultado por Cliente'
    }
    searchType.value.type = event
})

const PesquisarOrden = (()=>{
    axios.get(`/PDV/getOrders/${NumeroOrden.value}/${searchType.value.column}`)
    .then((Response) => {
        ListaDefault.value = Response.data
        PassarLista(ListaDefault.value)
    }).catch((err) => {
        console.log(err);
    });

})
</script>

<style scoped lang="scss">
@import '../../../assets/filterSearch/css/filter';
</style>
