<template>
  <div class="listPrice">
    <div class="AddListPrice">
      <form @submit.prevent.stop="form.id ? updateListPrice() : AddListPrice()">
        <div class="formPrice">
          <label for="qtd">{{$t('words.quantity')}}:</label>
          <input
            v-model="form.quantity"
            type="text"
            name=""
            id="qtd"
            :placeholder="$t('words.quantity')"
          />
        </div>
        <div class="formPrice">
          <label for="price">{{$t('words.price')}}:</label>
          <input
            v-model="form.price_discount"
            type="text"
            name=""
            id="price"
            :placeholder="$t('words.price')"
          />
        </div>

        <div class="buttton">
            <FontAwesomeIcon @click="form = list_price" v-if="form.id" icon="fa-solid fa-rotate-right" />
            <button type="submit">{{form.id ? $t('words.update') :$t('words.added')}}</button>
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
import { serviceMessage } from "@/composable/public/messages";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import axios from "axios";
import { computed, onMounted, reactive, ref } from "vue";
import { useStore } from "vuex";
const store = useStore();
const company = computed(()=> store.state.publico.company);
const user = computed(()=> store.state.publico.user);
const product = computed(()=> store.getters['Product/product'])
const list_price = ref([])
const {showMessage} = serviceMessage()
const form = ref({
  id: null,
  quantity: null,
  price_discount: null,
  user_id: user.value.id,
  company_id: company.value.id,
  produtos_id: product.value.data.id
});

onMounted(()=>{
  getProduct()
})

function getProduct() {
    axios.get(`/products/${product.value.data.id}`).then((response)=>{
        list_price.value = response.data.product.list_price
    });
}

async function AddListPrice() {
    await axios
    .post("/AddListPrice", { ...form.value })
    .then((response) => {
        showMessage(response.data.message,response.data.type)
        list_price.value = response.data.data.list_price
    })
    .catch((err) => {
        showMessage(err.response.data.message,'error')
        console.log(err);
    });
}

async function updateListPrice() {
    await axios.post(`/updateListPrice/${form.value.id}/${product.value.data.id}`,{...form.value})
    .then((response) => {
        list_price.value = response.data.list_price
    }).catch((err) => {
        console.log(err);
    });
}

async function deleteListPrice(id) {
 await axios
    .put(`/DeleteListPrice/${id}/${product.value.data.id}`)
    .then((response) => {
        showMessage(response.data.message,response.data.type)
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
