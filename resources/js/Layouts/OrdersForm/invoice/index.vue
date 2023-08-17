<template>
    <div class="Principal">
        <div class="Modal">
            <div class="Header">
                <h2>Comprovativo de compra</h2>
                <div>
                    <button class="Imprimir" @click="exportToPDF">Imprimir</button>
                    <button class="Fechar" @click="$emit('CloseModal')">X</button>
                </div>
            </div>

            <div id="Container" class="Container">
                <div class="ContainerOne">
                    <div class="LogoEmpresa">
                        <img :src="`/company/image/${company.image}`">
                    </div>
                    <div class="NomeEmpresa">
                        <span>{{company.name}}</span>
                    </div>
                </div>
                <div class="ContainerTwo">
                    <div class="InfoClienteEmpresa">
                        <div class="InfoEmpresa">
                            <div class="Titulo">
                                <span>Endereço:</span>
                                <span>Nif:</span>
                                <span>Telefone:</span>
                            </div>
                            <div class="ContentClient">
                                <span>
                                    {{company.sede == "" ?'Sem endereço' : company.sede  }}
                                </span>
                                <span>{{company.nif}}</span>
                                <span>{{company.phone}}</span>
                            </div>
                        </div>
                        <div class="Client">
                            <div class="InfoCliente">
                                <div class="Titulo">
                                    <span>Cliente:</span>
                                    <span>Endereço:</span>
                                    <span>Telefone:</span>
                                </div>
                                <div class="ContentClient">
                                    <span>{{order.relations?.relation?.name}}</span>
                                    <span>{{order.relations?.relation?.city}}</span>
                                    <span>{{Invoice.client?.phone}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="termCondition">
                        <section class="documentNumber">
                            <strong>Numero de documento: </strong>
                            <span>{{Invoice.orderNumber+Invoice.id}}</span>
                        </section>
                        <section class="vendedore">
                            <strong>Vendedore: </strong>
                            <span>{{Invoice.user?.surname}}</span>
                        </section>
                        <section class="term">
                            <div>
                                <strong>Data de Encomenda: </strong>
                                <span>{{Invoice.DateOrder != null ? formatDate(Invoice.DateDue) : formatDate(Invoice.created_at)}}</span>
                            </div>
                            <div>
                                <strong>Data de vencimento: </strong>
                                <span>{{Invoice.DateDue != null ? formatDate(Invoice.DateDue) : 'Não definido'}}</span>
                            </div>
                        </section>
                    </div>
                    <div class="ContainerFatura">
                        <div class="HeaderInvoice"
                            >
                            <span class="title" :class="title.class == 'nameItem' ? 'Nome':''" v-for="title,idx in general.form.inputs"
                            :key="idx">{{title.label}}</span>
                        </div>
                        <div class="ContainerInvoice">
                            <div v-for="item in Invoice.items" class="Items" :key="item.id">
                                <div class="item" :class="input.product ? 'Nome': ''" v-for="input,idx in general.form.inputs" :key="idx">
                                    {{input.product ? item.product.nome : input.type == 'select' ? item.store?.name
                                    : input.class == 'quantity' ? item[input.name]+'.00Un(s)'
                                    : input.name == 'Discount' || input.name == 'tax' ? item[input.class]+'%' : formatMoney(item[input.name])}}
                                </div>
                            </div>
                            <div class="Totals">
                                <div class="TotalsContainer">
                                    <div class="TotalMercadoria">
                                        <div class="totalMount">
                                            <strong v-for="item,idx in general.form.totalOrder.totals" :key="idx">{{item.title}} : </strong>
                                        </div>
                                        <div class="totalMount">
                                            <span v-for="item,idx in general.form.totalOrder.totals" :key="idx">{{formatMoney(Invoice[item.amount])}} </span>
                                        </div>
                                    </div>
                                    <div class="TotalMercadoria">
                                        <div>
                                            <strong class="TitlTotals">{{general.form.totalOrder.total.title}} : </strong>
                                        </div>
                                        <div>
                                            <span class="Totals">{{formatMoney(Invoice[general.form.totalOrder.total.amount])}}</span>
                                        </div>
                                    </div>
                                    <div v-if="general.form.totalOrder.total.title !='Total da transferencia'" class="Pagamentos">
                                        <div class="payment" v-for="payment in Payments" :key="payment.id">
                                            <span>{{formatDate(payment.created_at)}}</span>
                                            <span>{{payment.method.name}}</span>
                                            <span>{{formatMoney(payment.Amount)}}</span>
                                        </div>
                                    </div>

                                    <div v-if="general.form.totalOrder.total.title !='Total da transferencia'" class="TotalMercadoria">
                                        <div>
                                            <strong>TOTAL EM DIVIDA</strong>
                                        </div>
                                        <div>
                                            <span>{{formatMoney(Invoice[general.form.totalOrder.restPayable])}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="TankYour">
                        <strong></strong>
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
    order: Object,
    general: Object
})


const Invoice = ref([])
const store = useStore()

const Payments = ref([])

const company = ref(store.state.publico.company);

onMounted(async ()=>{
    await Select();
})

const emit = defineEmits(['CloseModal'])

const Select = (async()=>{
    await axios.get(`${props.general.routes.getItems.name}/${props.order.id}`)
        .then((response) => {
            Invoice.value = response.data
        }).catch((err) => {
            console.log(err);
        });
    if ( props.general.form.totalOrder.total.title =='Total da transferencia') return
    await axios.get(`getPayments/${props.order.id}`)
    .then((Response) => {
        Payments.value = Response.data
    }).catch((err) => {
        console.log(err);
    });
})


const exportToPDF = (()=> {
  const item = document.getElementById("Container");
  var opt = {
    margin: 0,
    filename: props.order.relations?.relation?.name,
    html2canvas: { scale: 2},
  };

  html2pdf().set(opt).from(item).save();
})
</script>

<style scoped>
@import './invoice.css';
</style>
