<template>
  <div class="Principal">
    <div class="Header">
        <div class="Header-left">
            <div>
                <h1>Lista das compras</h1>
            </div>
            <div>
               <button @click="NewPurchase">Criar Compra</button>
            </div>
        </div>
        <div class="Header-right">
            <span class="p-input-icon-right w-100">
                <i class="pi pi-search" />
                <input type="text" @keyup="SearchInvoice" placeholder="Pesquisar...">
            </span>
        </div>
    </div>
    <div class="Container">
        <orders @message="message"/>
    </div>
  </div>
</template>

<script setup>
import orders from './Orders.vue'
import { onMounted } from "@vue/runtime-core";
import axios from "axios";
import useEventsBus from '@/Eventbus';
const {emit} = useEventsBus();

const emits = defineEmits(['modulos','message'])

const NewPurchase = (()=>{
    axios.post('NewPurchase')
    .then((Response) => {
        emits('modulos','compra', Response.data)
    }).catch((err) => {
        console.log(err);
    });
})

const message = ((message,tipo)=>{
    emits('message',message,tipo)
})

const SearchInvoice = ((event)=>{
    emit('PerquisarEncomenda',event.target.value)
})

</script>

<style lang="scss" scoped>
@import "../../../assets/Compra/css/menu";
</style>
