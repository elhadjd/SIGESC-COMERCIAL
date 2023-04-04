<template>
   <div class="Principal">
      <Fatura v-if="StateInvoice" @FecharModal="StateInvoice = false" :order="Invoice.id"/>
         <Pagamento
            @message="message"
            v-if="StatePayment"
            @invoice="UpdateInvoice"
            @close="StatePayment = false"
            :invoice_id="Invoice.id"
         />
      <div class="Header">
         <h1>Fatura</h1>
      </div>
      <div class="Container">
         <div class="Botoes">
            <button @click="ConfirmOrder" v-if="Invoice.state.trim() == 'Cotação'" class="botoes">Confirmar</button>
            <button @click="PayInvoice" v-if="Invoice.state.trim() == 'Publicado'" class="botoes">Adicionar pagamento</button>
            <button @click="PrintInvoice" class="botoes">Imprimir fatura</button>
            <button @click="AnnularInvoice(Invoice.id)" v-if="Invoice.state.trim() == 'Publicado'" class="discartar">Annular fatura</button>
            <button @click="$emit('CancelarFatura'),CancelarFatura" class="botoes">Fechar</button>
         </div>
         <div class="Formulario">
            <div class="Form">
               <div class="HeaderFatura">
                  <div class="NumeroOrdem">
                     <strong>Orden /{{Invoice.orderNumber+Invoice.id}} </strong>
                  </div>
                  <div class="Informacao">
                     <div>
                        <div class="cliente">
                           <div>
                              <input type="text" placeholder="Selecionar Cliente"
                                 @click="SelectCliente" @keyup="PesquisarCliente"
                                 :disabled="Invoice.state.trim() != 'Cotação'"
                                 :value="Invoice.client?.surname"/>
                              <!-- <i class="fa fa-plus"></i> -->
                           </div>
                           <div v-if="MostrarClientes" class="shadow ListCliente">
                              <div class="clienteRows"
                                @click="AddClient(cliente)"
                                v-for="cliente, index in Clients.list"
                                :key="index"
                              >
                                 <div>
                                    {{cliente.surname}}
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="telefone">
                           <input type="text" disabled :value="Invoice.client?.phone != '' ? Invoice.client?.phone : Invoice.client?.whatssap" class="form-control" placeholder="Telefone"/>
                        </div>
                     </div>
                     <div>
                        <Termo :invoice="Invoice"/>
                     </div>
                  </div>
               </div>
               <div class="FooterFatura">
                  <InvoiceItem :Invoice="Invoice" @message="message"/>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import Dropdown from "primevue/dropdown";
import { ref, onMounted,reactive} from "vue";
import { mapState,useStore } from "vuex"
import InvoiceItem from "./InvoiceItem.vue";
import Pagamento from './Payments/FormPagamento.vue'
import Termo from './termo.vue';
import Fatura from './fatura/index.vue';
import useEventsBus from "@/Eventbus";
import axios from "axios";
const props = defineProps({
    Invoice: Object
})

const {emit} = useEventsBus()

const emits = defineEmits(['message'])

const Invoice = ref(props.Invoice)

const selectedCountry = ref();

const loading = ref(true);

const Clients = ref({
    list: [],
    store: []
});

const store = useStore();

const StatePayment = ref(false)

const StateInvoice = ref(false)

const PodeEditar = ref(store.state.PodeEditar)

const FaturaFaturacao = ref(store.state.FaturaFaturacao)

const MostrarClientes = ref(false)

const faturas = ref({
    fatura: null,
    pagamentos: null,
    resto: null
});

const FormularioPagamentoCompra = reactive({
    encomenda: Invoice.value,
    valor:Invoice.value.TotalFatura,
    metodo:[],
    data:null,
    fornecedor:Invoice.value['apelido'],
    caminho: `AddPaymentFatura/${Invoice.value.id}`,
    CalcularTotalAPagar: `CalcularTotalAPagarFaturacao/${Invoice.value.id}`
});

const UpdateInvoice = ((invoice) =>{
    Invoice.value = invoice
})

const AnnularInvoice = ((idInvoice)=>{
    axios.post(`/AnnularInvoice/${idInvoice}`)
    .then((response) => {
        Invoice.value = response.data.Invoice
    }).catch((err) => {
        console.log(err);
    });
})


onMounted(()=> {
    store.state.PodeEditar = null
    axios.get("/clients").then((response) => {
        Clients.value.list = response.data;
        Clients.value.store = response.data;
    });
})

const ConfirmOrder = ()=>{
    if (Invoice.value.client == null) return emits('message','Cliente Obligatorio','info')
    if (Invoice.value.items?.length <= 0) return emits('message','Adiciona produto para poder Confirmar a encomenda','info')
    axios.post(`ConfirmOrder/${Invoice.value.id}`)
    .then((response)=>{
        if (!response.data.message) {
            Invoice.value = response.data
        } else {
            return message(response.data.message, response.data.type)
        }
    })
}

const message = ((message,tipo)=>{
    emits('message',message,tipo)
})

const CancelarFatura = () => {
    store.state.form_faturacao = 'Ordens';
    store.state.OnLoad = false;
    store.state.prodFaturacao = [];
}

const PesquisarCliente = ((event)=>{
    const filter = Clients.value.store.filter((filte)=>{
        return String(filte.apelido).toLowerCase().includes(event.target.value.toLowerCase())
    })
    Clients.value.list = filter
})

const SelectCliente = (()=>{
    MostrarClientes.value = true
})

const AddClient = ((event)=>{
    Invoice.value.client = event
    MostrarClientes.value = false
});

const PrintInvoice = (()=>{
    StateInvoice.value = true
})

const PayInvoice = (()=>{
    StatePayment.value = true
})

</script>

<style scoped lang="scss">
@import '../../../assets/faturacao/css/CriarFaturas.scss';
</style>
