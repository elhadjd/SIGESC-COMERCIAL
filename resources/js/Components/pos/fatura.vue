<template>
<div class="Form_fatura">
    <div id="EmpresaEnfo">
        <div>
            <h2 class="">{{company.name}}</h2>
        </div>
        <div class="info-company" v-if="invoice.user.config.infoCompany">
            <div class="content">
                <span>TELEFONE : </span>
                <div class="numero">{{company.phone}}</div>
            </div>
            <div class="content">
                <span>NIF DA EMPRESA : </span>
                <div class="numero">{{company.nif}}</div>
            </div>
        </div>
    </div>

    <div class="Cliente">
        <strong class="title">Cliente</strong>
        <div class="InfoCliente">
            <div>
                <strong>Nome : </strong>
                <span id="NomeCliente">{{invoice.cliente}} </span>
            </div>
            <div>
                <strong>Telefone : </strong>
                <span>{{"+244"}} </span>
            </div>
        </div>
    </div>

    <div>
        <div class="Titls">
            <div class=""><strong>Quantidade</strong></div>
            <div class=""><strong>Preço</strong></div>
            <div class="totalTitl"><strong>Total</strong></div>
        </div>

        <div class="listaItens">
            <div class="item" v-for="item in invoice.items" :key="item.product.id">
                <div>{{item.product.nome}} </div>
                <div class="PrecoQuant">
                    <div class="QuantidadeFatura">{{item.quantity+'Un(s)'}}</div>
                    <div class="PrecoFatura">{{formatMoney(item.price_sold)}}</div>
                    <div class="TotalFatura">{{formatMoney(item.total)}}</div>
                </div>
            </div>
        </div>

        <div id="BaixoFatura">
            <div class=" InfoPag">

                <div class="TiposPag" >
                    <div><strong>Total</strong></div>
                    <div  v-for="payment in invoice.payments" :key="payment.id"><strong id="multicaixaTitl">{{payment.method.name}}</strong></div>
                    <div><strong>Troco</strong></div>
                    <div><strong>Operador</strong></div>
                </div>

                <div class="totais">
                    <div id="TotalFatura">{{formatMoney(invoice.total)}}</div>
                    <div id="transferencia" v-for="payment in invoice.payments" :key="payment.id">{{formatMoney(payment.amountPaid)}}</div>
                    <div id="troco">{{changes()}}</div>
                    <div id="operador">{{invoice.user.surname}}</div>
                </div>
            </div>
        </div>

        <div class="data_fatura">
            <div>
                <span>Data: </span>
                <span>{{formatHourAndTime(invoice.created_at)}}</span>
            </div>
            <div>
                <span>Numero da fatura: </span>
                <span>{{invoice.number}}</span>
            </div>
            <div>
                <span>Total de items: </span>
                <span>{{invoice.items.length}}</span>
            </div>
        </div>
        <div class="Obrigado">OBRIGADO VOLTE SEMPRE !!!</div>
    </div>
</div>

</template>


<script setup>
import { onMounted, ref } from "@vue/runtime-core"
import { useStore } from "vuex"
import moment from 'moment'

const props = defineProps({
    dadosFatura: Object
})
const store = useStore()
const config = ref(store.state.pos.configCash)
const invoice = ref(props.dadosFatura)
const emits = defineEmits(['closePrint'])

const company = ref(store.state.publico.company)
''

const changes = (()=>{
    let amountPaid = 0
    invoice.value.payments.forEach((amount)=>{
        amountPaid += Number(amount.amountPaid)
    })
    return formatMoney(Number(amountPaid) - Number(invoice.value.total))
})


onMounted(async ()=>{
    window.print()
    window.addEventListener('afterprint', emits('closePrint'))
})
</script>

<style scoped lang="scss">
    @import '../../../assets/Pos/css/fatura';
    *{
        padding: 0;
        margin: 0;
        color: black !important;
    }
    .data_fatura {
        width: 100%;
        padding: 2px;
        display: flex;
        justify-content: space-between;
        font: 11pt arial;
        font-weight: bold;
    }
    #EmpresaEnfo{
        width: 100%;
        display: flex;
        flex-direction: column;
        >div{

        }
        >div:nth-child(1){
            display: flex;
            justify-content: center;
            align-items: center;
        }

        h2{
            font: 11pt arial !important;
            font-weight: bold !important;
        }
    }
</style>
