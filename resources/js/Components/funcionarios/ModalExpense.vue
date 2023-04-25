<template>
   <div class="Principal">
      <div class="Modal">
         <div class="Header">
            Pedido de adianta
         </div>
         <div class="Container">
            <div class="form-Control">
               <label for="type">Tipo</label>
               <input type="text" @click="types.state = true" v-model="types.type" id="type" name="type">
               <div class="drop" v-if="types.state">
                  <span @click="selectType(method)" v-for="method,index in methods" :key="index">
                  {{method.name}}
                  </span>
               </div>
            </div>
            <div v-if="types.type" class="form-Control">
               <label for="valor">{{types.type}}</label>
               <input v-if="!invoices.state" :placeholder="types.placeholders" :type="type" :value="numberStr"
               @input="onInput" @blur="onBlur" @focus="onFocus" id="valor" name="valor">
               <input :placeholder="types.placeholders" v-else type="text" @keyup="(e)=>getInvoice(e.target.value)" id="valor">
               <div v-if="invoices.state" class="drop">
                  <span @click="selectMoney(invoice)" v-for="invoice in invoices.data.slice(0,50)" :key="invoice.id">
                     <div>{{invoice.id}}</div>
                     <div>{{format(invoice.total)}}</div>
                  </span>
               </div>
            </div>
         </div>
         <div class="Footer">
            <button @click="$emit('close')" class="Descartar">Fechar</button>
            <button @click="saveExpense">Confirmar</button>
         </div>
      </div>
   </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";

const emits = defineEmits(['message','close'])

const invoices = ref({
    data:[],
    storeData: [],
    state: false
})

const props = defineProps({
    worker: Object
})

const types = ref({
    worker: props.worker,
    state: false,
    type: null,
    numberInvoice: null,
    placeholders: "",
    value: null
})

const methods = ref([
    {name: "Dinheiro"},
    {name: "Fatura"}
])

const format = (n)=>{
    return new Intl.NumberFormat('PT-AO', {
    style: 'currency',
    currency: 'AOA',
  }).format(n);
}

const number = ref(0);

const type = ref('text');

const numberStr = ref(format(number.value));

const selectType = ((type)=>{
    types.value.type = type.name
    types.value.state = false
    if (type.name != "Fatura") {
        types.value.placeholders = 'Digite valor'
        invoices.value.state = false
        return
    }
    axios.get('/PDV/getOrders')
    .then((response) => {
        invoices.value.data = response.data.data
        invoices.value.storeData = response.data.data
        invoices.value.state = true
    }).catch((err) => {
        emits('message','Aconteceu um erro no sistema por favor tenta novamente','error')
        console.log(err);
    });
    types.value.placeholders = 'Digite o numero da fatura'
})

const saveExpense = (()=>{

    if (types.value.type == null || types.value.value == null) return emits('message','Os campos não podem ficar vazios','info')

    axios.post('saveExpense',types.value)
    .then((response) => {
        if (response.data.message != 'success') return emits('message',response.data.message,response.data.type)
        emits('message',response.data.message,response.data.type)
        emits('close')
    }).catch((err) => {
        emits('message','Aconteceu um erro no sistema por favor tenta novamente','error')
        console.log(err);
    });
})


const onInput = ({ target }) => {
    if (target.value != '' ) {
        number.value = parseInt(target.value);
    }
};
const onFocus = () => {
    if (number.value != '' ) {
        numberStr.value = number.value;
        type.value = 'number';
    }
};
const onBlur = () => {
    if (number.value != '' ) {
        type.value = 'text';
        numberStr.value = format(number.value);
        types.value.value = number.value
    }
};

const selectMoney = ((invoice)=>{
    console.log(invoice.id);
    types.value.numberInvoice = invoice.id
    invoices.value.state = false
    numberStr.value = invoice.total
    types.value.value = invoice.total
})

const getInvoice = ((numero)=>{

    if (types.value.type == 'Fatura') {
        const filter = invoices.value.storeData.filter(invoice=>
            String(invoice.id).includes(numero)
            || String(invoice.total).includes(numero)
        )
        if (filter.length <= 0) {
            return emits('message','Nemhuma fatura encontrada','info')
        }
        invoices.value.data = filter
    }else{
        types.value.numberInvoice = null
    }

})
</script>

<style lang="scss" scoped>
.Principal{
    @include modal;
    .form-Control{
        @include form-control;
        display: grid;
        grid-auto-columns: 50% 50%;
        align-items: center;
        position: relative;
        input{
            width: 100% !important;
        }
        .drop{
            position: absolute;
            display: flex;
            flex-direction: column;
            background-color: white !important;
            z-index: 1;
            margin-top: 70px;
            margin-left: 31.5%;
            width: 65%;
            border: 1px solid #ebebebf9;
            border-top: 0px;
            border-bottom-left-radius: 3px;
            border-bottom-right-radius: 3px;
            max-height: 50px;
            overflow-y: auto;
            @include scroll;
            span{
                padding: 2px 10px 2px 10px;
                font-size: 14px;
                transition: 0.3s;
                display: flex;
                justify-content: space-between;
                &:nth-child(even){
                    background-color: #00000008;
                }
                &:hover{
                    cursor: pointer;
                    background-color: #00000010;
                }
            }
        }
    }
}
</style>
