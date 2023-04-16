<template>
    <div class="Principal">
        <div class="Modal">
            <div class="Header">
                <h2>Comprovativo de compra</h2>
                <div>
                    <button class="Imprimir" @click="exportToPDF">Imprimir</button>
                    <button class="Fechar" @click="$emit('FecharModal')">X</button>
                </div>

            </div>

            <div id="Container" class="Container">
                <div class="ContainerOne">
                    <div class="LogoEmpresa">
                        <img :src="`/login/image/${company.image}`">
                    </div>
                    <div class="NomeEmpresa">
                        <span>{{company.name}}</span>
                        <div>
                            {{formatDate(Invoice.created_at)}}
                        </div>
                    </div>
                </div>
                <div class="ContainerTwo">
                    <div class="InfoClienteEmpresa">
                        <div class="InfoEmpresa">
                            <div class="Titulo">
                                <span>ENDEREÇO</span>
                                <span>NIF</span>
                                <span>TELEFONE</span>
                            </div>
                            <div class="Content">
                                <span>
                                    {{company.sede == "" ?'Sem endereço' : company.sede  }}
                                </span>
                                <span>{{company.nif}}</span>
                                <span>{{company.phone}}</span>
                            </div>
                        </div>
                        <div class="">
                            <div class="NomeCliente">
                                {{Invoice.client?.surname}}
                            </div>
                            <div class="InfoCliente">
                                <div class="Titulo">
                                    <span>ENDEREÇO</span>
                                    <span>TELEFONE</span>
                                </div>
                                <div class="Content">
                                    <span>{{Invoice.client?.city}}</span>
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
                        <div class="HeaderInvoice">
                            <span class="title Nome">Artigo</span>
                            <span class="title">Quantidade</span>
                            <span class="title">Preço</span>
                            <span class="title">Desconto</span>
                            <span class="title">Total</span>
                        </div>
                        <div class="ContainerInvoice">
                            <div v-for="item in Invoice.items" class="Items" :key="item.id">
                                <div class="Nome item">{{item.product.nome}}</div>
                                <div class="item">{{item.quantity+'Un(s)'}}</div>
                                <div class="item">{{formatMoney(item.PriceSold)}}</div>
                                <div class="item">{{item.Discount+'%'}}</div>
                                <div class="item">{{formatMoney(item.TotalSold)}}</div>
                            </div>
                            <div class="Totals">
                                <div class="TotalsContainer">
                                    <div class="TotalMercadoria">
                                        <div class="totalMount">
                                            <strong>TOTAL MERCADORIA</strong>
                                            <strong>DESCONTO</strong>
                                            <strong>IVA</strong>
                                        </div>
                                        <div class="totalMount">
                                            <span>{{formatMoney(Invoice.TotalMerchandise)}}</span>
                                            <span>{{formatMoney(Invoice.discount)}}</span>
                                            <span>{{formatMoney(0)}}</span>
                                        </div>
                                    </div>
                                    <div class="TotalMercadoria">
                                        <div>
                                            <strong>TOTAL FATURA</strong>
                                        </div>
                                        <div>
                                            <span>{{formatMoney(Invoice.TotalInvoice)}}</span>
                                        </div>
                                    </div>
                                    <div class="Pagamentos">
                                        <div class="payment" v-for="payment in Payments" :key="payment.id">
                                            <span>{{formatDate(payment.created_at)}}</span>
                                            <span>{{payment.method.name}}</span>
                                            <span>{{formatMoney(payment.Amount)}}</span>
                                        </div>
                                    </div>

                                    <div class="TotalMercadoria">
                                        <div>
                                            <strong>TOTAL EM DIVIDA</strong>
                                        </div>
                                        <div>
                                            <span>{{formatMoney(Invoice.RestPayable)}}</span>
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
    order: Object
})


const Invoice = ref([])
const store = useStore()

const Payments = ref([])

const company = ref(store.state.Empresa);

onMounted(()=>{
    Select();
})

const Select = (()=>{
    axios.get(`getItems/${props.order}`)
    .then((Response) => {
        Invoice.value = Response.data
    }).catch((err) => {
        console.log(err);
    });
    axios.get(`/getPayments/${props.order}`)
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
    filename: Invoice.value.client?.surname,
    html2canvas: { scale: 2},
  };

  html2pdf().set(opt).from(item).save();
})
</script>

<style scoped>
@import url('../../../../assets/Compra/css/fatura.css');
</style>
