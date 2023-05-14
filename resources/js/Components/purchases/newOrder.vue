<template>
  <orders :general="general" :order="Purchase"/>
</template>

<script setup>
import orders from '@/Layouts/OrdersForm/index.vue'
import {form} from '@/composable/formOrders/purchase'
import { onMounted, ref } from 'vue'

const emits = defineEmits(['fechar']);
const props = defineProps({
    Purchase:Object
})

const modalConfirm = ref({
    state: null,
    message: null,
})

const order = ref(props.Purchase)
const print = ref(false)
const loading = ref(null)
const statePayment = ref(false)

const relations = ref({
    relation: [],
    placeholder: 'Seleciona um cliente ',
    data:[]
})


onMounted(async ()=>{
    relations.value.relation = order.value.supplier
    await getRelations();
})

async function getRelations() {
    await axios.get(`${general.routes.getRelations.name}`)
    .then((response) => {
        relations.value.data = response.data
        order.value.relations = relations.value
    }).catch((err) => {
        message('Aconteceu um erro no sistema por favor tenta novamente !!!','error')
    });
}
const {general,CancelTransfer,addPayment,message,Close,printOrder,CancelSubmit} = form(emits,print,order,statePayment,loading,modalConfirm)

</script>

<style lang="scss" scoped>

</style>