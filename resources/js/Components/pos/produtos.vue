<template>
  <Transition name="bounce">
    <InfoProduct
      v-if="OpenInfo"
      :Information="Information"
      @CloseInfo="OpenInfo = false"
    />
  </Transition>
  <div class="Artigos">
    <label
      :class="idProd == product.id ? { shake: disabled } : ''"
      v-for="product in produtos.slice(0,100)"
      :key="product.id"
      class="formArtigo"
    >
      <div style="height: 122px; width: 125px; padding: 5px, 5px, 5px, 5px">
        <div>
          <div class="PrecoProd">
            <div class="iconesBloco">
              <i
                class="fa fa-exclamation-circle"
                style="color: red; margin-right: 5px"
                v-if="product.stock_sum_quantity <= 0"
                aria-hidden="true"
              ></i>
              <i class="fa fa-info-circle" @click="ShwInfo(product)"></i>
            </div>
            <div id="ProdutoPreco">
              {{ FormatarDineiro.format(product.preçovenda) }}
            </div>
          </div>
          <div class="ProdutoImagem">
            <img
              @click="AddProd(product)"
              :src="'/produtos/image/' + product.image"
              :alt="product.image"
              srcset=""
            />
          </div>
          <div class="ProdutoNome">{{ product.nome }}</div>
        </div>
      </div>
    </label>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "@vue/runtime-core";
import { useToast } from "primevue/usetoast";
import { mapMutations, mapState, useStore } from "vuex";
import Toast from "primevue/toast";
import InfoProduct from "./InfoProduct.vue";
import axios from "axios";
const produtos = ref([]);
const toast = useToast();
const Information = ref();
const disabled = ref(false);
const idProd = ref();
const OpenInfo = ref();
const store = useStore();
const emits = defineEmits(["AddProds", "message"]);

const FormatarDineiro = Intl.NumberFormat("PT-AO", {
  style: "currency",
  currency: "AOA",
});

const PesquisarProduto = computed(() => {
  return store.state.PesquisarProduto;
});

const ShwInfo = (event) => {
  Information.value = event;
  OpenInfo.value = true;
};

const OnMounted = onMounted(() => {
  axios.get('/products/1500/1')
    .then((Response) => {
    localStorage.setItem("produtos", JSON.stringify(Response.data.data));
    produtos.value = Response.data.data;
  });
});

const AddProd = (produto) => {
  if (Number(produto.preçovenda) >= produto.preçocust) {
    if (Number(produto.stock_sum_quantity) <= 0) {
      idProd.value = produto.id;
      disabled.value = true;
      setTimeout(() => {
        disabled.value = false;
      }, 1500);
      emits(
        "message",
        "error",
        "Atenção a quantidade deste produto ja não e suficiente !!!"
      );
    } else {
      emits("AddProds", produto);
    }
  } else {
    emits(
      "message",
      "error",
      "Atenção o preço de venda não pode ser menor do que preço de custo !!!"
    );
  }
};

const ProdutosPesquisa = ref([]);

watch(PesquisarProduto, (valor) => {
  ProdutosPesquisa.value = JSON.parse(localStorage.getItem("produtos"));
  if (valor != null || valor != "") {
    const prods = ProdutosPesquisa.value.filter((object) => {
      if (valor == object.codego) {
        return String(valor).toLowerCase() == object.codego;
      } else {
        return (
          String(object.nome).toLowerCase().includes(valor.toLowerCase(), 0) ||
          String(object.preçovenda)
            .toLowerCase()
            .includes(valor.toLowerCase(), 0)
        );
      }
    });
    if (prods.length == 1) {
      store.state.PesquisarProduto = null;
      AddProd(prods[0]);
    } else if (prods.length >= 1) {
      produtos.value = prods;
    } else if (prods.length == 0) {
      emits("message", "info", "Nemhum produto encontrado com este nome ");
    }
  }
});

const sumQuantity = ((product)=>{
    product.qtd = 0
    product.stock.forEach(item => {
        product.qtd += item.quantity
    });
    return product.qtd
})
</script>

<style lang="scss" scoped>
@import "../../../assets/Pos/css/produtos";
</style>
