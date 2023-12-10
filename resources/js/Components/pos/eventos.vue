<template>
    <div class="FormPagamentoCima">
        <div>
            <Cliente @cliente="client"/>
            <span @click="$emit('showProds')">Add Prod <i class="fa fa-plus"></i></span>
        </div>

        <div class="QtdPrcImpr">
            <div @click="print">{{$t('words.print')}}</div>
            <div @click="$emit('tipo','preco')">{{$t('words.price')}}</div>
            <div >{{$t('words.discount')}}</div>
            <div @click="$emit('tipo','quantidade')">{{$t('words.quantity')}}</div>
        </div>
        <div v-if="printOrder" class="print">
            <div class="Modal">
                <div class="Header">
                    <h2>{{`${$t('words.print')} ${$t('words.order')}`}}</h2>
                </div>
                <div class="Container">
                    <label for="number">{{`${$t('words.number')} ${$t('words.of')} ${$t('words.order')}`}}:</label>
                    <input type="text" class="normal-case" v-model="invoice.number" id="number" :placeholder="`${$t('words.number')} ${$t('words.of')} ${$t('words.order')}`">
                </div>
                <div class="Footer">
                    <button class="Descartar normal-case" @click="printInvoice('last')">{{`${$t('words.last')} ${$t('words.order')}`}}</button>
                    <button @click="printInvoice()">{{$t('words.print')}}</button>
                    <button class="Descartar normal-case" @click="printOrder = false">{{$t('words.close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="FormPagamentoBaixo">
        <div class="PagamentoEsquerda" @click="$emit('payment',true)">
            <i class="fa fa-credit-card-alt"></i>
            <div>{{$t('words.payment')}}</div>
        </div>
        <div class="PagamentoDireita">
            <div v-for="(i, index) in 9"
                class="nomeros" id="numero" :key="index" @click="numero(i)">
                {{ i }}
            </div>

            <div class="nomeros" @click="numero(0)" id="numero">{{ 0 }}</div>
            <div class="nomeros" @click="numero('.')" id="numero">.</div>
            <div id="numeros" @click="$emit('remover')">
                <i class="fa fa-ban" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from '@vue/runtime-core'
import axios from 'axios'
import Cliente from './cliente.vue'

const emits = defineEmits(['Alterar','cliente','remover','payment','tipo','showProds','message','invoice'])
const props = defineProps({
    idSession: Number,
    user:Object
})
const client = ((event)=>{
    emits('cliente',event)
})

const printOrder = ref(false)

const numero = ((event)=>{
    emits('Alterar',event)
})

const invoice = reactive({
    number: null
})

async function printInvoice(type = null) {
    await axios.get(`/PDV/printInvoice/${props.idSession}/${type != null ? type:invoice.number}`)
    .then((response) => {
        if(response.data.message) return emits('message',response.data.type,response.data.message)
        emits("invoice",response.data);
        printOrder.value = false
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{

    });
}

const print = (()=>{
    if(props.user.config.length <= 0 || props.user.config.print === "0") return emits('message','warn','Acesso negado')
    printOrder.value = true
})
</script>

<style lang="scss" scoped>
@import '../../../assets/Pos/css/Eventos';
</style>
