<template>
  <div class="listPrice">
    <div class="AddListPrice">
      <form @submit.prevent.stop="form.id ? updateListPrice() : AddListPrice()">
        <div class="formPrice">
          <label for="qtd">Quantidade:</label>
          <input
            v-model="form.quantity"
            type="text"
            name=""
            id="qtd"
            placeholder="quantidade"
          />
        </div>
        <div class="formPrice">
          <label for="price">Preço:</label>
          <input
            v-model="form.price_discount"
            type="text"
            name=""
            id="price"
            placeholder="preço"
          />
        </div>

        <div class="buttton">
            <FontAwesomeIcon @click="form = list_price" v-if="form.id" icon="fa-solid fa-rotate-right" />
            <button type="submit">{{form.id ? "Atualizar" :"Adicionar"}}</button>
        </div>

      </form>
    </div>
    <div class="prices">
      <div 
        v-for="item in list_price" :key="item.id"
         @click="form = item"
      >
        <span>{{item.quantity + ',00Un(s)'}}</span>
        <span>{{formatMoney(item.price_discount) + 'kz'}}</span>
        <FontAwesomeIcon @click="deleteListPrice(item.id)" icon="fa-solid fa-delete-left" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import axios from "axios";
import { computed, onMounted, reactive, ref } from "vue";
import { useStore } from "vuex";

const store = useStore();

const company = computed(()=> store.state.Empresa);
const user = computed(()=> store.state.user);

const props = defineProps({
    product:{
        default: () => Object
    }
});

const list_price = ref([])

const form = ref({
  id: null,
  quantity: null,
  price_discount: null,
  user_id: user.value.id,
  company_id: company.value.id,
  produtos_id: props.product.id
});


onMounted(()=>{
  getProduct()
})

function getProduct() {
    axios.get(`/products/${props.product.id}`).then((response)=>{
        list_price.value = response.data.product.list_price
    });
}

function AddListPrice() {
  axios
    .post("AddListPrice", { ...form.value })
    .then((response) => {
      list_price.value = response.data.data.list_price
    })
    .catch((err) => {
      console.log(err);
    });
}

function updateListPrice() {
    axios.post(`updateListPrice/${form.value.id}/${props.product.id}`,{...form.value})
    .then((response) => {
        list_price.value = response.data.list_price
    }).catch((err) => {
        console.log(err);
    });
}

function deleteListPrice(id) {
 axios
    .put(`DeleteListPrice/${id}/${props.product.id}`)
    .then((response) => {
      list_price.value = response.data.data.list_price
    })
    .catch((err) => {
      console.log(err);
    });
}
</script>

<style scoped lang="scss">
@import "../../../../assets/produtos/css/infoProducts/listPrice.scss";
</style>