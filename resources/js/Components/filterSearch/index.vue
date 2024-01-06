<template>
  <div class="FilterOrder">
        <div>
            <div class="InputPesquisar">
                <span class="p-input-icon-right d-flex w-100">
                    <strong class="TipoFiltro truncate">{{ searchType.type }}</strong>
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
import { useI18n } from 'vue-i18n';
import { serviceMessage } from '@/composable/public/messages';
const {showMessage} = serviceMessage()
const {t} = useI18n()
const emits = defineEmits(['ListDefault'])
const searchType = ref({
    column: 'number',
    type: t('words.order'),
    placeholder: `${t('words.number')} ${t('words.of')} ${t('words.order')}`
});
const ListaDefault = ref([])
const NumeroOrden = ref();
const store = useStore();

onMounted(()=>{
    store.state.pos.StateProgress = true
    axios.get(`/PDV/getOrders/${localStorage.getItem('locale') || 'en'}`)
    .then((Response) => {
        if(Response.data.message) return showMessage(Response.data.message,Response.data.type);
        ListaDefault.value = Response.data
        PassarLista(ListaDefault.value)
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        store.state.pos.StateProgress = false
    });
})

const PassarLista = ((list)=>{
    emits('ListDefault',list)
})

const filtro = ((event)=>{
    if (event == t('words.order')) {
        searchType.value.column = 'number',
        searchType.value.placeholder = `${t('words.number')} ${t('words.of')} ${t('words.order')}`
    } else if(event == t('words.total')) {
        searchType.value.column = 'total'
        searchType.value.placeholder = `${t('words.total')} ${t('words.of')} ${t('words.order')}`
    }else if(event == `${t('words.total')} ${t('words.greaterEqual')}`) {
        searchType.value.column = 'TotalMaior'
        searchType.value.placeholder = `${t('words.total')} ${t('words.greaterEqual')}`
    }else if(event == `${t('words.total')} ${t('words.lessEqual')}`) {
        searchType.value.column = 'TotalMenor'
        searchType.value.placeholder = `${t('words.total')} ${t('words.lessEqual')}`
    }
    else if(event == t('words.client')) {
        searchType.value.column = 'cliente'
        searchType.value.placeholder = t('words.client')
    }
    searchType.value.type = event
})

const PesquisarOrden = (()=>{
    axios.get(`/PDV/getOrders/${localStorage.getItem('locale') || 'en'}/${NumeroOrden.value}/${searchType.value.column}`)
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
