<template>
<Progress v-if="loadState"/>
<div class="user-select-none formulario-pagamento">
    <form @submit.prevent="validatePayment" class="formulario-content">
        <div class="sessao-one">
            <div>
                <span>{{formatMoney(orderShow.total)}}</span>
                <FontAwesomeIcon icon="fa-solid fa-money-check-dollar" />
            </div>
        </div>

        <div class="sessao-two">
            <div class="Container">
                <div class="buttons">
                    <div>
                        <div>
                            <button @click="$emit('closePaymentForm')"><i class="fa fa-angle-double-left"></i> {{$t('words.canceled')}}</button>
                        </div>

                        <div>
                            <h2>{{$t('words.payment')}}</h2>
                        </div>

                        <div>
                            <button class="Validar" type="submit" v-if="submitState" :disabled="isSubmitting">Validar <i :class="isSubmitting ? 'fa fa-spinner fa-pulse fa-3x fa-fw' : 'fa fa-angle-double-right'"></i></button>
                        </div>
                    </div>
                </div>

                <div class="container-two">
                    <div class="one">
                        <div class="opcao">
                            <div
                            class="methodo"
                            v-for="method in orderStore.order.methods"
                            :key="method.id"
                            @click="setMethods(method.name)"
                            >
                                <strong>{{ method.method_translate[0].translate + " : " + formatMoney(method.valor) }}</strong>
                                <strong class="ValorEntregue"></strong>
                            </div>
                        </div>
                    </div>

                    <div class="two">
                        <div class="container-thee">
                            <div id="totalEncomendaPagamento">
                                <h1 class="TotalEncomendas" v-if="showTotal">{{formatMoney(orderShow.total)}}</h1>
                                <div class="CalculoTotal" v-else>
                                    <div class="TotalCompra">
                                        <h3 class="Restante">
                                            <strong class="capitalize">{{$t('words.missing')}} : </strong>
                                            <span >{{formatMoney(restPayable)}}</span>
                                        </h3>
                                        <h5 class="div5">
                                            <span>{{`${$t('words.total')}`}}</span>
                                            <span id="TotalCompra">: {{formatMoney(orderShow.total)}}</span>
                                        </h5>
                                    </div>
                                    <div class="troco">
                                        <span >
                                            <strong class="capitalize">{{$t('words.change')}} : </strong>
                                            <strong id="TrocoCliente">{{formatMoney(Number(String(clientChange).replace('-','')))}} </strong>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="numeros">
                                <div class="Numero">
                                    <div v-for="i in 9" :key="i" @click="setNumbers(i)" :numero="i"><strong>{{i}}</strong></div>
                                    <div @click="setAmount(0)"><strong>0</strong></div>
                                    <div><strong>.</strong></div>
                                    <div id="TiraNumeros" @click="trashNumbers"><strong><i class="fa fa-eraser" aria-hidden="true"></i></strong></div>
                                </div>
                                <div class="Valores">
                                    <div @click="setNumbers(100)">{{formatMoney(100)}}</div>
                                    <div @click="setNumbers(200)">{{formatMoney(200)}}</div>
                                    <div @click="setNumbers(500)">{{formatMoney(500)}}</div>
                                    <div @click="setNumbers(1000)">{{formatMoney(1000)}}</div>
                                    <div @click="setNumbers(2000)">{{formatMoney(2000)}}</div>
                                    <div @click="setNumbers(8000)">{{formatMoney(8000)}}</div>
                                    <div @click="setNumbers(3000)">{{formatMoney(3000)}}</div>
                                    <div @click="setNumbers(4000)">{{formatMoney(4000)}}</div>
                                    <div @click="setNumbers(5000)">{{formatMoney(5000)}}</div>
                                    <div @click="setNumbers(6000)">{{formatMoney(6000)}}</div>
                                    <div @click="setNumbers(7000)">{{formatMoney(7000)}}</div>
                                    <div @click="setNumbers(10000)">{{formatMoney(10000)}}</div>
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
import { mapMutations, mapState, useStore } from "vuex";
import Progress from '../confirmation/progress.vue'
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {PaymentService} from "@/Components/pos/services/payment/index"
const store = useStore()
const orderShow = computed(()=>store.getters['pos/public'].order)
const orderStore = store.state.pos
const emits = defineEmits(['sendInvoice','closePaymentForm'])
const {
    clientChange,
    paid,
    submitState,
    showTotal,
    setNumbers,
    setAmount,
    setMethods,
    loadState,
    restPayable,
    trashNumbers,
    validatePayment,
    isSubmitting
} = PaymentService(emits)
const OnMounted = onMounted(async ()=>{
    orderStore.order.methods = orderStore.methods
    store.commit("pos/setOrder", orderStore.order);
    submitState.value = false;
    paid.value = "";
    clientChange.value = 0;
    showTotal.value = true;
})
</script>

<style scoped lang="scss">
@import "../../../assets/Pos/css/pagamento";
</style>
