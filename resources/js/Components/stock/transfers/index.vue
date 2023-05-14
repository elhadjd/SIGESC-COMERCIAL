<template>
<div>
    <invoice v-if="print" @CloseModal="print = false" :general="general" :order="order"/>
    <itemOrder
        :order="order"
        :general="general"
        @message="message"
        :loading="loading"
        >
    </itemOrder>
</div>

</template>

<script setup>
import invoice from '@/Layouts/OrdersForm/invoice/index.vue'
import itemOrder from "@/Layouts/OrdersForm/index.vue";
import { onMounted, reactive, ref } from "vue";
import {form} from '@/composable/formOrders/transfer'
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import { useStore } from 'vuex';
const props = defineProps({
    transfer: Object,
})
const relations = ref({
    relation: props.transfer.store_to,
    placeholder: 'Selecione armagem de destino',
    data:[]
})
const store = useStore();
const print = ref(false)
const order = ref(props.transfer)
const emits = defineEmits(['modulo']);
const toast = useToast();
const loading = ref(null);

const {general,CancelTransfer,Close,printOrder} = form(emits,store,print,order.value,loading)

onMounted(async ()=>{
    order.value.relations = relations.value
    await getRelations();
})

async function getRelations() {
    await axios.get(`${general.routes.getRelations.name}`)
    .then((response) => {
        order.value.relations.data = response.data.armagens
    }).catch((err) => {
        message('Aconteceu um erro no sistema por favor tenta novamente !!!','error')
    });
}
function message (type,message) {
  toast.add({
    severity: type,
    summary: 'Informação do Sistema',
    detail:message,
    life: 4000
  });
}
</script>

<style lang="scss" scoped>
.test{
    height: 100%;
}
</style>
