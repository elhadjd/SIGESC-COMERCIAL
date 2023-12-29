<template>
  <div class="principal">
    <div class="Header">
        <div class="Header-left">
            <h2>{{$t('words.client')}}s</h2>
            <span>
            <button @click="newClient">{{$t('words.create')}}</button>
            </span>
        </div>

        <div class="Header-right">
            <span class="p-input-icon-right w-100">
                <i class="pi pi-search" />
                <input
                    type="text"
                    @keyup="searchClient"
                    :placeholder="$t('words.search')"
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
          @click="$emit('newClient',item)"
          :key="item.id"
        >
            <div class="FormClient">
                <div>
                    <img :src="'/Clientes/image/' + item.image" />
                </div>
                <div>
                    <span>{{ item.surname }}</span>
                    <span>{{ item.email }}</span>
                    <span>{{ item.phone }}</span>
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
import { useStore } from "vuex";

const store = useStore();
const Dados = ref(null);
const estadoFormulario = ref(false);

const clients = ref({
    list: [],
    store: []
});
const emits = defineEmits(['newClient'])

const newClient = () => {
    axios.post("/createClient")
    .then((response) => {
        emits('newClient',response.data)
    }).catch((err) => {
      console.log(err);
    });
};

onMounted(async () => {
  await axios.get("/clients")
    .then((response) => {
      clients.value.list = response.data;
      clients.value.store = response.data;
    }).catch((err) => {
      console.log(err);
    });
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

</script>

<style scoped lang="scss">
@import "../../../assets/Clientes/css/index";
</style>
