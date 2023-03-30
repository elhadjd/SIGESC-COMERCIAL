<template>
    <div class="user-select-none Agrupar mt-1">
        <div class="">
            <button @click="AbrirForm">
                <i class="fa fa-bars"></i>
                <span>Agrupar</span>
            </button>
        </div>
        <div v-if="EstadoForm" class="Form1 shadow-lg">
            <div>
                <div class="Opcoes" @click="Opcao(tables.tableUser)">Vendedor</div>
                <div class="SubOpcao shadow-lg" v-if="opcaos == tables.tableUser">
                    <label v-for="Lista in Listas" :key="Lista.id">
                        <input type="radio" class="InputRadio" :id="Lista.id" :value="Lista.id" v-model="user" @click="Select(Lista.id,'id_responsavel')"/>
                        <span :for="Lista.id">{{Lista.apelido}}</span>
                    </label>
                </div>
            </div>
            <div>
                <div class="Opcoes" @click="Opcao(tables.TableCliente)">Cliente</div>
                <div class="SubOpcao shadow-lg" v-if="opcaos == tables.TableCliente">
                    <input type="text" @keyup="PesquisarCliente" v-model="NomeCliente" class="w-100 PesquisarCliente" placeholder="Pesquisar clientes">
                    <label v-for="Lista in Listas" :key="Lista.id">
                        <input type="radio" class="InputRadio" :id="Lista.id" :value="Lista.id" v-model="cliente" @click="Select(Lista.nome,'cliente')"/>
                        <span :for="Lista.id">{{Lista.nome}}</span>
                    </label>
                </div>
            </div>
            <div>
                <div class="Opcoes" @click="Opcao('Estado')">Estado</div>
                <div v-if="opcaos == 'Estado'" class="SubOpcao shadow-lg">
                    <label for="Pago">
                        <input type="radio" class="InputRadio" id="Pago" value="Pago" v-model="estado" @click="Select('Pago','estado')"/>
                        <span>Pago</span>
                    </label>
                    <label>
                        <input type="radio" class="InputRadio" id="Annulado" value="Annulado" v-model="estado" @click="Select('Annulado','estado')"/>
                        <span for="Annulado">Annulado</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from '@vue/runtime-core'
import axios from 'axios';
import { useStore } from 'vuex';

const props = defineProps({
    Agrupar: Object
})
const emits = defineEmits(['ListaDefault'])
const store = useStore()
const EstadoForm = ref()
const opcaos = ref(null);
const user = ref()
const cliente = ref()
const estado = ref()

const ListaDefault = ref([])
const tables = ref(props.Agrupar[0])
const colun = reactive({
    colun0: null,
    colun1: null,
    value: null,
    table: props.Agrupar[0].table,
    join: props.Agrupar[0].join
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
        ListaDefault.value = Response.data
        emits('ListaDefault',ListaDefault.value)
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
            axios.post('/Agrupar',{
                dados: event
            })
            .then((Response) => {
                opcaos.value = event
                Listas.value = Response.data;
            }).catch((err) => {

            });
        }
    }
})
</script>

<style scoped lang="scss">
@import '../../../assets/FiltrosPesquisas/css/Agrupar';
</style>
