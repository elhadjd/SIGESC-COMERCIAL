<template >
  <div class="BottomProd">
    <Progress v-if="$store.state.StateProgress" />
    <div class="botoes_do_meio">
      <div :class="steps == 0 && 'core'" class="px-2 py-2 md:px-3 truncate" @click="changeStep(0)">
        {{$t('words.notice')}}
      </div>
      <div :class="steps == 1 && 'core'" class="px-2 py-2 truncate" @click="changeStep(1)">
        {{$t('words.provider')}}
      </div>
      <div :class="steps == 2 && 'core'" class="px-2 truncate py-2" @click="changeStep(2)">
        {{`${$t('words.list')} ${$t('words.of')} ${$t('words.price')}`}}
      </div>
      <div :class="steps == 3 && 'core'" class="px-2 py-2 truncate" @click="changeStep(3)">
        {{`${$t('words.discount')}`}}
      </div>
    </div>
    <div class="information_prod">
      <div class="informacao">
        <prices v-if="steps == 0"/>
        <supplier v-else-if="steps == 1"/>
        <listPrice v-else-if="steps == 2"/>
        <product-discount v-else/>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "@vue/runtime-core";
import prices from '../infoProduct/prices.vue'
import supplier from '../infoProduct/supplier.vue'
import listPrice from '../infoProduct/listPrice.vue'
import Progress from "@/components/confirmation/progress.vue";
import productDiscount from "../infoProduct/productDiscount.vue"
const steps = ref(0)
const changeStep = ((step)=>{
    steps.value = step
})
</script>

<style scoped lang="scss">
@import "../../../../assets/produtos/css/informacao.scss";
</style>
