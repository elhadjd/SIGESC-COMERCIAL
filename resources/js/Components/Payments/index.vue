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
                    <input @click="AbrirMetodoPagamento" type="text" v-model="formulario.metodo.nome"/>
                    <div v-if="metodosPagamentos.estado" class="pagamento">
                        <span @click="SelecionarMetodoPagamento(metodo)" v-for="metodo in metodosPagamentos.lista" :key="metodo.id">
                            {{metodo.nome}}
                        </span>
                    </div>
                    <input disabled type="text" placeholder="digita o cliente" v-model="formulario.fornecedor">
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
            <button @click="$emit('fechar')" class="cancelar">Fechar</button>
            <button @click="SalvarPagamento">Salvar</button>
        </div>
    </div>

  </div>
</template>

<script setup>
import axios from 'axios';
import moment from 'moment'
import {onMounted, reactive, ref} from 'vue';

const formulario = ref(props.FormularioPagamentoCompra);

const metodosPagamentos = reactive({
    estado:false,
    lista:[],
});

const emits = defineEmits(['message','encomenda','fechar'])

const props = defineProps({
    FormularioPagamentoCompra:Object
})

const ValorAPagr = ref()

const format = (n)=>{
    return new Intl.NumberFormat('PT-AO', {
    style: 'currency',
    currency: 'AOA',
  }).format(n);
}

const number = ref(0);

const type = ref('text');

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
    await axios.get('BuscarMethods').then((response) => {
        metodosPagamentos.lista = response.data
    }).catch((err) => {
        console.log(err)
    });

    await axios.post(props.FormularioPagamentoCompra.CalcularTotalAPagar,
    {dados:props.FormularioPagamentoCompra.encomenda})
    .then((Response) => {
        numberStr.value = format(Response.data.ValorAPagr)
        number.value = Response.data.ValorAPagr
    }).catch((err) => {
        console.log(err);
    });

})

const AbrirMetodoPagamento = () => {
    metodosPagamentos.estado = true
}

const SelecionarMetodoPagamento = (event) => {
    formulario.value.metodo  = event
    metodosPagamentos.estado = false
}

const SalvarPagamento = (()=>{
    if (formulario.value.metodo.nome != null || formulario.value.metodo.nome != '') {
        if (formulario.value.valor != null || formulario.value.valor != '') {
            formulario.value.valor = number.value
            formulario.value.data = FormatDate()
            axios.post(`/${formulario.value.caminho}`,formulario.value)
            .then((response) => {
                emits('message',response.data.message,response.data.tipo)
                emits('encomenda',response.data.encomenda)
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
