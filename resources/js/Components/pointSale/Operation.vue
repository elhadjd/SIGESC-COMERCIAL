<template>
  <div class="DivOrdensVendas">
  <Progress v-if="showProgress"/>
    <div class="Container">
      <div class="OrdenCima">
        <div class="GastosCimaEsquerda">
          <h4>{{$t('words.expenses')}}</h4>
          <button @click="AddOperation" class="AdicionarGastos">
            {{$t('words.added')}}
          </button>
        </div>
        <div class="GastosCimaDireita">
          <span class="p-input-icon-right w-100">
            <i class="pi pi-search" />
            <input
              type="text"
              name="PesquuisarGasto"
              :placeholder="`${$t('words.search')} ${$t('words.by')} ${$t('words.date')} Exemplo(01-01-2001)`"
              id="PesquuisarGasto"
              class="w-100 p-2 form-control"
            />
          </span>
        </div>
      </div>
      <div class="list-content">
        <div class="titleGastos">
          <div>Id {{`${$t('words.of')} ${$t('words.movement')}`}}</div>
          <div>{{$t('words.subject')}}</div>
          <div>{{$t('words.employee')}}</div>
          <div>{{$t('words.date')}}</div>
          <div class="TotalOrden">{{$t('words.total')}}</div>
        </div>
        <div class="ListaOrden">
          <div>
            <div
              v-for="(item, index) in operations.data"
              :key="index"
              class="listOperations"
            >
              <span @click="operation('one', index)">{{ index }}</span>

              <div v-if="operations.indexOne === index">
                <div
                  class="operations"
                  v-for="(second, key) in item"
                  :key="key"
                >
                  <span
                    class="dropdown-toggle"
                    @click="operation('two', key), sumOperations(second)"
                    >{{ key }}</span
                  >
                  <div class="items" v-if="operations.indexTwo === key">
                    <div
                      v-for="theen in second"
                      @click="sowMovement(theen)"
                      :key="theen.id"
                    >
                      <div>{{ key }}</div>
                      <div>{{ theen.subject }}</div>
                      <div>{{ theen.user.surname }}</div>
                      <div>{{ formatDate(theen.created_at) }}</div>
                      <div class="TotalOrden">
                        {{ formatMoney(theen.amount) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="TotalGastos">
            <span class="">total: </span>
            <div>{{ formatMoney(operations.total) }}</div>
          </div>
        </div>
      </div>
      <NewOperation
        @update="(operations.data = $event), (newOperation.state = false)"
        @message="message"
        @close="newOperation.state = false"
        :data="newOperation.data"
        v-if="newOperation.state"
      />
    </div>
  </div>
</template>

<script setup>
import Progress from "../confirmation/progress.vue";
import { onMounted, reactive, ref } from "@vue/runtime-core";
import axios from "axios";
import NewOperation from "@/Components/pointSale/newOperation.vue";
const showProgress = ref(false)
const emit = defineEmits(["message"]);
const Resposta = reactive({
  user: [],
  lista: null,
  storeGastos: [],
  total: null,
});

const operations = reactive({
  data: [],
  storeData: [],
  total: 0,
  indexOne: null,
  indexTwo: null,
});

const newOperation = ref({
  state: false,
  data: [],
});

const sowMovement = (item) => {
  newOperation.value.data = item;
  newOperation.value.state = true;
};

const FormatDinheiro = new Intl.NumberFormat("pt-AO", {
  style: "currency",
  currency: "AOA",
});

onMounted(() => {
    showProgress.value = true
  axios
    .get("/PDV/getOperation")
    .then((Response) => {
      operations.data = Response.data;
      operations.storeData = Response.data
    })
    .catch((err) => {
      console.log(err);
    }).finally(()=>{
        showProgress.value = false
    });
});

const showGasto = (abject) => {
  NovoGasto.value = abject;
  FormGasto.value = true;
};

const operation = (type, index) => {
  type === "one"
    ? (operations.indexOne =
        operations.indexOne === null || operations.indexOne != index
          ? index
          : null)
    : (operations.indexTwo =
        operations.indexTwo === null || operations.indexTwo != index
          ? index
          : null);
};

const sumOperations = (items) => {
  operations.total = 0;
  items.forEach((item) => {
    operations.total += Number(item.amount);
  });
};
const AddOperation = () => {
  newOperation.value.data = {};
  newOperation.value.state = true;
};
const message = (message, type) => {
  emit("message", type, message);
};
</script>

<style scoped lang="scss">
@import "../../../assets/PontoVenda/css/gastos";
</style>
