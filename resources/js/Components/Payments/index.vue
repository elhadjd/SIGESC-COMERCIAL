<template>
   <div class="Principal">
      <div class="Modal">
         <div class="Header">
            <h2>Pagamento de Compra</h2>
         </div>
         <div class="Container">
            <div class="MethodosPagamento">
               <div class="MethodosPagamento-titulos">
                  <span>Metódos</span>
                  <span>Fornecedor</span>
               </div>
               <div class="MethodosPagamento-container">
                    <input @click="ShowMethodsPayment" type="text" v-model="methods.choose.name"/>
                    <div v-if="methods.state" class="pagamento">
                        <span @click="SelectMethodPayment(method)" v-for="method in methods.data" :key="method.id">
                            {{method.name}}
                        </span>
                    </div>
                    <input disabled type="text" placeholder="fornecedor" :value="form.order.supplier?.name">
               </div>
            </div>
            <div class="ValorPagamento">
               <div class="ValorPagamento-titulos">
                  <span>Montante</span>
                  <span>Data de Pagamento</span>
               </div>
               <div class="ValorPagamento-container">
                  <input 
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
            <button @click="$emit('fechar')" class="cancelar">Fechar</button>
            <button @click="savePayment">Salvar</button>
         </div>
      </div>
   </div>
</template>

<script setup>
import axios from 'axios';
import moment from 'moment'
import {onMounted, reactive, ref} from 'vue';

const props = defineProps({
    paymentOrder:Object
})

const emits = defineEmits(['message','encomenda','fechar'])

const form = ref(props.paymentOrder);

const methods = reactive({
    choose:[],
    state:false,
    data:[],
});

console.log(form.value);

const ValorAPagr = ref()

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
    return moment().format('DD/MM/YYYY H:mm:ss')
 })

onMounted(async ()=>{
    getMethods();
    getPurchase();
})

const ShowMethodsPayment = () => {
    methods.state = true
}

const SelectMethodPayment = (event) => {
    methods.choose = event
    methods.state = false
}

const getMethods = async () => {
     await axios.get('/getMethods').then((response) => {
        methods.data = response.data
    }).catch((err) => {
        console.log(err)
    });
}

const getPurchase = async () => {
    await axios.get(`getPurchases/${form.value.order.id}`).then((Response) => {
        numberStr.value = format(Response.data.restPayable)
        number.value = Response.data.restPayable
    }).catch((err) => {
        console.log(err);
    });
}

const savePayment = (()=>{
    if (methods.choose != []) {
        if (number.value != null || number.value != '') {
            methods.choose.valor = number.value
            axios.post(`savePayment/${form.value.order.id}`,methods.choose)
            .then((response) => {
                emits('message',response.data.message,response.data.type)
                emits('encomenda',response.data)
                emits('fechar')
            }).catch((err) => {
                console.log(err);
            });
        } else {
            emits('message','Por favor enforma o valor a pagar','info')
        }
    } else {
        emits('message','Por favor selectiona metodo de pagamento','info')
    }

})
</script>

<style lang="scss" scoped>
@import "../../../assets/Pagamento/scss/index";
</style>