<template>
   <div class="Principal">
      <div class="Modal">
         <div class="Header">
            <h2>Comprovativo de compra</h2>
            <span>
            <button class="Imprimir" @click="exportToPDF">Imprimir</button>
            <button class="Fechar" @click="$emit('FecharModal')">X</button>
            </span>
         </div>
         <div id="Container" class="Container">
            <div class="ContainerOne">
               <div class="LogoEmpresa">
                  <img :src="`/company/image/${empresa.image}`">
               </div>
               <div class="NomeEmpresa">
                  <span>{{empresa.name}}</span>
                  <div>
                     {{formatDate(invoice.created_at)}}
                  </div>
               </div>
            </div>
            <div class="ContainerTwo">
               <div class="InfoClienteEmpresa">
                  <div class="InfoEmpresa">
                     <div class="Titulo">
                        <span class="span">ENDEREÇO:</span>
                        <span class="span">NIF:</span>
                        <span class="span">TELEFONE:</span>
                     </div>
                     <div class="Content">
                        <span class="span">
                        {{empresa.city == "" ? 'Sem endereço' : empresa.city}}
                        </span>
                        <span class="span">{{empresa.nif}}</span>
                        <span class="span">{{empresa.phone}}</span>
                     </div>
                  </div>
                  <div class="">
                     <div class="NomeCliente">
                        {{invoice.supplier?.name}}
                     </div>
                     <div class="InfoCliente">
                        <div class="Titulo">
                           <span>ENDEREÇO</span>
                           <span>TELEFONE</span>
                        </div>
                        <div class="Content">
                           <span>{{invoice.supplier?.city}}</span>
                           <span>{{invoice.supplier?.phone}}</span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="ContainerFatura">
                  <div class="HeaderInvoice">
                     <span class="title">Artigo</span>
                     <span class="title">Quantidade</span>
                     <span class="title">Preço</span>
                     <span class="title">Desconto</span>
                     <span class="title">Iva</span>
                     <span class="title">Gastos</span>
                     <span class="title">Total</span>
                  </div>
                  <div class="ContainerInvoice">
                     <div v-for="item in invoice.items" class="Items" :key="item.id">
                        <div class="item">{{item.product?.nome}}</div>
                        <div class="item">{{item.quantity+'Un(s)'}}</div>
                        <div class="item">{{formatMoney(item.finalPrice)}}</div>
                        <div class="item">{{item.discount+'%'}}</div>
                        <div class="item">{{item.tax+'%'}}</div>
                        <div class="item">{{formatMoney(item.spent)}}</div>
                        <div class="item">{{formatMoney(item.totalItem)}}</div>
                     </div>
                     <div class="Totals">
                        <div class="TotalsContainer">
                           <div class="TotalMercadoria">
                              <div class="totalMount">
                                 <strong>TOTAL MERCADORIA</strong>
                                 <strong>DESCONTO</strong>
                                 <strong>IVA</strong>
                                 <strong>GASTOS</strong>
                              </div>
                              <div class="totalMount">
                                 <span>{{formatMoney(invoice.totalMerchandise)}}</span>
                                 <span>{{formatMoney(invoice.totalDiscount)}}</span>
                                 <span>{{formatMoney(invoice.totalTax)}}</span>
                                 <span>{{formatMoney(invoice.TotalSpending)}}</span>
                              </div>
                           </div>
                           <div class="TotalMercadoria">
                              <div>
                                 <strong>TOTAL FATURA</strong>
                              </div>
                              <div>
                                 <span>{{formatMoney(invoice.total)}}</span>
                              </div>
                           </div>
                           <div class="Pagamentos">
                              <div class="payment" v-for="payment in invoice.payments" :key="payment.id">
                                 <span>{{formatDate(payment.created_at)}}</span>
                                 <span>{{payment.method?.name}}</span>
                                 <span>{{formatMoney(payment.Amount)}}</span>
                              </div>
                           </div>
                           <div class="TotalMercadoria">
                              <div>
                                 <strong>TOTAL EM DIVIDA</strong>
                              </div>
                              <div>
                                 <span>{{formatMoney(invoice.restPayable)}}</span>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="TankYour">
                  <strong>OBRIGADO POR SER NOSSO QUERIDO CLIENTE , VOLTA SEMPRE</strong>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import html2pdf from "html2pdf.js";
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import { useStore } from "vuex";
import moment from 'moment'

const props = defineProps({
    Order: Object
})
const invoice = ref([])

const store = useStore()

const payments = ref([])

const format = Intl.NumberFormat('PT-AO',{
    style: 'currency',
    currency: 'AOA'
})

const empresa = ref(store.state.publico.company);

onMounted(()=>{
    Select();
})

const Select = (()=>{
    axios.get(`getPurchases/${props.Order.id}`)
    .then((Response) => {
        invoice.value = Response.data
    }).catch((err) => {
        console.log(err);
    });
})

const exportToPDF = (()=> {
  const item = document.getElementById("Container");
  var opt = {
    margin: 0,
    filename: invoice.value.supplier.name+"_"+invoice.value.id,
    html2canvas: { scale: 2},
    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait'}
  };

  html2pdf().set(opt).from(item).save();
})
</script>

<style scoped>
@import url('../../../assets/Compra/css/fatura.css');
</style>
