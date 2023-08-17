<template>
  <div class="principal">
    <div class="Header">
        <div class="Header-left">
            <h2>Lista de Sessões</h2> 
        </div>
    </div>
    <div class="Container">
        <div class="Title">
            <div>Id da sessão</div>
            <div>Ponto de venda</div>
            <div>Responsavel</div>
            <div>Data de abertura</div>
            <div>Valor enformado</div>
            <div>Diferencia</div>
            <div>Data de fecho</div>
            <div>Estado</div>
        </div>

        <div class="list_items">
            <span class="rows" @click=" user.nivel != 'admin' ? message() : $emit('AbrirCaixa', item.id)"
                v-for="item in sessoes" :key="item.id">
                <div>
                    <strong>{{ "Sessões00" + item.id }}</strong>
                </div>
                <div>{{ item.caixa.name }}</div>
                <div>{{ item.user.surname }}</div>
                <div>{{ formatDate(item.created_at) }}</div>
                <div>{{ formatMoney(item.cash) }}</div>
                <div :class="item.cash - Number(item.orders_values) < 0 ? 'text-danger' : ''">
                    {{ formatMoney(item.cash - Number(item.orders_values)) }}
                </div>
                <div>{{ formatDate(item.updated_at) }}</div>
                <div :class="item.state == 'A abrir' ? 'Abrir'
                    : item.state == 'Aberto' ? 'Aberto' : 'Fechado'">
                    {{ item.state }}
                    <i :class="item.state == 'A abrir' ? 'fa fa-clock-o'
                        : item.state == 'Aberto' ? 'fa fa-opencart': 'fa fa-check'"></i>
                </div>
            </span>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import moment from "moment";
import { useStore } from "vuex";
const store = useStore();
const user = ref(store.state.publico.user);
const emits = defineEmits(["message"]);

const props = defineProps({
  dados_caixa: Number,
});

const sessoes = ref([]);

const FormatrDinheiro = Intl.NumberFormat("PT-AO", {
  style: "currency",
  currency: "AOA",
});

const message = () => {
  emits("message", "error", "Usuario sem aseço");
};

onMounted(() => {
  axios
    .get(`caixa/getSessions/${props.dados_caixa}`)
    .then((response) => {
      sessoes.value = response.data;
    })
    .catch((erro) => {
      console.log(erro);
    });
});

const formatDate = (data) => {
  return moment(data).format("DD-MM-YYYY hh:mm:ss");
};
</script>

<style scoped lang="scss">
@import "../../../assets/PontoVenda/css/sessoes";
</style>
