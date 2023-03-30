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
                        <img :src="`/login/image/${empresa.imagem}`">
                    </div>
                    <div class="NomeEmpresa">
                        <span>{{empresa.nome_de_empresa}}</span>
                        <div>
                            {{FormatDate(Encomenda.Invoice.created_at)}}
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
                                    {{empresa.sede == "" ?'Sem endereço' : empresa.sede  }}
                                </span>
                                <span>{{empresa.nif_empresa}}</span>
                                <span>{{empresa.phone}}</span>
                            </div>
                        </div>
                        <div class="">
                            <div class="NomeCliente">
                                {{Encomenda.Invoice.apelido}}
                            </div>
                            <div class="InfoCliente">
                                <div class="Titulo">
                                    <span>ENDEREÇO</span>
                                    <span>TELEFONE</span>
                                </div>
                                <div class="Content">
                                    <span>{{Encomenda.Invoice.cidade}}</span>
                                    <span>{{Encomenda.Invoice.telefone}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="termCondition">
                        <section class="documentNumber">
                            <strong>Numero de documento: </strong>
                            <span>{{Encomenda.Invoice.id}}</span>
                        </section>
                        <section class="vendedore">
                            <strong>Vendedore: </strong>
                            <span>{{Encomenda.Invoice.nome_completo}}</span>
                        </section>
                        <section class="term">
                            <div>
                                <strong>Data de Encomenda: </strong>
                                <span>{{props.encomenda.DataEncomenda != null ? FormatDate(props.encomenda.DataVencimento) : FormatDate(props.encomenda.created_at)}}</span>
                            </div>
                            <div>
                                <strong>Data de vencimento: </strong>
                                <span>{{props.encomenda.DataVencimento != null ? FormatDate(props.encomenda.DataVencimento) : 'Não definido'}}</span>
                            </div>
                        </section>
                    </div>
                    <div class="ContainerFatura">
                        <div class="HeaderInvoice">
                            <span class="title Nome">Artigo</span>
                            <span class="title">Quantidade</span>
                            <span class="title">Preço</span>
                            <span class="title">Desconto</span>
                            <span class="title">Iva</span>
                            <span class="title">Total</span>
                        </div>
                        <div class="ContainerInvoice">
                            <div v-for="item in Encomenda.ItemInvoice" class="Items" :key="item.id">
                                <div class="Nome item">{{item.nome}}</div>
                                <div class="item">{{item.quantidade+'Un(s)'}}</div>
                                <div class="item">{{format.format(item.PrecoVenda)}}</div>
                                <div class="item">{{item.Desconto+'%'}}</div>
                                <div class="item">{{0+'%'}}</div>
                                <div class="item">{{format.format(item.TotalVenda)}}</div>
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
                                            <span>{{format.format(TotalMercadoria)}}</span>
                                            <span>{{format.format(props.encomenda.desconto)}}</span>
                                            <span>{{format.format(0)}}</span>
                                        </div>
                                    </div>
                                    <div class="TotalMercadoria">
                                        <div>
                                            <strong>TOTAL FATURA</strong>
                                        </div>
                                        <div>
                                            <span>{{format.format(props.encomenda.TotalFatura)}}</span>
                                        </div>
                                    </div>
                                    <div class="Pagamentos">
                                        <div class="payment" v-for="Pagamento in Pagamentos" :key="Pagamento.id">
                                            <span>{{FormatDate(Pagamento.data)}}</span>
                                            <span>{{Pagamento.nome}}</span>
                                            <span>{{format.format(Pagamento.ValorPago)}}</span>
                                        </div>
                                    </div>
                                    <div class="TotalMercadoria">
                                        <div>
                                            <strong>TOTAL EM DIVIDA</strong>
                                        </div>
                                        <div>
                                            <span>{{format.format(TotalDivida)}}</span>
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
    encomenda: Object
})


const TotalMercadoria = ref(Number(props.encomenda.TotalFatura) + Number(props.encomenda.desconto))

const Encomenda = ref({
    ItemInvoice: [],
    Invoice: []
})
const store = useStore()

const Pagamentos = ref([])

const TotalDivida = ref([])

const format = Intl.NumberFormat('PT-AO',{
    style: 'currency',
    currency: 'AOA'
})

 const FormatDate = ((data) =>{
    return moment(data).format('DD-MM-YYYY')
 })

const empresa = ref(store.state.Empresa);

onMounted(()=>{
    Select();
})

const Select = (()=>{
    axios.get(`BuscarEncomendaFaturacao/${props.encomenda.id}`)
    .then((Response) => {
        Encomenda.value = Response.data
    }).catch((err) => {
        console.log(err);
    });
    axios.get(`/PagamentosFatura/${props.encomenda.id}`)
    .then((Response) => {
        let pagamento = 0
        Response.data.forEach(Item => {
            pagamento += Number(Item.ValorPago)
        });
        Pagamentos.value = Response.data
        TotalDivida.value = props.encomenda.TotalFatura - pagamento ;
    }).catch((err) => {
        console.log(err);
    });
})

const exportToPDF = (()=> {
  const item = document.getElementById("Container");
  var opt = {
    margin: 0,
    filename: Encomenda.value.Invoice.apelido,
    html2canvas: { scale: 2},
  };

  html2pdf().set(opt).from(item).save();
})
</script>

<style scoped>
@import url('../../../../assets/Compra/css/fatura.css');
</style>
