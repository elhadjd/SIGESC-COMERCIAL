<template>
  <div v-if="stateFormItems" class="encomenda">
    <orderItems @message="message" @fechar="stateFormItems = false" :order="ListPedido" />
  </div>
  <div v-else class="w-100 DivOrdensVendas">
    <div class="h-100">
      <div class="d-flex w-100 OrdenCima">
        <div class="OrdenCimaEsquerda w-50">
          <h4 class="text-secondary ms-4">Ordens</h4>
        </div>
        <div class="OrdenCimaDireita w-50">
          <KeepAlive>
            <FiltroOrden @ListDefault="List" :Filtros="Filtros" class="" />
          </KeepAlive>
        </div>
      </div>
      <div class="FormListe">
        <div class="d-flex titleOrdens text-secondary w-100">
          <strong class="d-flex w-100">
            <div>Ref Da Orden</div>
            <div>Ponto De Venda</div>
            <div>Sessão</div>
            <div>Cliente</div>
            <div>Data</div>
            <div>Funcionario</div>
            <div class="TotalOrden">Total</div>
            <div class="px-3">Estado</div>
          </strong>
        </div>
        <div class="overflow-auto ListaOrden">
          <div v-if="$store.state.pos.ListEncomenda.mostrar">
            <div
              v-show="$store.state.pos.ListEncomenda.MostrarFiltro >= 1"
              v-for="primeira in $store.state.pos.ListEncomenda.PrimeiraLista"
              :key="primeira.id"
              class="ListAgrupado"
            >
              <div
                @click="BuscarLista(primeira)"
                class="w-100 Grupos dropdown-toggle"
              >
                {{ primeira.nome }}
              </div>
              <div
                v-show="$store.state.pos.ListEncomenda.MostrarFiltro >= 2"
                v-for="segunda in $store.state.pos.ListEncomenda.SegundaLista"
                :key="segunda.id"
                class="ListAgrupado"
              >
                <div class="w-100 Grupos dropdown-toggle">
                  {{ segunda.nome }}
                </div>
                <div
                  v-show="$store.state.pos.ListEncomenda.MostrarFiltro >= 3"
                  v-for="terceira in $store.state.pos.ListEncomenda.TerceiraLista"
                  :key="terceira.id"
                  class="ListAgrupado"
                >
                  <div class="w-100 Grupos dropdown-toggle">
                    {{ terceira.nome }}
                  </div>
                  <div
                    v-show="$store.state.pos.ListEncomenda.MostrarFiltro == 4"
                    v-for="quarta in $store.state.pos.ListEncomenda.QuartaLista"
                    :key="quarta.id"
                    class="ListAgrupado"
                  >
                    <div class="w-100 Grupos dropdown-toggle">
                      {{ quarta.nome }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else>
            <div
              v-for="encomenda in ListEncomenda.slice(0, 100)"
              :key="encomenda.id"
              @click="Encomendas(encomenda)"
              class="d-flex ListaOrdens"
            >
              <div>
                <strong>{{ "Orden " + encomenda.id }}</strong>
              </div>
              <div>{{ encomenda.session.caixa.name }}</div>
              <div>{{ encomenda.session_id }}</div>
              <div>{{ encomenda.cliente }}</div>
              <div>{{ formatDate(encomenda.created_at) }}</div>
              <div>{{ encomenda.user.surname }}</div>
              <div class="TotalOrden pr-1">
                {{ FormetDineiro.format(encomenda.total) }}
              </div>
              <div class="px-5">{{ encomenda.state }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from "@vue/runtime-core";
import FiltroOrden from "@/components/FiltrosPesquisas/index.vue";
import orderItems from "./orderItems.vue";
import { useStore } from "vuex";
import moment from "moment";

const emit = defineEmits(['message']);

const ListPedido = ref();

const store = useStore();

const stateFormItems = ref(false);

const ListEncomenda = ref([]);

const Filtros = reactive({
  Agrupar: [
    {
      table: "encomendas_pos",
      tableUser: "users",
      TableCliente: "clientes",
      join: "cliente_pos",
    },
  ],
});

const formatDate = (date) => {
  return moment(date).format("DD-MM-YYYY H:m:s");
};

const Encomendas = (event) => {
  stateFormItems.value = true;
  ListPedido.value = event;
};

const message = (type, message) => {
    emit('message', type,message)
}

const BuscarLista = (event) => {
  if (store.state.pos.ListEncomenda.MostrarFiltro >= 2) {
    store.state.pos.ListEncomenda.MostrarFiltro -= 1;
  } else {
    store.state.pos.ListEncomenda.MostrarFiltro += 1;
  }
};

const List = (list) => {
  ListEncomenda.value = list.data;
};

const FormetDineiro = new Intl.NumberFormat("pt-AO", {
  style: "currency",
  currency: "AOA",
});
</script>

<style scoped>
@import url("../../../assets/PontoVenda/css/OrdenDeVenda.css");
</style>
