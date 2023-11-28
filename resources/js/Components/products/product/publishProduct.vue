<template>
<ButtonVue :className="'publish'" @click="publishProductOnline" :type="'button'">
    Divulgar
    <FontAwesomeIcon v-if="stateDrop == 'shopOnline'" icon="fa-solid fa-spinner" shake />
    <FontAwesomeIcon icon="fa-solid fa-shop" v-else beat-fade />
    <FontAwesomeIcon icon="fa-solid fa-check" class="check" />
</ButtonVue>
</template>

<script  setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import ButtonVue from '@/ui/button.vue'
import { ProductServices } from '../services/product/productServices'
import { StoreProduct } from '@/types/product'
import { useStore } from 'vuex'
import { computed } from 'vue'
const store = useStore()
const product = computed<StoreProduct>(()=> store.getters['Product/product'])
const {
    stateDrop,
    publishProductOnline,
} = ProductServices()
</script>

<style>

</style>
