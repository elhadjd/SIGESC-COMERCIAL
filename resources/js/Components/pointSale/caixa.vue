<template>
  <Toasts />
  <Progress v-if="showProgress" />
  <div class="principal">
    <div class="Header">
        <div class="Header-left">
            <div @click="$emit('pointOfSale',$t('apps.pdvName'))" class="flex flex-row mt-2 cursor-pointer hover:text-[#00a5cf] p-3 items-center justify-start">
                <FontAwesomeIcon class="mb-2" icon="fa-solid fa-arrow-left"/>
                <h2>{{$t('apps.pdvName')}}</h2>
            </div>
        </div>
        <div class="Header-right">

        </div>
    </div>
    <div class="Container">
      <div class="buttons">
        <button @click="AbrirControlo"
          v-if="DadosCaixa.orders.state == 'A abrir'"
          class="botoesCaixa">
          {{`${$t('words.open')} ${$t('words.the')}`}} controle
          <FontAwesomeIcon v-if="showProgress" icon="fa-solid fa-spinner" class="text-2xl text" style="color: white" shake />
        </button>
        <button @click="ContinuarVenda"
          v-if="DadosCaixa.orders.state == 'Aberto'"
          class="BtnColor Continuar botoesCaixa" >
          {{$t('words.continue')}}
        </button>
        <button @click="CloseCash"
          v-if="DadosCaixa.orders.state == 'Aberto'"
          class="mx-1 botoesCaixa">
          {{$t('phrases.closeControl')}}
          <FontAwesomeIcon v-if="showProgress" icon="fa-solid fa-spinner" class="text-2xl text" style="color: white" shake />
        </button>
        <button
          @click="updateSession"
          v-if="DadosCaixa.orders.state == 'Fechado'"
          class="mx-1 botoesCaixa capitalize"
        >
          {{$t('words.edit')}}
          <FontAwesomeIcon v-if="showProgress" icon="fa-solid fa-spinner" class="text-2xl text" style="color: white" shake />
        </button>
      </div>
      <div class="FormCaixaCompleta">
        <div class="FormCaixa">
          <div id="FormSession">
            <div class="FormSessionCima">
              <div class="d-flex" v-for="item in DadosCaixa.operations" :key="item.id">
                <FontAwesomeIcon v-if="progressAmount" icon="fa-solid fa-spinner" class="text-2xl text" style="color: #00a5cf" shake />
                <cash v-else class="mt-2" :size="25" />
                <div class="TotalPorCima">
                  <div class="text-center">
                    {{ formatMoney(progressAmount ? 0 : item.operations_sum_amount) }}
                  </div>
                  <div class="truncate">{{item.operation_translate[0].translate}}</div>
                </div>
              </div>
              <div class="d-flex">
                <FontAwesomeIcon v-if="progressAmount" icon="fa-solid fa-spinner" class="text-2xl text" style="color: #00a5cf" shake />
                <cash v-else class="mt-2" :size="25" />
                <div class="TotalPorCima">
                  <div class="text-center">
                    {{ formatMoney(progressAmount ? 0 : DadosCaixa.orders.cash)}}
                    
                  </div>
                  <div class="truncate">Total {{$t('words.reported')}}</div>
                </div>
              </div>
              <div class="d-flex items-center">
                <FontAwesomeIcon v-if="progressAmount" icon="fa-solid fa-spinner" class="text-2xl text" style="color: #00a5cf" shake />
                <cash class="mt-2" v-else :size="25" />
                <div class="TotalPorCima">
                  <div>{{ formatMoney(progressAmount ? 0 : DadosCaixa.orders.cash - Number(DadosCaixa.orders.orders_values)) }}</div>
                  <div class="truncate">{{$t('words.difference')}}</div>
                </div>
              </div>
              <div class="d-flex items-center">
                <FontAwesomeIcon v-if="progressAmount" icon="fa-solid fa-spinner" class="text-2xl text" style="color: #00a5cf" shake />
                <shopping v-else class="mt-2" :size="25" />
                <div class="TotalPorCima">
                  <div>{{progressAmount ? 0 :  DadosCaixa.length }}</div>
                  <div class="truncate">{{$t('words.order') + 's'}}</div>
                </div>
              </div>
              <div class="d-flex items-center">
                <FontAwesomeIcon v-if="progressAmount" icon="fa-solid fa-spinner" class="text-2xl text" style="color: #00a5cf" shake />
                <Coin v-else class="mt-2" :size="25" />
                <div class="TotalPorCima">
                  <div class="text-center">
                    {{formatMoney(progressAmount ? 0 : Number(DadosCaixa.orders.orders_sum_total) + operations.entrada + Number(DadosCaixa.orders.opening) - operations.saida - operations.gasto) }}
                  </div>
                  <div class="truncate">{{$t('words.payment') + 's'}}</div>
                </div>
              </div>
            </div>

            <div class="content">
              <div class="title_input">
                <div class="titulo">
                  <h1>POS/0000{{ DadosCaixa.orders.id }}</h1>
                </div>

                <div
                  v-if="DadosCaixa.orders.state != 'Fechado'"
                  class="DivInput"
                >
                  <input
                    class="Input"
                    id="inputRef" ref="inputRef"
                    v-model="Input"
                    :placeholder="
                      DadosCaixa.orders.state == 'Aberto'
                        ? 'Digite o valor de fachamento'
                        : placeholder
                    "
                  />
                </div>
              </div>

              <div class="content-two">
                <div class="info">
                  <div>
                    <span>{{$t('pdv.openingBy')}} :</span>
                    <span>{{$t('apps.pdvName')}}:</span>
                    <span>{{$t('pdv.openingDate')}}:</span>
                    <span v-if="DadosCaixa.orders.state == 'Fechado'">{{$t('words.date')}} de {{$t('words.close')}}:</span>
                  </div>

                  <div>
                    <span class="truncate">{{ DadosCaixa.user.surname }}</span>
                    <span class="truncate">{{ DadosCaixa['caixa']['name'] }}</span>
                    <span class="truncate">{{ formatDate(DadosCaixa.orders.created_at) }}</span>
                    <span class="truncate" v-if="DadosCaixa.orders.state == 'Fechado'">
                      {{ formatDate(DadosCaixa.orders.updated_at) }}
                    </span>
                  </div>
                </div>

                <div v-if="DadosCaixa.orders.state != 'A abrir'" class="abertura">
                    <div class="informacoes">
                        <span>{{$t('words.opening')}}:</span>
                        <span>{{formatMoney(DadosCaixa.orders.opening)}}</span>
                    </div>
                    <div class="informacoes" v-for="method in DadosCaixa.methods" :key="method.id">
                        <span>{{method.method_translate[0]?.translate+':'}}</span>
                        <span>{{formatMoney(method.payments_pdv_sum_amount_paid-method.payments_pdv_sum_change)}}</span>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import Toasts from "primevue/toast";
import { useToast } from "primevue/usetoast";
import cash from "vue-material-design-icons/CashMultiple.vue";
import shopping from "vue-material-design-icons/Shopping.vue";
import Coin from "vue-material-design-icons/HandCoinOutline.vue";
import Progress from "../confirmation/progress.vue";
import { useI18n } from "vue-i18n";
const { t,te,tm,locale } = useI18n();

import {
  onMounted,
  reactive,
  toRefs,
  ref,
  watch,
  onUpdated,
} from "@vue/runtime-core";
import axios from "axios";
import { Inertia } from "@inertiajs/inertia";
import Message from "primevue/message";
import moment from 'moment'
import { useCurrencyInput } from "vue-currency-input";
import { router } from "@inertiajs/vue3";
import { serviceMessage } from "@/composable/public/messages";
const emits = defineEmits(["message",'pointOfSale']);
const showProgress = ref(false);
const progressAmount = ref(false)
const props = defineProps(["caixaId"]);
const {showMessage} = serviceMessage()
const operations = ref({
    entrada: 0,
    saida: 0,
    gasto: 0
})
const Toast = useToast();
const { inputRef } = useCurrencyInput({currency: 'AOA' })
const Input = ref();
const placeholder = ref("digita o valor de abertura");

const DadosCaixa = ref({
    operations: [],
    orders: [],
    methods:[],
    caixa: [],
    user: [],
    valorInformado: 0
});
const formatDate = (date) => {
  return moment(date).format("DD-MM-YYYY H:m:s");
};

const ContinuarVenda = () => {
  router.get(`Pos/${DadosCaixa.value.caixa.id}`, {
    onSuccess: (Response) => {
        showMessage(Response.data.message,'info')
    },
  });
};

const AbrirControlo = () => {
  if (Input.value == null || Input.value == "") {
    showMessage(t(`message.emptyInput`,{name: t('words.amount')}),'info')
  } else {
    showProgress.value = true;
    DadosCaixa.value.opening = Input.value;
    axios
      .post("/AbrirControloCaixa", DadosCaixa.value)
      .then((response) => {
        if (response.data.message) {
          return emits("message", response.data.message, response.data.tipo);
        }
        DadosCaixa.value = response.data;
        DadosCaixa.value = response.data.session;
        showProgress.value = false;
        localStorage.clear();
      })
      .catch((erro) => {
        console.log(erro);
      });
  }
};

const updateSession = () => {
  showProgress.value = true;
  axios.post(`caixa/updateSession/${locale.value}/${DadosCaixa.value.orders.id}`)
    .then((response) => {
        DadosCaixa.value = response.data;
        DadosCaixa.value.caixa = response.data.orders.caixa
        DadosCaixa.value.user = response.data.orders.user
        CalcularValorCaixa();
        showProgress.value = false;
    }).catch((erro) => {
        showProgress.value = false;
        console.log(erro);
    });
};

const CloseCash = () => {
  if (Input.value == null || Input.value == "") {
    showMessage(t(`message.emptyInput`,{name: t('words.amount')}),'info')
  } else {
    showProgress.value = true;
    let totals = Number(DadosCaixa.value.orders.orders_sum_total) + Number(operations.value.entrada) + Number(DadosCaixa.value.orders.opening)
    let discont = Number(operations.value.saida) + Number(operations.value.gasto)
    const totalValue = totals - discont
    axios.post(`caixa/clossSession/${locale.value}/${DadosCaixa.value.orders.id}`, {informado:Input.value,total:totalValue})
      .then((response) => {
        if(response.data.message) return showMessage(response.data.message,response.data.type)
        DadosCaixa.value = response.data;
        DadosCaixa.value.caixa = response.data.orders.caixa
        DadosCaixa.value.user = response.data.orders.user
        showProgress.value = false;
        CalcularValorCaixa();
      })
      .catch((err) => {
        showMessage(t('message.serverError'))
        console.log(err);
      }).finally(()=>{
        showProgress.value = false;
      } );
  }
};

onMounted(async() => {
    progressAmount.value = true
    await axios.get(`caixa/getCaixa/${locale.value}/${props.caixaId}`)
    .then((Response) => {
      DadosCaixa.value = Response.data;
      DadosCaixa.value.caixa = Response.data.orders.caixa
      DadosCaixa.value.user = Response.data.orders.user
      CalcularValorCaixa();
    })
    .catch((err) => {
      console.log(err);
    }).finally(()=>{
        progressAmount.value = false
    });
});

const CalcularValorCaixa = () => {
    DadosCaixa.value.operations.forEach(operation => {
        if (operation.name == "Entrada") {
            operations.value.entrada = Number(operation.operations_sum_amount)
        }else if(operation.name == "Saida"){
            operations.value.saida = Number(operation.operations_sum_amount)
        }else{
            operations.value.gasto = Number(operation.operations_sum_amount)
        }
    });
};
</script>

<style lang="scss" scoped>
@import "../../../assets/PontoVenda/css/caixa";
</style>
