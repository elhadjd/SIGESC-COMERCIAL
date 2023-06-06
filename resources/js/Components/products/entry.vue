<template>
  <Toast />
  <Dialog
    class="p-0"
    :header="props.dados.store.name + ' em Stock'"
    v-model:visible="displayPosition"
    :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
    :style="{ width: '50vw' }"
    :position="position"
    :modal="true"
  >
    <div class="w-100 h-auto bg-white">
      <form @submit.prevent.stop="Confirmar">
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
            <div class="w-50 p-1">{{ product.nome }}</div>
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
                <select @change="(e)=>addArmagen(e.target.value)" id="armagen">
                    <option v-for="item in armagens" :selected="form.armagen_id == item.id" :key="item.id">{{item.name}}</option>
                </select>
            </div>
          </div>
          <div class="d-flex w-100 mt-2">
            <div @click="$emit('fechar')" class="ms-1 fechar">Fechar</div>
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
import { useToast } from "primevue/usetoast";
import axios from "axios";
import { useStore } from "vuex";
const store = useStore()

const quantity = ref(0)

const toast = useToast();

const position = ref();

const displayPosition = ref();

const ShowModal = ref(false);

const armagens = ref([])

const emits = defineEmits(["Confirmar", "fechar"]);
const user = ref(store.state.publico.user);
const props = defineProps({
  dados: Object,
});
const product = ref([]);
const form = reactive({
  quantity: 0,
  armagen_id: null,
  produtos_id: product.value.id,
  movement_type: props.dados.store,
  motif: String(),
});

onMounted(async() => {
  displayPosition.value = true;
  position.value = "top";
  product.value = props.dados.product;
 await getArmagens()
  addArmagen(user.value.armagen.name)
});

const addArmagen = ((e)=>{

    let types = armagens.value.find((item) => String(item.name).trim() == String(e).trim() );
    if (types) {
        return sumQuantity(types)
    }
    quantity.value = 0

})

const sumQuantity = ((armagen)=>{
    form.armagen_id = armagen.id;
    const filter = armagen.stock.filter((item)=>{
        return item.produtos_id == product.value.id && item.armagen_id == form.armagen_id
    })
    if (filter[0]) {
        return quantity.value = filter[0].quantity
    }
    quantity.value = 0
})

const getArmagens = (async()=>{
   await axios.get('/getArmagens')
    .then((response) => {
        armagens.value = response.data.armagens
    }).catch((err) => {
        console.log(err);
    });
})


const Confirmar = () => {
  if (form.quantity != 0) {
    if (form.armagen_id == null) return message('seleciona um armagem para continuar','info')
    form.produtos_id = product.value.id;
    ShowModal.value = true;
    axios
      .post(`/updateQuantity/${form.produtos_id}`, form)
      .then((Response) => {
        if (!Response.data.message) {
          if (form.tipoOper == "Entrada") {
            product.value.qtd += form.quantity;
          } else {
            product.value.qtd -= form.quantity;
          }
          emits("fechar");
          emits("Confirmar");
          message("Operação concluida","success")
        } else {
          message(Response.data.message,"info")
        }
        ShowModal.value = false;
      })
      .catch((err) => {
        console.log(err);
      });
  } else {
    message("A campo quantidade não pode ficar vazio","info")
  }
};
const message = ((message,type)=>{
    toast.add({
      severity: type,
      summary: "menssagem",
      detail: message,
      life: 5000,
    });
})
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
