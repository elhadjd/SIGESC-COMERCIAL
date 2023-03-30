<template>
    <div class="div2">
        <span>
            <input
            v-model="$store.state.ClientePos"
                @keyup="cliente"
                type="search"
                name="NomeCliente"
                placeholder="Pesqise pelo nome do cliente"
                class="NomeCliente" />
        </span>
    </div>
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
    .ResultadoCliente{
        max-height: 170px;
        min-height: 100px;
        overflow: auto;
        margin-top: 10px;
    }
    .p-dialog-header{
        display: none;
    }
    .p-dialog-footer{
        padding-bottom: 10px!important;
    }
    .p-dialog-content{
        padding-left: 0px!important;
        padding-right: 0px!important;
        padding-bottom: 10px!important;
    }
    #Cliente{
        padding-top: 5px;
        padding-bottom: 3px;
        color: #777;
        font: 11pt arial;
        font-weight: 500;
        padding-left: 20px;
    }
    #Cliente:hover{
        cursor: pointer;
        -webkit-box-shadow: 4px 0px 10px 1px rgba(176,176,176,0.75);
        box-shadow: 4px 0px 10px 1px rgba(176,176,176,0.75);
        opacity: 0.9;
    }

    .NomeCliente{
        padding: 3px 10px 3px 10px !important;
        border: none;
        outline: none;
        box-shadow: 0 0 0 0 !important;
        border-radius: 0px !important;
    }
.div2{
    width: 100%;
    height: 50%;
    display: flex;
    span{
        width: 75%;
        input{
            width: 100%;
            border: none;
            outline: none;
            border-radius: 5px;
            border: 1px solid #eee;
            padding-left: 10px;
            padding: 8px;
        }
    }
}
</style>
