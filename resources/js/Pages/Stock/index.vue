<template>
    <div class="mt-0" style="height:100vh;width: 100vw">
        <Toast/>
        <Headers @modulos="modulos" :menus="menus"/>
        <div class="Container">
            <products v-if="modul == 'Artigos'"/>
            <index v-if="modul == 'Stock'"/>
            <catalog-product v-if="modul == 'Catolologo'"/>
            <store @message="message" v-if="modul == 'Armagens'"/>
            <transfers  @message="message" @handleModule="modulos" v-if="modul == 'Transferencias'"/>
            <NewTransfer
                @modulo="modulos"
                :transfer="transfer"
                @message="message"
                v-if="modul == 'transfer'"
            />
        </div>
    </div>
</template>

<script setup>
import Headers from '../../Layouts/header.vue'
import catalogProduct from '@/components/stock/catalogProduct.vue'
import { ref } from '@vue/runtime-core';
import products from '@/components/products/Products.vue'
import index from '@/Components/stock/index.vue'
import store from '@/Components/stock/armagens.vue'
import NewTransfer from '@/components/stock/transfers/index.vue';
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast';
import transfers from '@/Components/stock/transfers/transfers.vue'
const modul = ref("Stock")
const MostrarDrop = ref()
const toast = useToast()

const menus = ref([
    {"menu": "Stock"},
    {"menu":"Relatório",
        "subMenu": [
            {"name":"Catolologo"},
            {"name":"Transferencias"},
        ],
    },
    {"menu": "Armagens"},
    {"menu": "Artigos"},
])

const transfer = ref([])

const modulos = ((event,Transfer=null)=>{
    if (event == "transfer") {
        transfer.value = Transfer
    }
    MostrarDrop.value = null
    modul.value = event
})

const DropShow = ((evento)=>{
    if (evento == MostrarDrop.value) {
        MostrarDrop.value = null
    } else {
        MostrarDrop.value = evento
    }
})

const message = ((message,type)=>{
    toast.add({
        severity: type,
        summary: 'Message',
        detail: message,
        life: 5000,
    })
})
</script>

<style scoped lang="scss">
.Container{
    width: 100%;
    height: 92%;
    box-sizing: border-box;
}
@import '../../../assets/stock/scss/menuStock';
</style>
