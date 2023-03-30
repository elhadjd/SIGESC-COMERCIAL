<template>
<div class="Principal">
    <TransitionGroup name="list" tag="ul">
        <ShowOrder :dados="MostrarLista.dados" @fechar="MostrarLista.estado = false" v-if="MostrarLista.estado"/>
    </TransitionGroup>
    <section class="Header">
        <div>
            <button @click="$emit('NovoPedido')" class="NovoPedido">Nova encomenda</button>
            <button @click="$emit('Fechar')" class="Voltar">Voltar</button>
        </div>
        <div>
            <input type="text" @keyup="search" placeholder="Pesquisar...">
        </div>
    </section>
    <section class="Container">
        <div class="Titles">
            <div>Data</div>
            <div>Pedido</div>
            <div>Cliente</div>
            <div>Responsavel</div>
            <div>Total</div>
            <div>Estado</div>
        </div>
        <div class="List">
            <div v-for="item in ListaOrdens.slice(0,100)" :key="item.id" @mouseover="ViewPedido(item.number)" class="Items" @click="$emit('AlterarPedido',item.number)" >
                <div>{{formatDate(item.created_at)}}</div>
                <div>{{item.number}}</div>
                <div>{{item.cliente}}</div>
                <div>{{item.user.surname}}</div>
                <div>{{FormatarDineiro.format(item.total)}}</div>
                <div class="ViewPedido">{{item.state}}
                    <Transition>
                    <span @click="ShowList(item)" v-if="Mostra == item.number">Mostrar</span>
                    </Transition>
                </div>
            </div>
        </div>
    </section>
</div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import ShowOrder from '@/Components/pos/MostrarPedido.vue';
import moment from 'moment'
const FormatarDineiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})

const props = defineProps({session: Number})

const Encomendas = ref()

const ListaOrdens = ref([])

const MostrarLista = ref({
    estado: false,
    dados: null
})

const Pedidos = ref()

const session = ref()

const Mostra = ref(null)
const ordersStore = ref()

const ViewPedido = ((event)=>{
    Mostra.value = event
})

const formatDate = ((date)=>{
    return moment(date).format("DD MM YYYY H:M:S")
})

const ShowList = ((event)=>{
    if (event.state == "Pago") {
        MostrarLista.value.estado = true;
        MostrarLista.value.dados = event
    }
})

const search = ((event)=>{
    const filter = ordersStore.value.filter((item)=>{
        return String(item.id).includes(event.target.value)
    })

    ListaOrdens.value = filter
    ListaOrdens.value.reverse()
})

const OnMounted = onMounted(() => {
    session.value = localStorage.getItem('session')
    Encomendas.value = localStorage.getItem('Encomendas'+session.value);
    ListaOrdens.value = JSON.parse(Encomendas.value);
    ordersStore.value = JSON.parse(Encomendas.value)
    ListaOrdens.value.reverse()
})


</script>

<style  lang="scss" scoped>
    @import '../../../assets/Pos/css/ListePedido.scss';
</style>

