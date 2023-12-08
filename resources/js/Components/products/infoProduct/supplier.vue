<template>
  <div class="fornecedore">
    <div class="addFornecedor">
      <button
        @click="provider.state = !provider.state"
        type="button"
        class="dropdown-toggle"
      >
        {{`${$t('words.added')} ${$t('words.provider')}`}}
      </button>
      <div v-if="provider.state" class="list-items">
        <input
          type="text"
          autofocus
          @keyup="search"
          class="form-control"
          :placeholder="$t('words.search') + '...'"
        />
        <div
          v-for="item in provider.data"
          :key="item.id"
          @click="addProvider(item.id)"
        >
          {{ item.name }}
        </div>
      </div>
    </div>
    <div class="suppliers">
      <div v-for="item in product.data.fornecedor" :key="item.id">
        <span>{{ item.name }}</span>
        <FontAwesomeIcon
          @click="deleteSupplier(item.id)"
          icon="fa-solid fa-delete-left"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { serviceMessage } from "@/composable/public/messages";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { computed, onMounted, ref } from "vue";
import { useStore } from "vuex";
const store = useStore()
const provider = ref({
  state: false,
  data: [],
  store: [],
});
const product = computed(()=> store.getters['Product/product'])
const {showMessage} = serviceMessage()
onMounted(async () => {
  getSuppliers();
});

async function getSuppliers() {
  await axios
    .get("/suppliers")
    .then((Response) => {
      provider.value.data = Response.data;
      provider.value.store = Response.data;
    })
    .catch((err) => {
      console.log(err);
    });
}

async function addProvider(provider) {
  await axios
    .post(`/AddProductSupplier/${product.value.data.id}/${provider}`)
    .then((response) => {
      if (response.data.message) return showMessage(response.data.message,response.data.type);
      product.value.data.fornecedor = response.data.fornecedor;
    })
    .catch((err) => {
      console.log(err);
    });
}

function search(event) {
  const filter = provider.value.store.filter((item) => {
    return (
      String(item.name)
        .toLowerCase()
        .includes(event.target.value.toLowerCase()) ||
      String(item.empresa)
        .toLowerCase()
        .includes(event.target.value.toLowerCase()) ||
      String(item.nif).includes(event.target.value)
    );
  });
  provider.value.data = filter;
}

const deleteSupplier = async(supplier) => {
  await axios
    .post(`/deleteSupplierProduct/${product.value.data.id}/${supplier}`)
    .then((response) => {
      product.value.data.fornecedor = response.data.fornecedor;
    })
    .catch((err) => {
      console.log(err);
    });
};
</script>

<style scoped lang="scss">
@import "../../../../assets/produtos/css/infoProducts/supplier.scss";
</style>
