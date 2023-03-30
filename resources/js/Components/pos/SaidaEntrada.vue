<template>
<Transition>
<div class="principal">
    <div class="Modal">
        <div class="Header">
            <div class="Botoes">
                <button v-for="item in typeOperation" :key="item.id" :class="TipoButton.operation_caixa_type_id == item.id ? 'style' : ''" @click="TipoOperacao(item)">{{item.name}}</button>
            </div>
            <div class="Input">
                <input type="text" id="inputRef" ref="inputRef" v-model="TipoButton.amount" placeholder="Digite valor">
            </div>
        </div>
        <div class="Container">
            <textarea v-model="TipoButton.subject" cols="30" rows="10"></textarea>
        </div>
        <div class="Footer">
            <button @click="$emit('fechar')" class="Fechar">Fechar</button>
            <button @click="Save" class="Guardar">Guardar</button>
        </div>
    </div>
</div>
</Transition>
</template>

<script setup>
import { onMounted,ref } from '@vue/runtime-core'
import axios from 'axios'
import { useCurrencyInput } from 'vue-currency-input'
const FormatarDineiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})

const emits = defineEmits(['message','fechar'])
const session = ref(localStorage.getItem('session'));
const { inputRef } = useCurrencyInput({currency: 'AOA' })
const typeOperation = ref([])
const TipoButton = ref({
    user_id: null,
    session_id: session.value,
    operation_caixa_type_id: null,
    amount: null,
    subject: null
})

const TipoOperacao = ((event)=>{
    TipoButton.value.operation_caixa_type_id = event.id
    inputRef.value.focus
})

onMounted(()=>{
    axios.get('/PDV/getTypeOperation')
    .then((response) => {
        typeOperation.value = response.data
    }).catch((err) => {
        console.log(err);
    });
})

const Save = (()=>{
    if ((TipoButton.value.operation_caixa_type_id == null)||(TipoButton.value.amount == null)||(TipoButton.value.subject == null)||(TipoButton.value.amount == '')||(TipoButton.value.subject == '')) {
        emits('message','info','Atenção os campos não podem ficar vazios')
    } else{
        axios.post('/PDV/addOperation',{data:TipoButton.value})
        .then((Response) => {
            emits('message',Response.data.type,Response.data.message)
            emits('fechar')
        }).catch((err) => {
            console.log(err);
        });
    }
})


</script>

<style lang="scss" scoped>
    @import '../../../assets/Pos/css/EntradaSaida';
</style>
