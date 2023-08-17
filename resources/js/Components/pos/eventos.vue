<template>
    <div class="FormPagamentoCima">
        <div>
            <Cliente @cliente="client"/>
            <span @click="$emit('showProds')">Add Prod <i class="fa fa-plus"></i></span>
        </div>
        
        <div class="QtdPrcImpr">
            <div @click="print">Imprimir</div>
            <div @click="$emit('tipo','preco')">Preço</div>
            <div >Desconto</div>
            <div @click="$emit('tipo','quantidade')">Quantidade</div>
        </div>
        <div v-if="printOrder" class="print">
            <div class="Modal">
                <div class="Header">
                    <h2>Reimprimir a fatura</h2>
                </div>
                <div class="Container">
                    <label for="number">Numero de fatura:</label>
                    <input type="text" v-model="invoice.number" id="number" placeholder="Numero da fatura ">
                </div>
                <div class="Footer">
                    <button class="Descartar" @click="printInvoice('last')">Ultima fatura</button>
                    <button @click="printInvoice()">Imprimir</button>
                </div>
            </div>
        </div>
    </div>
    <div class="FormPagamentoBaixo">
        <div class="PagamentoEsquerda" @click="$emit('payment',true)">
            <i class="fa fa-credit-card-alt"></i>
            <div>Pagamento</div>
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
