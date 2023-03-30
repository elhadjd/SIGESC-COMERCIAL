<template>
<div>
    <Headers @modulos="modulos" :menus="menus"/>
</div>
</template>

<script setup>
import Headers from '../../layouts/header.vue'
import { useStore } from "vuex";
import { onMounted, reactive, ref } from '@vue/runtime-core';
const Mostrar = ref(null)

const emits = defineEmits(['titulo'])

const MostrarDrop = ref(null)

const MostrarTitls = (event)=>{
    if (event == Mostrar.value) {
        Mostrar.value = null
    } else {
        Mostrar.value = event
    }
}
const menus = ref([
    {"menu": "Ordens"},
    {"menu":"Clientes",
        "subMenu": [
            {"name":"Ordens"},
            {"name":"Notas de credito"},
            {"name":"Pagamentos"},
            {"name":"Clientes"},
            {"name":"Produtos"}
        ]
    },
])

const DropShow = ((evento)=>{
    if (evento == MostrarDrop.value) {
        MostrarDrop.value = null
    } else {
        MostrarDrop.value = evento
    }
})

const modulos = ((event)=>{
    emits('titulo',event)
    Mostrar.value = null
})

const props = defineProps({dados:Object})

const MostrarTitl = (event)=>{
    if (Mostrar.value != null) {
        Mostrar.value = event
    }
}

</script>

<style scoped>
    @import url("../../../assets/faturacao/css/header.css");
</style>
