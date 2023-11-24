<template>
<Toast/>
  <div style="height: 100%">
    <confirm :SmsConfirm="modalConfirm.message" @Confirme="CancelSubmit" @descartou="modalConfirm.state = null" v-if="modalConfirm.state == 'open'"/>
    <div v-if="statePayment">
      <FormPagamento @close="statePayment = false" @message="message" @invoice="ShowInvoice" :order="Invoice" :general="general"/>
    </div>
    <div style="height: 100%">
        <Header
            :dados="props.dados"
            @titulo="titulo"
            style="height: 6.5%" />
        <div class="TabelaBaixo">
            <analis-orders
                v-if="form_faturacao === 'Paineis'"
                :prefix="'analis'"/>
            <Pagamento
                @message="message"
                v-if="form_faturacao === 'Pagamentos'"/>
            <Produtos
                @message="message"
                v-if="form_faturacao === 'Produtos'" />
            <credits
                v-if="form_faturacao === 'Notas de credito'"
                @fatura="ShowInvoice"/>
            <NewOrder
                :order="Invoice"
                :general="general"
                @message="message"
                v-if="form_faturacao === 'NovaFatura'"
                @CancelarFatura="titulo('Ordens')"/>
            <ListInvoices
                @message="message"
                @fatura="ShowInvoice"
                v-if="form_faturacao === 'Ordens'" />
            <clients
                @message="message"
                @newClient="newClient"
                v-if="form_faturacao === 'Clientes'"/>
            <NewClient
                @message="message"
                @close="form_faturacao = 'Clientes'"
                :dataClient="dataClient"
                v-if="form_faturacao === 'newClient'"/>
            <suppliers
                @message="message"
                v-if="form_faturacao === 'Fornecedores'"/>
            <invoice
                v-if="print" @CloseModal="print = false"
                :general="general" :order="Invoice"/>
        </div>
    </div>
    
  </div>
</template>

<script setup>
import Toast from 'primevue/toast'
import clients from '../clients/index.vue'
import NewClient from '@/Components/clients/NovoCliente.vue'
import confirm from '@/Components/confirmation/index.vue'
import Header from "./header.vue";
import { ref, onMounted } from "vue";
import NewOrder from "@/Layouts/OrdersForm/index.vue";
import ListInvoices from "./listInvoices.vue";
import credits from '@/Components/Invoice/credits.vue'
import Produtos from "@/components/products/Products.vue";
import Pagamento from "./Payments/index.vue";
import FormPagamento from "@/Layouts/OrdersForm/payments/index.vue";
import suppliers from '@/components/suppliers/index.vue'
import { useToast } from 'primevue/usetoast';
import {form} from '@/composable/formOrders/facturation'
import analisOrders from '@/Components/charts/index.vue'
import invoice from '@/Layouts/OrdersForm/invoice/index.vue'

const toast = useToast()
const emits = defineEmits(['message','stateFormOrder']);
const props = defineProps({ dados: Object });
const print = ref(false)
const loading = ref(null)
const dataClient = ref([])
const modalConfirm = ref({
    state: null,
    message: null,
})
const form_faturacao = ref("Paineis");
const statePayment = ref(false)
const Invoice = ref([])
const relations = ref({
    relation: [],
    placeholder: 'Seleciona um cliente ',
    data:[]
})
const ShowInvoice = ((fatura)=>{
    Invoice.value = fatura
    relations.value.relation = Invoice.value.client
    Invoice.value.relations = relations.value
    form_faturacao.value = 'NovaFatura'
})

onMounted(async ()=>{
    await getRelations();
})

const newClient = ((event)=>{
    form_faturacao.value = 'newClient'
    dataClient.value = event
})

async function getRelations() {
    await axios.get(`${general.routes.getRelations.name}`)
    .then((response) => {
        relations.value.data = response.data
    }).catch((err) => {
        message('Aconteceu um erro no sistema por favor tenta novamente !!!','error')
    });
}
const {general,CancelTransfer,addPayment,message,Close,printOrder,CancelSubmit} = form(print,Invoice,statePayment,loading,form_faturacao,modalConfirm)

const titulo = (event) => {
  form_faturacao.value = event;
};

</script>

<style>
.TableFaturas {
  width: 100vw;
}
.Filtrar {
  width: 15%;
}
.Pesquisa {
  width: 85%;
}
.TabelaBaixo {
  height: 93%;
  overflow-y: auto;
}
.menssagens {
  position: absolute;
  right: 0;
  max-width: 50%;
}
</style>
