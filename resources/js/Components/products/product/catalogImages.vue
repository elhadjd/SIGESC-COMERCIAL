<template>

  <div class="Principal w-auto cursor-pointer h-full flex justify-center">
    
    <div class="w-96 h-full rounded p-2 catalogButton flex justify-center items-center">
        <svg @click="product.stateModalCatalog = true" width="100px" class="icon" height="90px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.2647 15.9377L12.5473 14.2346C11.758 13.4519 11.3633 13.0605 10.9089 12.9137C10.5092 12.7845 10.079 12.7845 9.67922 12.9137C9.22485 13.0605 8.83017 13.4519 8.04082 14.2346L4.04193 18.2622M14.2647 15.9377L14.606 15.5991C15.412 14.7999 15.8149 14.4003 16.2773 14.2545C16.6839 14.1262 17.1208 14.1312 17.5244 14.2688C17.9832 14.4253 18.3769 14.834 19.1642 15.6515L20 16.5001M14.2647 15.9377L18.22 19.9628M18.22 19.9628C17.8703 20 17.4213 20 16.8 20H7.2C6.07989 20 5.51984 20 5.09202 19.782C4.7157 19.5903 4.40973 19.2843 4.21799 18.908C4.12583 18.7271 4.07264 18.5226 4.04193 18.2622M18.22 19.9628C18.5007 19.9329 18.7175 19.8791 18.908 19.782C19.2843 19.5903 19.5903 19.2843 19.782 18.908C20 18.4802 20 17.9201 20 16.8V13M11 4H7.2C6.07989 4 5.51984 4 5.09202 4.21799C4.7157 4.40973 4.40973 4.71569 4.21799 5.09202C4 5.51984 4 6.0799 4 7.2V16.8C4 17.4466 4 17.9066 4.04193 18.2622M18 9V6M18 6V3M18 6H21M18 6H15" stroke="#00a5cf" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
    <div v-if="product.stateModalCatalog" class="Catalog flex items-center" >
        <div class="bg-white bg-red-500 flex flex-col relative h-auto md:w-2/3 w-full rounded">
            <span class="closeModal absolute ml-2 rounded-full flex justify-center items-center" @click="product.stateModalCatalog = false">x</span>
            <div class="h-28 bg-white rounded flex border-bottom p-3 justify-center items-center">
                <label for="imageCAtegory" class="flex justify-center h-full p-2 w-40 border border-gray-100 rounded-sm">
                    <FontAwesomeIcon v-if="progress" icon="fa-solid fa-spinner" class="text-6xl text" style="color: #00a6cf17" shake />
                    <FontAwesomeIcon v-else icon="fa-solid fa-file-circle-plus" class="text-6xl text" style="color: #00a6cf17" beat-fade/>
                </label>
                <input :type="StateModalConfirm.state || progress ? 'text' : 'file'" @change="onImageChange" id="imageCAtegory">
            </div>
            <div class="flex h-48 flex-row space-x-2 p-3 overflow-x-auto ">
                <span class="relative images border rounded p-1 border-gray-100" v-for="image in product.data.catalog_product" :key="image.id">
                    <span class="absolute ml-2 rounded-full flex justify-center items-center" @click="()=>confirmDelete(image.id)">x</span>
                    <img class="flex h-full rounded" :src="`/produtos/image/${image.product_id}/${image.image}`">
                </span>
                <Modal :SmsConfirm="SmsConfirm" @Confirme="deleteImage" @descartou="StateModalConfirm.state = false" v-if="StateModalConfirm.state"/>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Modal from '../../confirmation/index.vue'
import {useUploadCatalogProduct} from '@/composable/public/uploadCatalogProduct'
import { VueCarousel } from 'vue-carousel';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { StoreProduct } from "@/types/product";
import { computed, onMounted, ref } from "vue";
import ButtonVue from '@/ui/button.vue'
import { useStore } from "vuex";
const store = useStore()
const product = computed<StoreProduct>(()=>store.getters['Product/product'])

const {onImageChange,progress,deleteImage,StateModalConfirm,confirmDelete,} = useUploadCatalogProduct()
const SmsConfirm = ref('eliminar esta imagen')

</script>

<style lang="scss" scoped>
.catalogButton{
    background-color: var(--buttons-hover);
    &:hover{
        background-color: #00a6cf3b;
    }
}
.Principal{
    z-index: 0 !important;
    .Catalog{
        @include modal;
        z-index: 0 !important;
        cursor: auto !important;
        .closeModal{
            width: 30px;
            height: 30px;
            top: 0;
            right: 0;
            font: 16pt arial;
            font-weight: 600;
            cursor: pointer;
            color: var(--bgButtons);
            &:hover{
                background-color: var(--bgButtons);
                color: white;
            }
        }
        input{
            display: none !important;
        }
        .images{
            >span{
                width: 30px;
                height: 30px;
                top: 0;
                right: 0;
                font: 16pt arial;
                font-weight: 600;
                cursor: pointer;
                color: var(--bgButtons);
                &:hover{
                    background-color: var(--bgButtons);
                    color: white;
                }
            }
        }
    }
}

</style>
