<template>
    <div class="w-25 user-select-none Agrupar">
        <div class="mt-1">
            <button @click="AbrirForm">
                <i class="fa fa-filter"></i>
                <span>Filtrar</span>
            </button>
        </div>
        <div v-if="EstadoForm" class="Form1 shadow-lg">
            <div>
                <div class="Opcoes" @click="$emit('filtro','Id da Fatura'),EstadoForm = false">Id da Fatura</div>
            </div>
            <div>
                <div class="Opcoes" @click="$emit('filtro','Total da Fatura'),EstadoForm = false">Total da fatura</div>
            </div>
            <div>
                <div class="Opcoes" @click="$emit('filtro','Total maior Igual'),EstadoForm = false">Total maior igual</div>
            </div>
            <div>
                <div class="Opcoes" @click="$emit('filtro','Total menor Igual'),EstadoForm = false">Total menor igual</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from '@vue/runtime-core'
import axios from 'axios';
import { useStore } from 'vuex';

const store = useStore()
const EstadoForm = ref(false)
const opcaos = ref(null);
const user = ref()
const cliente = ref()
const estado = ref()
const tables = ref()
const colun = reactive({
    colun0: null,
    colun1: null,
    value: null,
})
const Listas = ref()
const AbrirForm = (()=>{
    EstadoForm.value = !EstadoForm.value
    colun.colun0 = null
    colun.colun1 = null
})



const Select = ((event,outro)=>{
    if (colun.colun0 != null && colun.colun0 != outro) {
        colun.colun1 = outro
        colun.value = event
    } else {
        colun.colun0 = outro
        colun.value = event
    }
    axios.post('/Agrupar',colun)
    .then((Response) => {
        store.state.ListEncomenda.ListaDefault = Response.data
    }).catch((err) => {
        console.log(err);
    });
})


const PesquisarCliente = ((event)=>{
    if (event.target.value.length == 3) {
        axios.post('/pesquisas',{
            table: tables.value.TableCliente,
            colun1: 'nome',
            nome: event.target.value
            })
        .then((Response) => {
            Listas.value = Response.data.resultado
        }).catch((err) => {

        });
    } else if(event.target.value.length > 3) {
        const filtrar = Listas.value.find(o=>{
            return o.nome.includes(event.target.value)
        })
        Listas.value = filtrar
    }
})


const Opcao = ((event)=>{
    if (event == 'Estado') {
        opcaos.value = event
    } else {
        if (event == opcaos.value) {
            opcaos.value = null
        }else{
            opcaos.value = event
        }
    }
})

</script>

<style scoped lang="scss">
@import '../../../assets/FiltrosPesquisas/css/Agrupar';
</style>
