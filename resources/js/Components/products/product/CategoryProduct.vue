<template>
  <div class="Principal">
    <form @submit.prevent.stop="saveData" class="Modal">
        <div class="Header">
            <h2>{{$t('words.new')+ ' ' +$t('words.category')}}</h2>
            <div class="ProductsSingleCategoria">
                <i class="fa fa-product-hunt"></i>
                <span>{{$t('words.article')}}</span>
            </div>
        </div>
        <div class="Container">
            <input type="text" v-model="data.name" :placeholder="`${$t('words.name')} ${$t('words.of')} ${$t('words.category')}`">
        </div>
        <div class="Footer">
            <button type="button" @click.prevent.stop="$emit('closeModal')" class="Descartar">{{$t('words.close')}}</button>
            <button type="submit">{{$t('words.save')}}</button>
        </div>
    </form>
  </div>
</template>

<script setup>
import { computed, reactive } from "@vue/runtime-core";
import axios from "axios";
import { serviceMessage } from "@/composable/public/messages";
import { useStore } from "vuex";
const store = useStore()
const {showMessage} = serviceMessage()
const product = computed(()=> store.getters['Product/product'])
const emit = defineEmits(['closeModal']);
const props = defineProps({
    category:Object
});

const data = reactive({
    name: null,
})

const saveData = (async()=>{
    if(data.nome === null || data.nome === '') return showMessage('Nome obligatorio','info')
    await axios.post(`/saveCategory/${props.category.idProduct}`,data).then((response) => {
        product.value.data.category = response.data.data
        showMessage(response.data.message,response.data.type)
        emit('closeModal');
    }).catch((erro) => {
        console.log(erro)
    });
})
</script>

<style lang="scss" scoped>
    .Principal{
        @include modal;
        background-color: rgba(0, 0, 0, 0.46) !important;
        align-items: center !important;
        .Modal{
            .Header{
                padding: 0px !important;
                justify-content: space-between;
                h2{
                    padding: 10px;
                }
                .ProductsSingleCategoria{
                    width: 120px;
                    height: 100%;
                    padding: 10px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-left: 1px solid #ddd;
                    color: #017e84;
                    &:hover{
                        cursor: pointer;
                        background-color: #017e8444;
                        color: #017e84;
                    }
                }
            }
            .Container{
                input{
                    @include InputPrincipal;
                    width: 100% !important;
                }
            }
        }

    }
</style>
