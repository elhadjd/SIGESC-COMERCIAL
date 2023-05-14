<template>
   <div class="Principal">
      <div class="Modal">
         <div class="Header">
            <h2>Pagamento</h2>
         </div>
         <form @submit.prevent="ConfirmPayment">
            <div class="Container">
               <div class="MethodosPagamento">
                  <div class="MethodosPagamento-titulos">
                     <span>Metódos</span>
                     <span>Fornecedor</span>
                  </div>
                  <div class="MethodosPagamento-container">
                     <input @click="methodPayment.state = !methodPayment.state" type="text" v-model="methodPayment.title"/>
                     <div v-if="methodPayment.state" class="pagamento">
                        <span @click="Methods(method)" v-for="method in methods" :key="method.id">
                        {{method.name}}
                        </span>
                     </div>
                     <input disabled type="text" placeholder="digita o cliente" :value="invoice.client?.surname">
                  </div>
               </div>
               <div class="ValorPagamento">
                  <div class="ValorPagamento-titulos">
                     <span>Montante</span>
                     <span>Data de Pagamento</span>
                  </div>
                  <div class="ValorPagamento-container">
                     <input placeholder="digita o metódo"
                     :type="type"
                     :value="numberStr"
                     @input="onInput"
                     @blur="onBlur"
                     @focus="onFocus">
                     <input type="text" :value="FormatDate()">
                  </div>
               </div>
            </div>
            <div class="Footer">
               <button @click="$emit('close')" type="button" class="cancelar">Fechar</button>
               <button type="submit">Salvar</button>
            </div>
         </form>
      </div>
   </div>
</template>

<script setup>
import  Dialog  from "primevue/dialog";
import { onMounted, reactive, ref } from 'vue'
import {useStore, mapState} from 'vuex'
import moment from 'moment'
import axios from 'axios';

const props = defineProps({
    order: Object,
    general: Object
})

const methodPayment = reactive({
    title: 'metodos de pagamentos',
    state: false
})

const emit = defineEmits(['close','message','invoice'])

const invoice = ref([]);

const methods = ref([]);

const form = {
    payment_method_id:null,
    Amount:0,
    TotalPayments: 0
}

const number = ref(0);

const type = ref('text');

const format = (n)=>{
    return new Intl.NumberFormat('PT-AO', {
    style: 'currency',
    currency: 'AOA',
  }).format(n);
}

const numberStr = ref(format(number.value));

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
    }
};

 const FormatDate = (() =>{
    return moment().format('DD-MM-YYYY H:mm:ss')
 })

onMounted(async ()=>{
    await getInvoice();
    await getMethods();
})

const getInvoice = async () => {
    axios.get(`${props.general.routes.amountToPay.name}/${props.order.id}`)
    .then((response) => {
        invoice.value = response.data
        numberStr.value = invoice.value.RestPayable
        number.value = invoice.value.RestPayable
    }).catch((err) => {
        console.log(err);
    })
}
const getMethods = async () => {
     axios.get(`/getMethods`)
    .then((response) => {
        methods.value = response.data
    }).catch((err) => {
        console.log(err);
    })
}

const ConfirmPayment = (() => {
form.Amount = number.value
axios.post(`sendPayment/${props.order.id}`, {...form})
    .then((response) => {
        emit('message',response.data.message,response.data.type)
        emit('invoice',response.data.data)
        emit('close')
    }).catch((err) => {
        console.log(err);
    })
})

const Methods = (event) => {
    methodPayment.state = false
    methodPayment.title = event.name
    form.payment_method_id = event.id
}
</script>

<style scoped lang="scss">
@import "../../../../assets/Pagamento/scss/index";
</style>
