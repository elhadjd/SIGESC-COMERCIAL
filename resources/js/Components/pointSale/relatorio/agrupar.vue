<template>
    <div class="user-select-none Agrupar">
        <div class=" mt-1">
            <button @click="AbrirForm">
                <i class="fa fa-bars"></i>
                <span>{{$t('words.group')}}</span>
            </button>
        </div>
        <div v-if="EstadoForm" class="Form1 shadow-lg">
            <div class="d-flex">
                <input type="radio" class="InputRadio mt-2 bg-secondary" id="users" value="users" v-model="Agrupar"/>
                <div class="Opcoes" @click="Opcao(tables.tableUser)">Vendedor</div>
                <div class="SubOpcao shadow-lg" v-if="opcaos == tables.tableUser">
                    <label v-for="Lista in Listas" :key="Lista.id">
                        <input type="radio" class="InputRadio" :id="Lista.id" :value="Lista.id" v-model="user" @click="Select(Lista.id,'id_responsavel')"/>
                        <span :for="Lista.id">{{Lista.apelido}}</span>
                    </label>
                </div>
            </div>
            <div class="d-flex">
                <input type="radio" class="InputRadio mt-2 bg-secondary" id="cliente_pos" value="cliente_pos" v-model="Agrupar"/>
                <div class="Opcoes" @click="Opcao(tables.TableCliente)">Cliente</div>
                <div class="SubOpcao shadow-lg" v-if="opcaos == tables.TableCliente">
                    <input type="text" @keyup="PesquisarCliente" v-model="NomeCliente" class="w-100 PesquisarCliente" placeholder="Pesquisar clientes">
                    <label v-for="Lista in Listas" :key="Lista.id">
                        <input type="radio" class="InputRadio" :id="Lista.id" :value="Lista.id" v-model="cliente" @click="Select(Lista.nome,'cliente')"/>
                        <span :for="Lista.id">{{Lista.nome}}</span>
                    </label>
                </div>
            </div>
            <div class="d-flex">
                <div class="Opcoes" @click="Opcao('data')">Data</div>
                <div v-if="opcaos == 'data'" class="SubOpcao shadow-lg">
                    <label for="ano">
                        <input type="radio" class="InputRadio" id="ano" value="ano" v-model="data" @click="Select('ano','data')"/>
                        <span>Ano</span>
                    </label>
                    <label for="mes">
                        <input type="radio" class="InputRadio" id="mes" value="mes" v-model="data" @click="Select('mes','data')"/>
                        <span>Mes</span>
                    </label>
                    <label>
                        <input type="radio" class="InputRadio" id="semana" value="semana" v-model="data" @click="Select('semana','data')"/>
                        <span for="Annulado">Semana</span>
                    </label>
                    <!-- <label for="ontem">
                        <input type="radio" class="InputRadio" id="ontem" value="ontem" v-model="data" @click="Select('ontem','data')"/>
                        <span>Ontem</span>
                    </label> -->
                    <label for="hoje">
                        <input type="radio" class="InputRadio" id="hoje" value="hoje" v-model="data" @click="Select('hoje','data')"/>
                        <span>Hoje</span>
                    </label>
                    <label for="dia">
                        <input type="radio" class="InputRadio" id="dia" value="dia" v-model="data" @click="Select('dia','data')"/>
                        <span>Dia</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { onMounted, reactive, ref, watch } from '@vue/runtime-core'
import axios from 'axios';
import { useStore } from 'vuex';

export default {
    components: {
    },
    props: {
        Agrupar: Object
    },
    setup(props , {emit}){
        const store = useStore()
        const EstadoForm = ref(false)
        const opcaos = ref(null);
        const user = ref()
        const cliente = ref()
        const estado = ref()
        const data = ref();
        const Agrupar = ref()
        const tables = ref( {
                table: 'encomendas_pos',
                tableUser: 'users',
                TableCliente: 'cliente_pos',
                join: 'cliente_pos'
            })
        const colun = reactive({
            colun0: null,
            colun1: null,
            value: null,
            table: {
                table: 'encomendas_pos',
                tableUser: 'users',
                TableCliente: 'cliente_pos',
                join: 'cliente_pos'
            },
        })
        const Listas = ref()
        const AbrirForm = (()=>{
            EstadoForm.value = !EstadoForm.value
            colun.colun0 = null
            colun.colun1 = null
        })

        onMounted((event)=>{
        })

        const Select = ((event,outro)=>{
            if (colun.colun0 != null && colun.colun0 != outro) {
                colun.colun1 = outro
                colun.value = event
            } else {
                colun.colun0 = outro
                colun.value = event
            }
            axios.post('/AgruparRelatorio',colun)
            .then((Response) => {
                store.state.Painel = Response.data;
                EstadoForm.value = false
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
            if (event == 'data') {
                if (event == opcaos.value) {
                    opcaos.value = null
                }else{
                opcaos.value = event
                }
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

        watch(Agrupar,(novo)=>{
            axios.post('/AgruparRelatorio',{dados:novo})
            .then((Response) => {
                store.state.Painel = Response.data
                EstadoForm.value = false

            }).catch((err) => {
                console.log(err);
            });
        })

        return{EstadoForm,store,AbrirForm,opcaos,Opcao,Listas,data,
        Select,colun,user,tables,cliente,estado,PesquisarCliente,Agrupar}
    }
}
</script>

<style scoped lang="scss">
@import '../../../../assets/filterSearch/css/filter';
</style>
