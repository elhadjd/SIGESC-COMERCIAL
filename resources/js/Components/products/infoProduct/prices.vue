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
            :disabled="product.estado === 1"
            v-model="product.preçocust"
            @keyup="$emit('prices', 'preçocust', product.preçocust)"
            class="input_info"
            placeholder="Preço de custo exemplo"
          />
        </div>
        <div>
          <input
            type="text"
            ref="inputRef"
            :disabled="product.estado === 1"
            v-model="product.preco_medio"
            @keyup="$emit('Precos', 'preco_medio', product.preco_medio)"
            class="input_info"
            placeholder="Preço medio exemplo "
          />
        </div>
        <div>
          <input
            type="text"
            ref="inputRef"
            :disabled="product.estado === 1"
            v-model="product.preçovenda"
            @keyup="$emit('prices', 'preçovenda', product.preçovenda)"
            class="input_info"
            placeholder="Preço de venda exemplo"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue"

const props = defineProps({
    product: {
        default: ()=> Object
    }
})

const product = ref(props.product);
</script>

<style scoped lang="scss">
@import "../../../../assets/produtos/css/infoProducts/prices.scss";
</style>
