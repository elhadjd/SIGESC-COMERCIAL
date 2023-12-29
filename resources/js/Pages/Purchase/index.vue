<template>
<div class="mt-0" style="height:100vh;width: 100vw">
    <Toast/>
    <Headers @modulos="showModule" :menus="menus"/>
    <div class="Containere">
        <products @message="message" v-if="modulo == $t('words.article')" />
        <new-order @fechar="modulo = $t('words.purchase')" @message="message"
         v-if="modulo == 'compra'" :Purchase="Purchase"/>
        <home @message="message" @modulos="showModule" v-if="modulo == $t('words.purchase')"/>
        <suppliers v-if="modulo == $t('words.provider')"/>
        <analis-orders v-if="modulo == $t('words.panel')" :prefix="'analis'"/>
        <payments v-if="modulo == t('words.payment')"/>
    </div>
</div>

</template>

<script setup>
import { onMounted,ref,watch } from '@vue/runtime-core';
import home from '@/Components/purchases/menu.vue'
import products from '@/Components/products/Products.vue'
import newOrder from '@/Components/purchases/newOrder.vue'
import useEventsBus from '@/Eventbus';
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast';
import { useStore } from 'vuex';
import { Inertia } from '@inertiajs/inertia';
import suppliers from '@/Components/suppliers/index.vue'
import Headers from '../../layouts/header.vue'
import payments from '@/components/purchases/Payments.vue'
import analisOrders from '@/Components/charts/index.vue'
import { useI18n } from 'vue-i18n';
const { t,te,tm } = useI18n();
const menus = ref([
    {"menu": t('words.purchase')},
    {"menu": t('words.provider'),
        "subMenu": [
            {"name":t('words.provider')},
            {"name":t('words.payment')}
        ]
    },
    {'menu':t('words.panel')},
    {"menu": t('words.article')},
])
const toast = useToast()
const {bus} = useEventsBus();
const store = useStore()
const modulo = ref(t('words.purchase'));
const MostrarDrop = ref(null)
const Mostrar = ref(null)
const Purchase = ref([])

const MostrarTitls = (event)=>{
    if (event == Mostrar.value) {
        Mostrar.value = null
    } else {
        Mostrar.value = event
    }
}

const MostrarTitl = (event)=>{
    if (Mostrar.value != null) {
        Mostrar.value = event
    }
}
const DropShow = ((evento)=>{
    if (evento == MostrarDrop.value) {
        MostrarDrop.value = null
    } else {
        MostrarDrop.value = evento
    }
})

const Sair = (()=>{
    Inertia.post('/sair');
})

watch(()=>bus.value.get('CompraPedidoFormulario'), (payload) => {
    Purchase.value = payload[0];
    modulo.value = 'compra'
});

const showModule = ((module,compra)=>{
    modulo.value = module
    Mostrar.value = null
    Purchase.value = compra
})

const message = ((message,tipo)=>{
    toast.add({
        severity: tipo,
        summary: 'Message',
        detail: message,
        life:5000
    })
})

</script>

<style lang="scss" scoped>
@import "../../../assets/Compra/css/compra";
</style>
