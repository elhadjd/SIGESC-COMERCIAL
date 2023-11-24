<template>
    <div v-for="button , index in buttonsData" :key="index">
        <ButtonVue @click="()=>{button.ClickFunction(),stateButtons = !stateButtons}" :type="button.type" :className="button.className" >
            {{button.name}}
            <i v-if="loading == 1" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
        </ButtonVue>
    </div>
</template>

<script setup>
import ButtonVue from '@/ui/button.vue'
import { ProductServices } from '../services/product/productServices'
import {ButtonsServices} from '../services/product/buttons'
import { onMounted, ref, watch } from 'vue'
const emits = defineEmits(['closeForm','saved'])
const {
    loading,
    product,
    StateEditProd,
} = ProductServices()
const {buttonsData,productStore} = ButtonsServices(emits)
const stateButtons = ref(false)
</script>

<style lang="scss">
@import "../../../../assets/produtos/css/produto";
</style>
