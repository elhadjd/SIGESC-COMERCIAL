<template>
 <Progress v-if="ShowModal"/>
<div class="user-select-none formulario-pagamento">
    <Toast/>
    <form @submit.prevent="Validar_Pagamento" class="formulario-content">
        <div class="sessao-one">
            <div>
                <span>{{formatMoney(TotalEncomenda)}}</span>
                <FontAwesomeIcon icon="fa-solid fa-money-check-dollar" />
            </div>
        </div>

        <div class="sessao-two">
            <div class="Container">
                <div class="buttons">
                    <div>
                        <div>
                            <button @click="$emit('closePaymentForm')"><i class="fa fa-angle-double-left"></i> Cancelar</button>
                        </div>

                        <div>
                            <h2>Pagamento</h2>
                        </div>

                        <div>
                            <button class="Validar" type="submit" v-if="Validar" :disabled="isSubmitting">Validar <i :class="isSubmitting ? 'fa fa-spinner fa-pulse fa-3x fa-fw' : 'fa fa-angle-double-right'"></i></button>
                        </div>
                    </div>
                </div>

                <div class="container-two">
                    <div class="one">
                        <div class="opcao">
                            <div
                            class="methodo"
                            v-for="method in methodos"
                            :key="method"
                            @click="methods(method.name)"
                            >
                                <strong>{{ method.name + " : " + formatMoney(method.valor) }}</strong>
                                <strong class="ValorEntregue"></strong>
                            </div>
                        </div>
                    </div>

                    <div class="two">
                        <div class="container-thee">
                            <div id="totalEncomendaPagamento">
                                <h1 class="TotalEncomendas" v-if="mostraTotal==true">{{formatMoney(TotalEncomenda)}}</h1>
                                <div class="CalculoTotal" v-else>
                                    <div class="TotalCompra">
                                        <h3 class="Restante">
                                            <strong>Em falta : </strong>
                                            <span >{{formatMoney(RestoPagar)}}</span>
                                        </h3>
                                        <h5 class="div5">
                                            <span>Toatal da compra</span>
                                            <span id="TotalCompra">: {{formatMoney(TotalEncomenda)}}</span>
                                        </h5>
                                    </div>
                                    <div class="troco">
                                        <span >
                                            <strong>Troco : </strong>
                                            <strong id="TrocoCliente">{{formatMoney(Troco)}} </strong>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="numeros">
                                <div class="Numero">
                                    <div v-for="i in 9" :key="i" @click="numer(i)" :numero="i"><strong>{{i}}</strong></div>
                                    <div @click="numer(0)"><strong>0</strong></div>
                                    <div><strong>.</strong></div>
                                    <div id="TiraNumeros" @click="reduzir_pagamento"><strong><i class="fa fa-eraser" aria-hidden="true"></i></strong></div>
                                </div>
                                <div class="Valores">
                                    <div @click="numero(100)">{{formatMoney(100)}}</div>
                                    <div @click="numero(200)">{{formatMoney(200)}}</div>
                                    <div @click="numero(500)">{{formatMoney(500)}}</div>
                                    <div @click="numero(1000)">{{formatMoney(1000)}}</div>
                                    <div @click="numero(2000)">{{formatMoney(2000)}}</div>
                                    <div @click="numero(8000)">{{formatMoney(8000)}}</div>
                                    <div @click="numero(3000)">{{formatMoney(3000)}}</div>
                                    <div @click="numero(4000)">{{formatMoney(4000)}}</div>
                                    <div @click="numero(5000)">{{formatMoney(5000)}}</div>
                                    <div @click="numero(6000)">{{formatMoney(6000)}}</div>
                                    <div @click="numero(7000)">{{formatMoney(7000)}}</div>
                                    <div @click="numero(10000)">{{formatMoney(10000)}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</template>
<script setup>
import { computed, onMounted ,ref} from "@vue/runtime-core";
import { useToast } from "primevue/usetoast";
import { mapMutations, mapState, useStore } from "vuex";
import Progress from '../confirmation/progress.vue'
import Toast from 'primevue/toast'
import { Inertia } from "@inertiajs/inertia";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const props = defineProps({
    method: Object,
    dados: Object
});

const ShowModal = ref(false)

const TotalEncomenda = computed(()=>{
    return props.dados.total
});

const emits = defineEmits(['fatura','message','closePaymentForm'])

const methodos = ref()

const store = useStore()

const methodo = ref('Numerario')

const FormatarDineiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})
const isSubmitting = ref(false)
const mostraTotal = ref(true)

const valor_entregue = ref()

const entregue = ref('')

const total_pago = ref('')

const RestoPagar = ref('')

const Troco = ref('')

const toast = useToast()

const Validar = ref()

const Encomendas = ref([])

const session = ref()

const Lista = ref()

const OnMounted = onMounted(async ()=>{
    methodos.value = props.method
    Validar.value = false;
    entregue.value = "";
    Troco.value = "";
    mostraTotal.value = true;
    Lista.value = props.dados
})

const numero = ((event)=>{
    mostraTotal.value = false;
    entregue.value = Number(entregue.value) + Number(event);

    const MethodPagamento = methodos.value.find(o => o.name === methodo.value)
    if (MethodPagamento) {
        MethodPagamento.valor = String(entregue.value)
        BUSCAR_RESTO()
        // Verificar se o troco do cliente ja e positivo
        if (RestoPagar.value <= 0) {
            Troco.value = RestoPagar.value;
            RestoPagar.value = 0;
            Validar.value = true;

        } else {
            Troco.value = 0;
            Validar.value = false;
        }
    }
})

const numer = (event) => {
    mostraTotal.value = false;
    entregue.value += String(event);

    const MethodPagamento = methodos.value.find(o => o.name === methodo.value)
    if (MethodPagamento) {
        MethodPagamento.valor = entregue.value
        BUSCAR_RESTO()
        // A verificar se o troco do cliente ja e positivo
        if (RestoPagar.value <= 0) {
            Troco.value = RestoPagar.value;
            RestoPagar.value = 0;
            Validar.value = true;
        } else {
            Troco.value = 0;
            Validar.value = false;
        }
    }

}

const methods = (event)=> {
    entregue.value = "";
    methodo.value = event
    BUSCAR_RESTO()
    // Verificar qual foi a metodo clicado
    if (event == "Multicaixa" || 'Transferencia') {
        const existProduct = props.method.find(o => o.name === methodo.value)
        if (existProduct) {
            RestoPagar.value = TotalEncomenda.value - total_pago.value;
            existProduct.valor = RestoPagar.value + Number(existProduct.valor)
            RestoPagar.value = 0
            Validar.value = true
        }
    }
}

const reduzir_pagamento = ()=>{
    entregue.value = entregue.value.substring(0,entregue.value.length - 1);
    const existProduct = props.method.find(o => o.name === methodo.value)
    if (existProduct) {
        var num = existProduct.valor.substring(0, existProduct.valor.length - 1);
        existProduct.valor = num
        BUSCAR_RESTO();
        if (RestoPagar.value <= 0) {
            Troco.value = RestoPagar.value;
            RestoPagar.value = 0;
            Validar.value = true;
        } else {
            Troco.value = 0;
            Validar.value = false;
        }
    }
}

const BUSCAR_RESTO = () =>{
    // aqui va a lógica do total
    total_pago.value = 0
    var ss = props.method.filter(novo => novo.valor);
    ss.forEach(novo => {
        total_pago.value += Number(novo.valor);
    });
    RestoPagar.value = Number(TotalEncomenda.value) - Number(total_pago.value);
}

 const Validar_Pagamento = ()=> {
    isSubmitting.value = true
    Lista.value.methods = methodos.value
    let number = Lista.value.number
    if (RestoPagar.value <= 0) {
    axios.post("/PDV/ValidatePayment",Lista.value)
        .then((Response) => {
            if (!Response.data.message) {
                return print(Response,number)
            } else {
                emits('message',Response.data.type,Response.data.message)
            }
        })
        .catch(function (error) {
            axios.post('/PDV/checkInvoice',Lista.value)
            console.log(error);
        })
        .finally(()=>{
            ShowModal.value = false
            isSubmitting.value = false
        });
    } else {
        emits('message','Info','Valor insuficiente ')
    }
}

const print = ((Response,number)=>{
    session.value = localStorage.getItem('session')
    Encomendas.value = JSON.parse(localStorage.getItem('Encomendas'+session.value));
    Lista.value = Response.data
    Encomendas.value[number] = Lista.value
    localStorage.setItem('Encomendas'+session.value,JSON.stringify(Encomendas.value))
    emits("fatura",Response.data);
})
</script>

<style scoped lang="scss">
@import "../../../assets/Pos/css/pagamento";
</style>
