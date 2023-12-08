<template>
  <div>
    <div class="Titulo"><strong>{{$t('words.price')+'s'}}</strong></div>
    <div class="FormPreco">
      <div class="informacoa_prod">
        <div>{{$t('phrases.costPrice')}}</div>
        <div>{{$t('phrases.salePrice')}}</div>
      </div>

      <div id="inputs_informacao">
        <div>
          <input
            type="text"
            ref="inputRef"
            :disabled="product.data.estado === 'inactive'"
            v-model="product.data.preçocust"
            class="input_info"
            :placeholder="$t('phrases.costPrice')"
          />
        </div>
        <div>
          <input
            type="text"
            ref="inputRef"
            :disabled="product.data.estado === 'inactive'"
            v-model="product.data.preçovenda"
            class="input_info"
            :placeholder="$t('phrases.salePrice')"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { StoreProduct } from "@/types/product"
import { computed, onMounted, ref } from "vue"
import { useStore } from "vuex"
const store = useStore()
const product = computed<StoreProduct>(()=> store.getters['Product/product'])

</script>

<style scoped lang="scss">
@import "../../../../assets/produtos/css/infoProducts/prices.scss";
</style>
