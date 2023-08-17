<template>
  <Toasts />
  <Progress v-if="ShowModal" />
  <div class="principal">
    <div class="Header"></div>
    <div class="Container">
      <div class="buttons">
        <button @click="AbrirControlo"
          v-if="DadosCaixa.orders.state == 'A abrir'"
          class="botoesCaixa">
          Abrir controlo
        </button>
        <button @click="ContinuarVenda"
          v-if="DadosCaixa.orders.state == 'Aberto'"
          class="BtnColor Continuar botoesCaixa" >
          Continuar
        </button>
        <button @click="CloseCash"
          v-if="DadosCaixa.orders.state == 'Aberto'"
          class="mx-1 botoesCaixa">
          Fechar
        </button>
        <button
          @click="updateSession"
          v-if="DadosCaixa.orders.state == 'Fechado'"
          class="mx-1 botoesCaixa"
        >
          Editar
        </button>
      </div>
      <div class="FormCaixaCompleta">
        <div class="FormCaixa">
          <div id="FormSession">
            <div class="FormSessionCima">
              <div class="d-flex" v-for="item in DadosCaixa.operations" :key="item.id">
                <cash class="mt-2" :size="25" />
                <div class="TotalPorCima">
                  <div class="text-center">
                    {{ FormatrDinheiro.format(item.operations_sum_amount) }}
                  </div>
                  <div>{{item.name}}</div>
                </div>
              </div>
              <div class="d-flex">
                <cash class="mt-2" :size="25" />
                <div class="TotalPorCima">
                  <div class="text-center">
                    {{ FormatrDinheiro.format(DadosCaixa.orders.cash)}}
                  </div>
                  <div>Valor informado</div>
                </div>
              </div>
              <div class="d-flex">
                <cash class="mt-2" :size="25" />
                <div class="TotalPorCima">
                  <div>{{ FormatrDinheiro.format(DadosCaixa.orders.cash - Number(DadosCaixa.orders.orders_values)) }}</div>
                  <div>Diferencia</div>
                </div>
              </div>
              <div class="d-flex">
                <shopping class="mt-2" :size="25" />
                <div class="TotalPorCima">
                  <div>{{ DadosCaixa.length }}</div>
                  <div>Pedidos</div>
                </div>
              </div>
              <div class="d-flex">
                <Coin class="mt-2" :size="25" />
                <div class="TotalPorCima">
                  <div class="text-center">
                    {{ FormatrDinheiro.format(Number(DadosCaixa.orders.orders_sum_total) + operations.entrada + Number(DadosCaixa.orders.opening) - operations.saida - operations.gasto) }}
                  </div>
                  <div>Pagamentos</div>
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
                    <span>Aberto por:</span>
                    <span>Ponto de Venda:</span>
                    <span>Data de Abertura:</span>
                    <span v-if="DadosCaixa.orders.state == 'Fechado'">Data de Fecho:</span>
                  </div>

                  <div>
                    <span>{{ DadosCaixa.user.surname }}</span>
                    <span>{{ DadosCaixa['caixa']['name'] }}</span>
                    <span>{{ formatDate(DadosCaixa.orders.created_at) }}</span>
                    <span v-if="DadosCaixa.orders.state == 'Fechado'">
                      {{ formatDate(DadosCaixa.orders.updated_at) }}
                    </span>
                  </div>
                </div>

                <div v-if="DadosCaixa.orders.state != 'A abrir'" class="abertura">
                    <div class="informacoes">
                        <span>Abertura:</span>
                        <span>{{formatMoney(DadosCaixa.orders.opening)}}</span>
                    </div>
                    <div class="informacoes" v-for="method in DadosCaixa.methods" :key="method.id">
                            <span>{{method.name+':'}}</span>
                            <span>{{formatMoney(method.payments_pdv_sum_amount_paid)}}</span>
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
import Toasts from "primevue/toast";
import { useToast } from "primevue/usetoast";
import cash from "vue-material-design-icons/CashMultiple.vue";
import shopping from "vue-material-design-icons/Shopping.vue";
import Coin from "vue-material-design-icons/HandCoinOutline.vue";
import Progress from "../confirmation/progress.vue";
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
const emits = defineEmits(["message"]);
const ShowModal = ref(false);
const props = defineProps(["caixaId"]);
const operations = ref({
    entrada: 0,
    saida: 0,
    gasto: 0
})
const Toast = useToast();
const { inputRef } = useCurrencyInput({currency: 'AOA' })
const Input = ref();
const placeholder = ref("digita o valor de abertura");
const FormatrDinheiro = new Intl.NumberFormat("PT-AO", {
  style: "currency",
  currency: "AOA",
});
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
        emits("message", "info", Response.props.message);
    },
  });
};

const AbrirControlo = () => {
  if (Input.value == null || Input.value == "") {
    Toast.add({
      severity: "info",
      summary: "Menssage",
      detail: "Por favor informe o valor para Abrir a caixa",
      life: 5000,
    });
  } else {
    ShowModal.value = true;
    DadosCaixa.value.opening = Input.value;
    axios
      .post("/AbrirControloCaixa", DadosCaixa.value)
      .then((response) => {
        if (response.data.message) {
          return emits("message", response.data.message, response.data.tipo);
        }
        DadosCaixa.value = response.data;
        DadosCaixa.value = response.data.session;
        ShowModal.value = false;
        localStorage.clear();
      })
      .catch((erro) => {
        console.log(erro);
      });
  }
};

const updateSession = () => {
  ShowModal.value = true;
  axios.post(`caixa/updateSession/${DadosCaixa.value.orders.id}`)
    .then((response) => {
        DadosCaixa.value = response.data;
        DadosCaixa.value.caixa = response.data.orders.caixa
        DadosCaixa.value.user = response.data.orders.user
        CalcularValorCaixa();
        ShowModal.value = false;
    }).catch((erro) => {
        ShowModal.value = false;
        console.log(erro);
    });
};

const CloseCash = () => {
  if (Input.value == null || Input.value == "") {
    Toast.add({
      severity: "info",
      summary: "Menssage",
      detail: "Por favor informe o valor para fechar a caixa",
      life: 5000,
    });
  } else {
    ShowModal.value = true;
    let totals = Number(DadosCaixa.value.orders.orders_sum_total) + Number(operations.value.entrada) + Number(DadosCaixa.value.orders.opening)
    let discont = Number(operations.value.saida) + Number(operations.value.gasto)
    const totalValue = totals - discont
    axios.post(`caixa/clossSession/${DadosCaixa.value.orders.id}`, {informado:Input.value,total:totalValue})
      .then((response) => {
        DadosCaixa.value = response.data;
        DadosCaixa.value.caixa = response.data.orders.caixa
        DadosCaixa.value.user = response.data.orders.user
        ShowModal.value = false;
        CalcularValorCaixa();
      })
      .catch((erro) => {
        ShowModal.value = false;
        console.log(erro);
      });
  }
};

onMounted(() => {
   axios.get(`caixa/getCaixa/${props.caixaId}`)
    .then((Response) => {
      DadosCaixa.value = Response.data;
      DadosCaixa.value.caixa = Response.data.orders.caixa
      DadosCaixa.value.user = Response.data.orders.user
      CalcularValorCaixa();
    })
    .catch((err) => {
      console.log(err);
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
