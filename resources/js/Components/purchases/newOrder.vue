<template>
<div>
    <confirm :SmsConfirm="modalConfirm.message" @Confirme="CancelSubmit" @descartou="modalConfirm.state = null" v-if="modalConfirm.state == 'open'"/>
    <invoice v-if="print" @CloseModal="print = false" :general="general" :order="order"/>
    <formPayment @close="statePayment = false" v-if="statePayment" @message="message" @invoice="OnMounted" :order="order" :general="general"/>
    <orders :general="general" :order="Purchase"/>
</div>

</template>

<script setup>
import confirm from '@/Components/confirmation/index.vue'
import formPayment from '@/Layouts/OrdersForm/payments/index.vue'
import invoice from '@/Layouts/OrdersForm/invoice/index.vue'
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


const OnMounted = onMounted(async ()=>{
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
