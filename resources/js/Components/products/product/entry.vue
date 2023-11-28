<template>
  <Dialog
    class="p-0"
    :header="props.data.store.name + ' em Stock'"
    v-model:visible="displayPosition"
    :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
    :style="{ width: '50vw' }"
    :position="modalPosition"
    :modal="true"
  >
    <div class="w-100 h-auto bg-white">
      <form @submit.prevent.stop="moveStock">
        <div class="p-2">
          <div class="w-100 p-1 d-flex text-secondary h5">
            <label for="" class="w-50"><strong>Artigo</strong></label>
            <label
              v-if="form.tipoOper != 'Entrada'"
              for=""
              class="w-50 text-center"
              ><strong>Motivo</strong></label
            >
          </div>
          <div class="d-flex">
            <div class="w-50 p-1">{{ product.data.nome }}</div>
            <textarea
              class="InputMotif"
              v-if="form.tipoOper != 'Entrada'"
              type="text"
              v-model="form.motif"
            ></textarea>
          </div>
          <div class="d-flex mt-4 p-2 text-secondary w-100 border-bottom">
            <div class="w-50 d-flex">
              <div class="">
                <strong class="mx-2">{{ quantity + ",00Un(s)" }}</strong>
                <i class="fa fa-bar-chart"></i>
                <InputNumber
                class="quantidade py-0 ms-2 w-75"
                inputId="locale-user"
                v-model="form.quantity"
                mode="decimal"
                :minFractionDigits="2"
              />
              </div>
            </div>
            <div class="w-50 armagens">
                <label for="armagen">Armagens: </label>
                <select @change="(e)=>changeStore(e.target.value)" id="armagen">
                    <option v-for="item in stores" :selected="form.armagen_id == item.id" :key="item.id">{{item.name}}</option>
                </select>
            </div>
          </div>
          <div class="d-flex w-100 mt-2">
            <div @click="$emit('closeModal')" class="ms-1 fechar">Fechar</div>
            <div class="ms-1 ConfirmarStock">
              <div v-if="ShowModal">
                <i class="fa fa-refresh fa-spin fa-3x fa-fw"
                  aria-hidden="true"></i>
              </div>
              <button v-else>Confirmar</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </Dialog>
</template>

<script setup>
import InputNumber from "primevue/inputnumber";
import Dialog from "primevue/dialog";
import { onMounted, reactive, ref } from "@vue/runtime-core";
import { moveProductStockServices } from '../services/product/moveProductStockServices'
import { useStore } from "vuex";
const emits = defineEmits(["closeModal"]);
const store = useStore()
const user = ref(store.state.publico.user);
const props = defineProps({
  data: Object,
});
const {
    moveStock,
    modalPosition,
    quantity,
    displayPosition,
    form,
    product,
    getStores,
    changeStore,
    ShowModal,
    stores
} = moveProductStockServices(props,emits)
 onMounted(async() => {
    displayPosition.value = true;
    modalPosition.value = "top";
    product.value = props.data.product;
    form.tipoOper = props.data.store.name
    await getStores()
    changeStore(user.value.armagen.name)
});
</script>

<style scoped lang="scss">
*::before,
*::after {
  box-sizing: border-box;
}
.p-dialog-content {
  padding: 0px !important;
}
.p-dialog-header {
  padding: 0px 0px 0px 10px !important;
}
.fechar {
  border: 1px solid white;
  color: #017e84;
  padding: 3px 10px 3px 10px;
  font: 10pt arial;
  font-weight: 600;
  &:hover {
    border: 1px solid #017e84;
    cursor: pointer;
  }
}
.ConfirmarStock {
  @include botao_normal;
  i {
    font-size: 15px;
  }
}
.quantidade {
  border: none !important;
  outline: none !important;
  border-bottom: 1px solid #017e84;
}
.InputMotif {
  width: 46% !important;
  height: 40px;
  max-height: 40px;
  border: none;
  outline: none !important;
  box-shadow: 0 0 0 0;
  border: 1px solid #017e84;
  padding: 5px;
  font-family: Tahoma, sans-serif;
  background-image: url(bg.gif);
  background-position: bottom right;
  background-repeat: no-repeat;
}
input,select {
  @include input_normal
}
.armagens{
    display: grid;
    align-items: center;
    grid-template-columns: 40% 60%;
    select{
        width: 90%;
    }
}
</style>
