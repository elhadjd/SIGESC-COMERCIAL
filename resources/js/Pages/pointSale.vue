<template>

  <div class="mt-0" style="height: 100vh; width: 100vw">
    <Toast />
    <Cabacalho @modulos="modulos" :menus="menus" />
    <div class="Container">
      <Caixa v-if="modul == 'caixa'" :caixaId="session" @message="message" />
      <Relatorio v-if="modul == 'Relatório'" />
      <Operation @message="message" v-if="modul == 'Operações'" />
      <analisProducts v-if="modul == 'Avaliação de Stock'" />
      <Order @message="message" v-if="modul == 'Orden de Venda'" />
      <MenuPdv
        @definicaoCaixa="definicaoCaixa"
        @Caixa="EmitsMenu"
        @sessao="AbrirSessao"
        @CriarCaixa="CriarCaixa"
        @AbrirCaixa="AbrirCaixa"
        @message="message"
        v-if="modul == 'Ponto de venda'"
        :dados="data.dados"
      />
      <Products v-if="modul == 'Artigos'" />
      <Fornecedores v-if="modul == 'fornecedor'" />
      <Sections
        v-if="modul == 'sessao'"
        :dados_caixa="dados_caixa"
        @AbrirCaixa="AbrirCaixa"
        @message="message"
      />
      <NovaCaixa
        @message="message"
        @close="modulos('menu')"
        :DadosCaixa="DadosCaixa"
        v-if="modul == 'NovaCaixa'"/>
    </div>
  </div>
</template>

<script setup>
import Products from "@/Components/products/Products.vue";
import Cabacalho from "../Layouts/header.vue";
import supplier from "@/components/suppliers/index.vue";
import Sections from "@/Components/pointSale/sessions.vue";
import { onMounted, reactive, ref } from "@vue/runtime-core";
import MenuPdv from "@/Components/pointSale/MenuPdv.vue";
import Order from "@/Components/pointSale/orders.vue";
import Operation from "@/Components/pointSale/Operation.vue";
import { useStore } from "vuex";
import analisProducts from '@/Components/pointSale/analisProducts.vue'
import Relatorio from "@/components/pointSale/relatorio/index.vue";
import Caixa from "@/Components/pointSale/caixa.vue";
import NovaCaixa from "@/components/pointSale/CashConfig/NovaCaixa.vue";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import axios from "axios";
import { Inertia } from "@inertiajs/inertia";

const menus = ref([
  { menu: "Ponto de venda" },
  {
    menu: "Relatório",
    subMenu: [
      { name: "Orden de Venda" },
      { name: "Relatório" },
      { name: "Operações" },
      { name: "Avaliação de Stock" },
    ],
  },
  { menu: "Artigos" },
]);

const props = defineProps({
  data: Object,
});
const toast = useToast();
const modul = ref("Ponto de venda");
const store = useStore();
const session = ref();

const dados_caixa = ref();
const data = reactive({
  Apps: null,
  dados: {
    caixas: props.data,
    user: store.state.user,
  },
});

const DadosCaixa = ref();

const definicaoCaixa = (payload) => {
  DadosCaixa.value = payload;
  modul.value = "NovaCaixa";
};

const modulos = (event) => {
  store.state.EstadoMuvemento.estado = false;
  store.state.ProdutosMostrarUm = false;
  MostrarDrop.value = null;
  modul.value = event;
};

let MostrarDrop = ref(null);

const OnMounted = onMounted(() => {
  data.Apps = props.app;
});

const Sair = () => {
  Inertia.post("/sair");
};

const message = (tipo, message) => {
  toast.add({
    severity: tipo,
    summary: "Message",
    detail: message,
    life: 5000,
  });
};

const DropShow = (evento) => {
  if (evento == MostrarDrop.value) {
    MostrarDrop.value = null;
  } else {
    MostrarDrop.value = evento;
  }
};

const EmitsMenu = (event, OutroEvento) => {
  if (event == "AbrirCaixa") {
    axios
      .get("/FormularioSession?id_caixa=" + OutroEvento)
      .then((Response) => {
        session.value = Response.data.session.id;
        modul.value = "caixa";
      })
      .catch((err) => {
        console.log(err);
      });
  }
};

const CriarCaixa = () => {
  modul.value = "NovaCaixa";
};

const AbrirSessao = (payload) => {
  modul.value = "sessao";
  dados_caixa.value = payload;
};

const AbrirCaixa = (payload) => {
  modul.value = "caixa";
  session.value = payload;
};
</script>

<style scoped lang="scss">
@import "../../assets/PontoVenda/css/PontoVenda";
</style>
