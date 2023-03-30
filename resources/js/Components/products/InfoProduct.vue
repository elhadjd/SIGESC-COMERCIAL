<template>
  <div class="BottomProd">
    <Progress v-if="$store.state.StateProgress" />
    <div class="botoes_do_meio">
      <div
        :class="props.produto.tipo == 'precos' ? 'core' : ''"
        @click="Tipoinfos('precos')"
      >
        Informação
      </div>

      <div
        :class="props.produto.tipo == 'fornecedore' ? 'core' : ''"
        @click="Tipoinfos('fornecedore')"
      >
        Fornecedore
      </div>
      <div
        :class="props.produto.tipo == 'ListaPreco' ? 'core' : ''"
        @click="Tipoinfos('ListaPreco')"
      >
        Lista de preço
      </div>
    </div>
    <div class="information_prod">
      <div class="informacao">
        <prices :product="product" @prices="updatePrice" v-if="props.produto.tipo == 'precos'"/>
        <supplier :product="product" v-else-if="props.produto.tipo == 'fornecedore'"/>
        <listPrice :product="product" v-else/>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import prices from './infoProduct/prices.vue'
import supplier from './infoProduct/supplier.vue'
import listPrice from './infoProduct/listPrice.vue'
import { useStore } from "vuex";
import Progress from "@/components/confirmation/progress.vue";
import { useCurrencyInput } from "vue-currency-input";

const { inputRef } = useCurrencyInput({ currency: "AOA" });
const store = useStore();
const emits = defineEmits(['prices'])
const props = defineProps({
  produto: Object,
});
const product = ref(props.produto.produtos);

const FormetDineiro = new Intl.NumberFormat("pt-AO", {
  style: "currency",
  currency: "AOA",
});

const updatePrice = ((type,value)=>{
    emits('prices',type,value)
})

const Tipoinfos = (event) => {
  props.produto.tipo = event;
};

const message = ((data)=>{

})

</script>

<style scoped lang="scss">
@import "../../../assets/produtos/css/informacao.scss";
</style>