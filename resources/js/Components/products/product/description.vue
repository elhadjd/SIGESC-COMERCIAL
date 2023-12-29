<template>
<div class="buttonPublish">
    <ButtonVue :className="'publish truncate relative text-start'" @click="state=true" :type="'button'">
        Add description
    </ButtonVue>
    <span class="rounded p-2 absolute bg-white text-gray-700" v-if="state">
        <textarea @blur="state = false" class="w-full border-0 outline-0 " v-model="product.data.description"></textarea>
    </span>
</div>
</template>

<script  setup lang="ts">
import ButtonVue from '@/ui/button.vue'
import { ProductServices } from '../services/product/productServices'
import { StoreProduct } from '@/types/product'
import { useStore } from 'vuex'
import { computed, ref } from 'vue'
import axios from 'axios'
const store = useStore()
const product = computed<StoreProduct>(()=> store.getters['Product/product'])
const state = ref<boolean>(false)
</script>

<style lang="scss" scoped>
.buttonPublish {
    position: relative;
    font-size: 14px;
    font-weight: 400;
    >span{
        width: 220px;
        z-index: 1080;
        top: 35px;
        right: 10px;
        box-shadow: var(--box-shadow);
    }
}
</style>
