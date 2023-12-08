<template>
<Progress v-if="$store.state.pos.StateProgress"/>
  <div class="Muvemento w-100">
    <div class="w-100 h-100">
        <div class="d-flex w-100 OrdenCima">
            <div class="OrdenCimaEsquerda w-50">
                <h3 @click="FecharMuv" class="TipoMuve ms-4">Analise de {{ product.movementsProduct.movement.name }} de {{product.data.nome}} </h3>
            </div>
            <div class="w-50">
                <span class="p-input-icon-right w-100">
                    <i class="pi pi-search" />
                    <InputText @keyup="Pesquisar" v-model="data" placeholder="Pesquisar... Exemplo 2022-09-21" class="w-100 p-2"/>
                </span>
                <span class="w-50" v-show="product.movementsProduct.movement.name == 'Muvementos'">
                    <select class="w-50" v-model="Select">
                        <option >{{Select}}</option>
                        <option v-if="Select != 'Muvementos'">{{$t('words.movement')}}</option>
                        <option v-if="Select != 'Entrada'">{{$t('words.entry')}}</option>
                        <option v-if="Select != 'Saida'">{{$t('words.output')}}</option>
                        <option v-if="Select != 'Entrada&Saida'">Entrada&Saida</option>
                    </select>
                </span>
            </div>
        </div>
        <div class="ListaMuvemento">
            <div class="d-flex titleOrden border-bottom w-100">
                <strong class="d-flex w-100">
                    <div class="w-20">{{$t('words.article')}}</div>
                    <div>Ref Da Orden</div>
                    <div>{{$t('words.employee')}}</div>
                    <div>{{$t('words.typeOperation')}}</div>
                    <div>{{$t('words.reason')}}</div> 
                    <div class="text-end px-3">{{$t('words.quantity')}}</div>
                </strong>
            </div>
            <div class="overflow-auto ListaOrden">
                <div v-for="data in movements" :key="data.id" class="MostrarLista">
                    <div @click="showMovement(data[0].dia)" class="ListData">
                        <span>{{data[0].dia}}</span>
                        <span v-html="data[0].movement_type.movement_translate[0].translate + ' : '+ sumQuantity(data)"></span>
                    </div>
                    <div v-if="showItem == data[0].dia && state == true" class="h-25 LinhaMostrar">
                        <div v-for="item in data" :key="item.id" class="Esconder border-bottom">
                            <div class="d-flex ListaDeMuvementos">
                                <div class="w-25">{{item.product.nome}} </div>
                                <div>{{item.quantityAfter+".00Un(s)"}}</div>
                                <div>{{item.surname}}</div>
                                <div>{{item.movement_type.movement_translate[0].translate}}</div>
                                <div>{{item.motive}}</div>
                                <div class="text-end px-3">
                                    <i :class="item.movement_type.name == 'Saida' ? 'fa fa-arrow-down Saidas':item.movement_type.name == 'Entrada' ? 'fa fa-arrow-up text-success Entradas': 'fa fa-cart-arrow-down text-warning'"></i>
                                    {{item.quantity}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import Progress from '@/components/confirmation/progress.vue'
import InputText from 'primevue/inputtext'
import axios from 'axios';
import { computed, onMounted , ref, watch} from '@vue/runtime-core';
import { useStore } from 'vuex';
const product = computed(()=> store.getters['Product/product'])

const showItem = ref()
const movements = ref();
const store = useStore()
const data = ref();
const listamuv = ref()
const state = ref(false);
const Select = ref('Muvementos')

onMounted(() => {
    store.state.pos.StateProgress = true
    axios.get(`/movements/${product.value.data.id}/${product.value.movementsProduct.movement.id}`)
    .then((Response) => {
        movements.value = Response.data;
        store.state.pos.StateProgress = false
    }).catch((err) => {
        console.log(err);
    });
})

const FecharMuv =(()=>{
    product.value.movementsProduct.state = false
})

watch(Select, (newX) => {
    if (newX != 'Muvementos') {
        store.state.StateProgress = true
        axios.post(`/Muvementos`,{
            escolha: newX,
            produto: product.value.data.id
        })
        .then((Response) => {
            listamuv.value = Response.data
            store.state.StateProgress = false
        }).catch((err) => {
            console.log(err);
        });
    }
})

const Pesquisar = (()=>{
    axios.post(`/Muvementos/${product.value.data.id}`,{data:data.value})
    .then((Response) => {
        movements.value = Response.data;
    }).catch((err) => {
        console.log(err);
    });
})

const showMovement = ((event) =>{
    if (event == showItem.value) {
        state.value = false
        showItem.value = ""
    }else{
       state.value = true
        showItem.value = event
    }

})

const sumQuantity = ((movement)=>{
    let total = 0
    movement.forEach((item)=>{
        total += item.quantity
    })
    return total
})
</script>

<style scoped lang="scss">
@import '../../../../assets/produtos/css/muvementos';
</style>
