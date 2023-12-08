<template>
<div class="info-basic">
    <NewCategory
    v-if="categories.StateModal"
    :category="categories.category"
    @closeModal="categories.StateModal = false"
    />
    <div class="form-content">
        <div class="form-Control">
            <label for="category">{{$t('words.category')}}:</label>
            <button @click="changeStateDrop('category')" type="button" id="category">
                {{product.data.category != null ? product.data.category.name : `${$t('words.select')} ${$t('words.the')} ${$t('words.category')} ${$t('words.of')} ${$t('words.article')}`}}
            </button>
            <div v-if="stateDrop == 'category'" class="drop">
                <span v-for="item in categories.listCategories" :key="item.id" @click="AddCategoryObject(item)">{{item.name}}</span>
                <button type="button" @click="createCategory(product.data.id)">{{`${$t('words.create')} ${$t('words.a')} ${$t('words.new')} ${$t('words.category')}`}}</button>
            </div>
        </div>
        <div class="form-Control">
            <label for="productType">{{`${$t('words.type')} ${$t('words.of')} ${$t('words.category')}`}}:</label>
            <button @click="changeStateDrop('type')" type="button" id="productType"
            :disabled="product.data.estado === 1">
                {{product.data.product_type != null ? product.data.product_type.name : `${$t('words.select')} ${$t('words.the')} ${$t('words.type')} ${$t('words.article')}`}}
            </button>
            <div v-if="stateDrop == 'type'" class="drop">
                <span v-for="item in itemType" :key="item.id" @click="addTypeProduct(item)">{{item.name}}</span>
            </div>
        </div>
        <div class="form-Control">
            <label for="bare">{{`${$t('words.code')} ${$t('words.of')} ${$t('words.barre')}`}}:</label>
            <input type="text" v-model="product.data.codego" :disabled="product.data.estado === 1" id="bare" placeholder="bare code">
        </div>
    </div>
    <div class="form-content">
        <CatalogImagesVue/>
    </div>
</div>
</template>

<script setup>
import NewCategory from "./CategoryProduct.vue";
import { computed, onMounted } from "vue";
import { useStore } from "vuex";
import { ProductServices } from "../services/product/productServices";
import {BannerProductServices} from '../services/product/bannerProductServices'
import CatalogImagesVue from './catalogImages.vue'
const store = useStore()
const product = computed(()=> store.getters['Product/product'])
const emits = defineEmits([ "saved"]);
const props = defineProps({
    listCategories: Object,
    itemTypeProps: Object
})
const {
    itemType,
} = ProductServices(emits)
const {
    categories,
    createCategory,
    AddCategoryObject,
    stateDrop,
    addTypeProduct
} = BannerProductServices()

const changeStateDrop = (type)=>{
    if (stateDrop.value == type) return stateDrop.value = ''
    categories.listCategories = props.listCategories
    itemType.value = props.itemTypeProps
    stateDrop.value = type
}
</script>

<style lang="scss" scoped>
@import "../../../../assets/produtos/css/produto";
</style>
