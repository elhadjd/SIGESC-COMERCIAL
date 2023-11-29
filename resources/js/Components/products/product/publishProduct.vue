<template>
<div class="buttonPublish">
    <ButtonVue @mouseenter="()=>showInfo(true)" @mouseout="()=>showInfo(false)" :className="'publish relative'" @click="!product.data.shop_online && publishProductOnline()" :type="'button'">
        Divulgar
        <FontAwesomeIcon v-if="button.stateProgress" icon="fa-solid fa-spinner" shake />
        <FontAwesomeIcon icon="fa-solid fa-shop" v-else-if="!product.data.shop_online" beat-fade />
        <FontAwesomeIcon v-if="product.data.shop_online" icon="fa-solid fa-check" class="check" />
        <FontAwesomeIcon icon="fa-solid fa-bag-shopping" v-if="product.data.shop_online"/>
    </ButtonVue>
    <span class="rounded p-2 absolute bg-white text-gray-700" v-if="button.info.state">{{button.info.message}}</span>
</div>
</template>

<script  setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import ButtonVue from '@/ui/button.vue'
import { ProductServices } from '../services/product/productServices'
import { StoreProduct } from '@/types/product'
import { useStore } from 'vuex'
import { computed, ref } from 'vue'
import axios from 'axios'
import {usePublishProductService} from '../services/product/publishProductService'
import { serviceMessage } from '@/composable/public/messages'
const store = useStore()
const {showMessage} = serviceMessage()
const product = computed<StoreProduct>(()=> store.getters['Product/product'])
const {publishProductOnline,button,showInfo} = usePublishProductService()

</script>

<style lang="scss" scoped>
.buttonPublish {
    position: relative;
    >span{
        width: 300px;
        z-index: 1080;
        top: 35px;
        right: 10px;
        box-shadow: var(--box-shadow);
    }
}
</style>
