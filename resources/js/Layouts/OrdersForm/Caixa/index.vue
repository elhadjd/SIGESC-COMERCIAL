<template>
   <div class="Principal">
      <div class="Modal">
         <div class="Header">
            <h1>{{item.product.nome}}</h1>
         </div>
         <form @submit.prevent.stop="FormSubmit">
         <div class="Container">
            <div class="Preco">
               <input type="text" ref="inputRef" class="p-2" v-model="box.price" :placeholder="`${$t('words.price')} ${$t('words.of')} ${$t('words.box')}`" />
               <input
                  v-if="item.product.spent"
                  type="text"
                  placeholder="Gasto da caixa"
                  v-model="box.spent"
                  />
            </div>
            <div class="Quantidades">
               <input type="number" v-model="box.total" :placeholder="$t('words.quantity')" />
               <input type="number" v-model="box.quantity" placeholder="Quantidade da caixa" />
            </div>
         </div>
         <div class="Footer">
            <button class="Descartar" @click="$emit('close')">{{$t('words.close')}}</button>
            <button type="submit">{{$t('words.confirm')}}</button>
         </div>
         </form>
      </div>
   </div>
</template>
<script setup>
import { onMounted, reactive, ref } from "@vue/runtime-core";
import { useCurrencyInput } from "vue-currency-input";

const { inputRef} = useCurrencyInput({
   currency: "AOA"
});

const emits = defineEmits(['message', 'confirm','close']);

const props = defineProps({
   item: Object
});

const box = reactive({
   total: null,
   price: null,
   quantity: null,
   spent: 0,
   result: {
      quantityFinal: null,
      PriceUnity: null,
      spent: null,
      id: props.item.id
   }
});

const FormSubmit = () => {
   if (box.total == null || box.price == null || box.quantity == null ) {
      emits("message", "Os campos não podem ser ser vázio", "info");
   } else {
      box.result.PriceUnity = box.price / box.quantity;
      box.result.quantityFinal = box.quantity * box.total;
      box.result.spent = box.spent;
      emits("confirm", box.result);
   }
};

</script>

<style lang="scss" scoped>
.Principal {
	@include modal;
	align-items: center;
	.Header {
		h1 {
			font-size: 20px !important;
		}
	}
	.Container {
		flex-direction: column;
        gap: 1rem;
		.Preco {
			width: 100% !important;
			display: flex;
			justify-content: center;
			gap: 1rem;
			input {
                @include input_normal;
                padding: 4px !important;
				width: 92% !important;
			}
		}
		.Quantidades {
			display: flex;
			justify-content: space-between;
		}
	}
}
</style>
