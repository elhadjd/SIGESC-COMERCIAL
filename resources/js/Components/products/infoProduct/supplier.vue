<template>
  <div class="fornecedore">
    <div class="addFornecedor">
      <button
        @click="fornecedores.state = !fornecedores.state"
        type="button"
        class="dropdown-toggle"
      >
        Adicionar fornecedor
      </button>
      <div v-if="fornecedores.state" class="list-items">
        <input
          type="text"
          autofocus
          @keyup="search"
          class="form-control"
          placeholder="pesquisa..."
        />
        <div
          v-for="item in fornecedores.data"
          :key="item.id"
          @click="AdicionarFornecedor(item.id)"
        >
          {{ item.name }}
        </div>
      </div>
    </div>
    <div class="suppliers">
      <div v-for="item in product.fornecedor" :key="item.id">
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
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { onMounted, ref } from "vue";

const fornecedores = ref({
  state: false,
  data: [],
  store: [],
});

const props = defineProps({
  product: {
    default: () => Object,
  },
});

onMounted(async () => {
  getSuppliers();
});

async function getSuppliers() {
  await axios
    .get("/suppliers")
    .then((Response) => {
      fornecedores.value.data = Response.data;
      fornecedores.value.store = Response.data;
      if (Response.data.ListPrice) {
        ListPrice.value.preco = Response.data.ListPrice.preco_de_desconto;
        ListPrice.value.quantidade = Response.data.ListPrice.quantidade;
      }
    })
    .catch((err) => {
      console.log(err);
    });
}

function AdicionarFornecedor(fornecedor) {
  axios
    .post(`/AddProductSupplier/${props.product.id}/${fornecedor}`)
    .then((response) => {
      if (response.data.message) return message(response.data);
      props.product.fornecedor = response.data.fornecedor;
    })
    .catch((err) => {
      console.log(err);
    });
}

function search(event) {
  const filter = fornecedores.value.store.filter((item) => {
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
  fornecedores.value.data = filter;
}

const deleteSupplier = (supplier) => {
  axios
    .post(`/deleteSupplierProduct/${props.product.id}/${supplier}`)
    .then((response) => {
      props.product.fornecedor = response.data.fornecedor;
    })
    .catch((err) => {
      console.log(err);
    });
};
</script>

<style scoped lang="scss">
@import "../../../../assets/produtos/css/infoProducts/supplier.scss";
</style>
