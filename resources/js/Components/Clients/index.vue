<template>
  <div class="principal">
    <div class="Header">
      <div class="Header-left">
        <h2>Clientes</h2>
        <div class="buttons" v-if="estadoFormulario">
          <button @click="GuardarCliente">Guardar</button>
          <button @click="Descartar">Descartar</button>
        </div>
        <button @click="NovoCliente" v-else>Criar Cliente</button>
      </div>

      <div class="Header-right">
        <span class="p-input-icon-right w-100">
            <i class="pi pi-search" />
            <input
                type="text"
                @keyup="searchClient"
                placeholder="pesquisa aqui..."
            />
        </span>

      </div>
    </div>

    <div class="Container">
      <newClient
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
                    <span>{{ item.surname }}</span>
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
import newClient from '@/Components/clients/NovoCliente.vue'
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

    axios
    .post("/createClient")
    .then((response) => {
        mostrarDetalhesCompleto(response.data)
    })
    .catch((erro) => {
      console.log(erro);
    });
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

const searchClient = ((event)=>{
    const filter = clients.value.store.filter((item)=>{
        return String(item.surname).toLocaleLowerCase().includes(event.target.value.toLocaleLowerCase())
        || String(item.name).toLocaleLowerCase().includes(event.target.value.toLocaleLowerCase())
        || String(item.email).toLocaleLowerCase().includes(event.target.value.toLocaleLowerCase())
        || String(item.phone).toLocaleLowerCase().includes(event.target.value.toLocaleLowerCase())
    })
    clients.value.list = filter
})

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
