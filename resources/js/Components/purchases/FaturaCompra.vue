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
                        <img :src="`/login/image/${empresa.imagem}`">
                    </div>
                    <div class="NomeEmpresa">
                        <span>{{empresa.nome_de_empresa}}</span>
                        <div>
                            {{FormatDate(Encomenda.encomenda.created_at)}}
                        </div>
                    </div>
                </div>
                <div class="ContainerTwo">
                    <div class="InfoClienteEmpresa">
                        <div class="InfoEmpresa">
                            <div class="Titulo">
                                <span class="span">ENDEREÇO</span>
                                <span class="span">NIF</span>
                                <span class="span">TELEFONE</span>
                            </div>
                            <div class="Content">
                                <span class="span">
                                    {{empresa.cidade == "" ? 'Sem endereço' : empresa.cidade}}
                                </span>
                                <span class="span">{{empresa.nif_empresa}}</span>
                                <span class="span">{{empresa.phone}}</span>
                            </div>
                        </div>
                        <div class="">
                            <div class="NomeCliente">
                                {{Encomenda.encomenda.empresa}}
                            </div>
                            <div class="InfoCliente">
                                <div class="Titulo">
                                    <span>ENDEREÇO</span>
                                    <span>TELEFONE</span>
                                </div>
                                <div class="Content">
                                    <span>{{Encomenda.encomenda.cidade}}</span>
                                    <span>{{Encomenda.encomenda.telefone}}</span>
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
                            <div v-for="item in Encomenda.lista" class="Items" :key="item.id">
                                <div class="item">{{item.nome}}</div>
                                <div class="item">{{item.quantidade+'Un(s)'}}</div>
                                <div class="item">{{format.format(item.preco_final)}}</div>
                                <div class="item">{{item.desconto+'%'}}</div>
                                <div class="item">{{item.iva+'%'}}</div>
                                <div class="item">{{format.format(item.gasto)}}</div>
                                <div class="item">{{format.format(item.total)}}</div>
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
                                            <span>{{format.format(TotalMercadoria())}}</span>
                                            <span>{{format.format(props.encomenda.total_desconto)}}</span>
                                            <span>{{format.format(props.encomenda.total_iva)}}</span>
                                            <span>{{format.format(props.encomenda.total_gastos)}}</span>
                                        </div>
                                    </div>
                                    <div class="TotalMercadoria">
                                        <div>
                                            <strong>TOTAL FATURA</strong>
                                        </div>
                                        <div>
                                            <span>{{format.format(props.encomenda.total)}}</span>
                                        </div>
                                    </div>
                                    <div class="Pagamentos">
                                        <div class="payment" v-for="Pagamento in Pagamentos" :key="Pagamento.id">
                                            <span>{{FormatDate(Pagamento.data)}}</span>
                                            <span>{{Pagamento.nome}}</span>
                                            <span>{{format.format(Pagamento.valor_pago)}}</span>
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
    encomenda: Object
})


const TotalMercadoria = (()=>{
    return Number(props.encomenda.total) + Number(props.encomenda.total_desconto)
- Number(props.encomenda.total_iva)
- Number(props.encomenda.total_gastos)
})

const Encomenda = ref({
    lista: [],
    encomenda: []
})
const store = useStore()

const Pagamentos = ref([])

const TotalDivida = ref([])

const format = Intl.NumberFormat('PT-AO',{
    style: 'currency',
    currency: 'AOA'
})

 const FormatDate = ((data) =>{
    return moment(data).format('DD-MM-YYYY H:mm:ss')
 })

const empresa = ref(store.state.Empresa);

onMounted(()=>{
    Select();
})

const Select = (()=>{
    axios.get(`Encomenda/${props.encomenda.id}`)
    .then((Response) => {
        Encomenda.value = Response.data
    }).catch((err) => {
        console.log(err);
    });
    axios.get(`/BuscarPagamentos/${props.encomenda.id}`)
    .then((Response) => {
        let pagamento = 0
        Response.data.forEach(Item => {
            pagamento += Number(Item.valor_pago)
        });
        Pagamentos.value = Response.data
        TotalDivida.value = props.encomenda.total - pagamento ;
    }).catch((err) => {
        console.log(err);
    });
})

const exportToPDF = (()=> {
  const item = document.getElementById("Container");
  var opt = {
    margin: 0,
    filename: Encomenda.value.encomenda.empresa,
    html2canvas: { scale: 2},
  };

  html2pdf().set(opt).from(item).save();
})
</script>

<style scoped>

@import url('../../../assets/Compra/css/fatura.css');
</style>
