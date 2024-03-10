<template>
    <span>
        <input
            @keyup="(e)=>setClient(e.target.value)"
            type="text"
            name="NomeCliente"
            :placeholder="`${$t('words.name')} ${$t('words.of')} ${$t('words.client')}`"
            class="clientName" />
    </span>
</template>
<script setup>
import Inplace from 'primevue/inplace';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import { ref } from '@vue/runtime-core';
import {OrdersServices} from '@/Components/pos/services/ordersServices'
const {
        setClient,
    } = OrdersServices()
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
