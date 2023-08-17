<template>
    <span>
        <input
        v-model="$store.state.ClientePos"
            @keyup="cliente"
            type="text"
            name="NomeCliente"
            placeholder="Pesqise pelo nome do cliente"
            class="clientName" />
    </span>
</template>
<script setup>
import Inplace from 'primevue/inplace';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import { ref } from '@vue/runtime-core';

const emits = defineEmits(['cliente'])

const cliente = ((event)=>{
    emits('cliente',event.target.value)
})
</script>

<style scoped lang="scss">

span{
    width: 100%;
    input{
        @include input_normal;
        padding: 5px !important;
    }
}
</style>
