<template>
  <div class="principal">
    <div class="header">
      <div>
        <h1>Clientes</h1>
        <div class="buttons" v-if="estadoFormulario">
          <button @click="GuardarCliente">Guardar</button>
          <button @click="Descartar">Descartar</button>
        </div>
        <button @click="NovoCliente" v-else>Criar Cliente</button>
      </div>

      <div>
        <input
          type="text"
          @keyup="PesquisarCliente"
          placeholder="pesquisa aqui..."
        />
      </div>
    </div>

    <div class="Container">
      <NovoClientes
        v-if="estadoFormulario"
        @voltar="Descartar"
        :Dados="Dados"
      />

      <div v-else class="Clientes">
        <label
          class="clientes"
          v-for="item in clients.list"
          @click="mostrarDetalhesCompleto(item)"
          :key="item.id"
        >
          <div class="FormClient">
            <div>
              <img :src="'/Clientes/image/' + item.image" />
            </div>

            <div>
              <strong>{{ item.surname }}</strong>
              <p>{{ item.email }}</p>
              <p>{{ item.phone }}</p>
            </div>
          </div>
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref, reactive, watch, computed } from "vue";
import NovoClientes from "@/components/Clients/NovoCliente.vue";
import { useStore } from "vuex";

const store = useStore();
const Dados = ref(null);
const estadoFormulario = ref(false);

const clients = ref({
    list: [],
    store: []
});

const lista = ref([]);

const Descartar = () => {
  estadoFormulario.value = false;
  onMountede();
};

const NovoCliente = () => {
  Dados.value = null;
  estadoFormulario.value = true;
};

const onMountede = onMounted(async () => {
  await axios
    .get("/clients")
    .then((response) => {
      clients.value.list = response.data;
      clients.value.store = response.data;
    })
    .catch((erro) => {
      console.log(erro);
    });
});

const mostrarDetalhesCompleto = (event) => {
  Dados.value = event;
  estadoFormulario.value = true;
  store.state.GuardarCliente = false;
};

const Guardar = computed(() => {
  return store.state.GuardarCliente;
});

const PesquisarCliente = (event) => {
  if (event.target.value.length === 2) {
    axios
      .post("/cliente", { faturacao: event.target.value })
      .then((Response) => {
        client.value.list = Response.data;
        client.value.store = Response.data
      })
      .catch((err) => {
        console.log(err);
      });
  } else if (event.target.value.length >= 3) {
    var liste = lista.value;
    const NovaLista = liste.filter((clientes) => {
      return clientes.apelido.includes(event.target.value);
    });
    client.value.list = NovaLista;
  } else  {
    return onMountede();
  }
};

const GuardarCliente = () => {
  store.state.GuardarCliente = true;
};

watch(Guardar, (novo) => {
  if (!novo) {
    onMountede();
    estadoFormulario.value = false;
  }
});
</script>

<style scoped lang="scss">
@import "../../../assets/Clientes/css/index";
</style>
