<template>
  <div>
    <div class="Titulo"><strong>Preços</strong></div>
    <div class="FormPreco">
      <div class="informacoa_prod">
        <div>Preço de custo</div>
        <div>Preço medio</div>
        <div>Preço de venda</div>
      </div>

      <div id="inputs_informacao">
        <div>
          <input
            type="text"
            ref="inputRef"
            :disabled="product.data.estado === 'inactive'"
            v-model="product.data.preçocust"
            class="input_info"
            :placeholder="`Preço de custo exemplo ${formatMoney(1)}`"
          />
        </div>
        <div>
          <input
            type="text"
            ref="inputRef"
            :disabled="product.data.estado === 'inactive'"
            v-model="product.data.preco_medio"
            class="input_info"
            :placeholder="`Preço medio exemplo ${formatMoney(1)}`"
          />
        </div>
        <div>
          <input
            type="text"
            ref="inputRef"
            :disabled="product.data.estado === 'inactive'"
            v-model="product.data.preçovenda"
            class="input_info"
            :placeholder="`Preço de venda exemplo ${formatMoney(1)}`"
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
