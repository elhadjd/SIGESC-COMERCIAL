<template>
  <div class="Principal">
    <div class="form-modal">
        <div v-if="!list_order.state" class="principal">
            <div class="Header">
                <div class="Header-left">
                    <span @click="$emit('closeModal')" class="Voltar">
                        <h2>{{$t('words.close')}}</h2>
                    </span>
                </div>
                <div class="Header-right">
                    <div>

                    </div>
                    <span class="p-input-icon-right w-100">
                        <i class="pi pi-search" />
                        <input @keyup="search" type="search" name="pesqusar_prod" id="pesqusar_prod"
                        :placeholder="$t('words.search')">
                    </span>
                </div>
            </div>
            <div class="Container">
                <div class="Title">
                    <div>Ref {{$t('words.order')}}</div>
                    <div>{{$t('apps.pdvName')}}</div>
                    <div>{{$t('words.sessions')}}</div>
                    <div>{{$t('words.client')}}</div>
                    <div>{{$t('words.date')}}</div>
                    <div>{{$t('words.employee')}}</div>
                    <div class="TotalOrden">{{$t('words.total')}}</div>
                    <div class="px-3">{{$t('words.state')}}</div>
                </div>
                <div class="list_items">
                    <div class="rows" @click="showOrder(order)" v-for="order in orders" :key="order.id">
                        <div>{{order.id}}</div>
                        <div>{{order.session.caixa.name}}</div>
                        <div>{{order.session_id}}</div>
                        <div>{{order.cliente}}</div>
                        <div>{{formatDate(order.created_at)}}</div>
                        <div>{{order.user?.surname}}</div>
                        <div class="TotalOrden">{{formatMoney(order.total)}}</div>
                        <div class="px-3">{{order.state}}</div>
                    </div>
                </div>
            </div>
        </div>
        <Encomendas @fechar="OnMounted()" :order="list_order.data" v-else/>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import moment from 'moment'
import Encomendas from './orderItems.vue'
const props = defineProps({
    idCaixa: String
})
const formatCurrency = Intl.NumberFormat('PT-AO',{style:"currency",currency: "AOA"})

const orders = ref([])
const list_order = ref({
    data: [],
    state: false,
})
const storeOrder = ref([])
 const FormatDate = ((date) =>{
    return moment(date).format('DD-MM-YYYY H:mm:ss')
 })
 const OnMounted = onMounted(()=>{
    list_order.value.state = false
    getOrderSingleUser()
})

const getOrderSingleUser = (()=>{
    axios.get(`getOrderSingleUser/${localStorage.getItem('locale') || 'en'}/${props.idCaixa}`)
    .then((response) => {
        storeOrder.value = response.data.data
        orders.value = response.data.data
    }).catch((err) => {
        console.log(err);
    });
})

const search = ((event)=>{
    const filter = storeOrder.value.filter((order)=>{
        return String(order.id).includes(event.target.value)
    })
    orders.value = filter
})

const showOrder = ((order)=>{
    list_order.value.data = order
    list_order.value.state = true
})
</script>

<style lang="scss" scoped>
    .Principal{
        @include modal;
        .form-modal{
            background-color: #f9f9f9f9;
            width: 95%;
            height: 88%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 5%;
            background-color: white;
            border-radius: 3px;
            @include components;
            .Container{
                border-radius: 3px;
                @include form_lists;
            }
        }
    }
</style>
