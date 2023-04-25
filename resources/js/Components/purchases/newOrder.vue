<template>
   <Progress v-if="process"/>
   <payments
    :paymentOrder="payment"
    @fechar="payment.state = false"
    @encomenda="order" @message="message"
    v-if="payment.state"
   />
   <invoice v-if="StateFatura"
     @FecharModal="StateFatura = false"
     :Order="order"
    />
   <div class="Principal">
      <div class="Header">
         <h1>Compras</h1>
      </div>
      <div class="Container">
         <div class="Botoes">
            <button v-if="order.state == 'Cotação'" @click="ConfirmOrder" class="botoes">Confirmar</button>
            <button @click="$emit('fechar')" class="botoes">Fechar</button>
            <button @click="payment.state = true" v-if="order.state != 'Pago'" class="botoes">Adicionar pagamento</button>
            <button @click="Imprimir" v-if="order.state != 'Cotação'" class="botoes">Imprimir fatura</button>
         </div>
         <div class="Formulario">
            <div class="Form">
               <div class="HeaderFatura">
                  <div class="NumeroOrdem">
                     <strong>Compra/00{{order.id}}</strong>
                  </div>
                  <div class="Informacao">
                     <div>
                        <div class="cliente">
                           <div>
                              <input :disabled="order.state !='Cotação'"
                                 @click="suppliers.state = ! suppliers.state"
                                 type="text" placeholder="Selecionar Fornecedor"
                                 @keyup="PesquisarFornecedor"
                                 :value="order.supplier?.name"/>
                           </div>
                           <div v-if="suppliers.state" class="shadow ListCliente">
                              <div class="clienteRows" v-for="supplier in suppliers.list" :key="supplier.id"
                                 @click="AddSupplierOrder(supplier)">
                                 <div>{{supplier.name}}</div>
                                 <div></div>
                              </div>
                           </div>
                           <div class="form-Control">
                              <label for="armagen"> seleciona armagen: </label>
                              <button type="button" id="armagen" @click="armagens.state = !armagens.state">{{order.armagen?.name}}</button>
                              <div v-if="armagens.state" class="drop">
                                 <span
                                    v-for="armagen in armagens.list" :key="armagen.id"
                                    @click="armagenAdd(armagen)"
                                    >
                                 {{armagen.name}}
                                 </span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div>
                        <DataEncomenda :Order="order"/>
                     </div>
                  </div>
               </div>
               <div class="FooterFatura">
                  <items
                     :id_order="Purchase.id"
                     @message="message"
                     @progress="process = !process"
                     />
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import items from './orderItems.vue'
import DataEncomenda from './DataEncomenda.vue'
import { computed, onMounted,reactive,ref, watch } from '@vue/runtime-core'
import Progress from '../confirmation/progress.vue'
import axios from 'axios'
import payments from '@/Components/Payments/index.vue'
import invoice from '@/Components/purchases/Invoice.vue'
import { useStore } from 'vuex'
import useEventsBus from '@/Eventbus'
const {bus} = useEventsBus();

const store = useStore()
const emits = defineEmits(['message'])
const process = ref(false)
const StateFatura = ref(false)
const props = defineProps({
    Purchase:Object
})
const order = ref(props.Purchase)

const suppliers = reactive({
    state:false,
    list:[],
    store: []
});

const armagens = ref({
    state: false,
    list: [],
    store: [],
})

const payment = reactive({
    order: order.value,
    state:false
});


onMounted(async ()=>{
    await getPurchase();
    getSuppliers();
    await getArmagens()
})

const getPurchase = async () => {
    await axios.get(`getPurchases/${order.value.id}`)
    .then((Response) => {
        order.value = Response.data
    }).catch((err) => {
        console.log(err);
    });
}

const getArmagens = (async ()=>{
    await axios.get(`/getArmagens`)
    .then((Response) => {
        armagens.value.list = Response.data.armagens
        armagens.value.store = Response.data.armagens
    }).catch((err) => {
        console.log(err);
    });
})

const armagenAdd = (event) => {
  order.value.armagen_id = event.id
  order.value.armagen = event
  armagens.value.state = false
}

const getSuppliers = (()=>{
    axios.get(`/suppliers`)
    .then((Response) => {
        suppliers.list = Response.data
        suppliers.store = Response.data
    }).catch((err) => {
        console.log(err);
    });
})

const ConfirmOrder = (()=>{
    if (!order.value.armagen_id) return emits('message','Seleciona um armagem para continuar','info')
    if (order.value.supplier != []) {
        process.value = true
        axios.post(`confirmOrder/${order.value.id}/${order.value.armagen_id}`,{...order.value})
        .then((Response) => {
            if (!Response.data.message) {
                order.value = Response.data
            } else {
                emits('message',Response.data.message,Response.data.tipo)
            }
            process.value = false
        }).catch((err) => {
            process.value = false
            emits('message','Aconteçeu um erro no sistema por favor atualize seu navigador e tenta novamente','info')
            console.log(err);
        });
    } else {
        emits('message','Fornecedor não pode ficar vazio','info')
    }
})

const Imprimir = (()=>{
    StateFatura.value = true
})

const PesquisarFornecedor = ((event)=>{
    const Filtrar = suppliers.store.filter(object=>{
        return object.name.toLowerCase().includes (event.target.value.toLowerCase(),0)
    })
    if (Filtrar.length != 0) {
        suppliers.list = Filtrar
    } else {
        return message('Nenhum fornecedor encontrado com este nome ','info')
    }

})

const message = ((message,tipo)=>{
    emits('message',message,tipo)
})

const AddSupplierOrder = (event) => {
    order.value.supplier = event
    suppliers.state = false
}
</script>

<style scoped lang="scss">
@import "../../../assets/faturacao/css/CriarFaturas.scss";
.Informacao {
  height: 60% !important;
  .cliente {
    margin-bottom: 10px;
  }
}
.form-Control{
    @include form-control;
    @include dopDown;
    .drop {
        margin-left: 160px;
        width: 54%;
    }
}
</style>
