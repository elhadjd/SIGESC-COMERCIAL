<template>
<div class="Principal">
    <TransitionGroup name="list" tag="ul">
        <ShowOrder :dados="MostrarLista.dados" @close="MostrarLista.estado = false" v-if="MostrarLista.estado"/>
    </TransitionGroup>
    <section class="Header">
        <div>
            <button @click="$emit('NovoPedido')" class="NovoPedido">Nova encomenda</button>
            <button @click="$emit('close')" class="Voltar">Voltar</button>
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
            <div class="text-right">Total</div>
            <div>Estado</div>
            <div>Açoes</div>
        </div>
        <div class="List">
            <div v-for="item in ListaOrdens.slice(0,100)" :key="item.id" @mouseover="ViewPedido(item.number)" class="Items" @click="viewOrder(item)" >
                <div>{{formatDate(item.created_at)}}</div>
                <div>{{item.number}}</div>
                <div>{{item.cliente}}</div>
                <div>{{item.user.surname}}</div>
                <div class="text-right">{{formatMoney(item.total)}}</div>
                <div>{{item.state}}</div>
                <div class="ViewPedido">
                    <span v-if="item.state == 'Pago'" @click="edit(item)" class="edit">Editar</span>
                    <Transition>
                        <span  @click="ShowList(item)" v-if="Mostra == item.number && item.state == 'Pago'">Mostrar</span>
                    </Transition>
                </div>
            </div>
        </div>
    </section>
    <InvoiceCancel
        @showOrder="viewOrder"
        @close="invoice.state = $event"
        v-if="invoice.state"
        :invoice="invoice.data"
    />
</div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import ShowOrder from '@/Components/pos/MostrarPedido.vue';
import moment from 'moment'
import InvoiceCancel from './invoiceCancel.vue';
import axios from "axios";
const FormatarDineiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})

const props = defineProps({session: Number})
const emits = defineEmits(['AlterarPedido','close']);
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

const invoice = ref({
    data: [],
    state: false
})

const viewOrder = ((order,edit)=>{
    if (order.state != 'Pago' || edit) {
        emits('AlterarPedido',order.number,'edit')
        invoice.value.state = false
    }else{
        console.log();
    }
})

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
        return String(item.total).includes(event.target.value)
        || String(item.cliente).toLocaleLowerCase().includes(event.target.value.toLocaleLowerCase())
        || String(item.number).includes(event.target.value)
        || String(item.state).toLocaleLowerCase().includes(event.target.value.toLocaleLowerCase())
    })
    ListaOrdens.value = filter
})

const edit = ((item)=>{
    invoice.value.data = item
    invoice.value.state = true
})

const OnMounted = onMounted(async() => {
    await axios.get(`/PDV/getOrderSingleUser/${props.session}`)
    .then((response) => {
        ListaOrdens.value = response.data.data
        ordersStore.value = response.data.data
        filterOrder()
    }).catch((err) => {
        console.log(err);
    });
})

const filterOrder = (()=>{
    session.value = localStorage.getItem('session')
    Encomendas.value = JSON.parse(localStorage.getItem('Encomendas'+session.value));
    Encomendas.value.forEach(item => {
        if (item.state != 'Pago') {
            ListaOrdens.value.push(item)
        }
    });
    ListaOrdens.value.reverse()
})


</script>

<style  lang="scss" scoped>
    @import '../../../assets/Pos/css/listOrders.scss';
</style>

