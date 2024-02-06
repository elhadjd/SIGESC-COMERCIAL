<template>
    <ConfirmVue v-if="stateModal" @descartou="stateModal = false" @Confirme="deleteDiscount" :SmsConfirm="'tirar o desconto deste produto '"/>
    <form @submit.prevent.stop="SaveData" class="Principal">
        <div class="formControl">
            <label for="discount">{{$t('words.discount')}}</label>
            <span class="relative flex">
                <input type="text" required v-model="form.discount" class="w-full" id="discount" :placeholder="$t('words.discount')">
                <span class="absolute">%</span>
            </span>
        </div>
        <div class="formControl">
            <label for="start">{{$t('words.startDate')}}</label>
            <input type="date" required v-model="form.startDate" id="start" :placeholder="$t('words.startDate')">
        </div>
        <div class="formControl">
            <label for="end">{{$t('words.endDate')}}</label>
            <input type="date" id="end" required v-model="form.endDate" :placeholder="$t('words.endDate')">
        </div>
        <div class="w-full space-x-2 p-2 flex justify-end">
            <button v-if="product.data.discount?.discount != 0" type="button" @click="stateModal = true" class="delete text-red-700">{{$t('words.delete')}}</button>
            <button type="submit" class="save">
                {{$t('words.save')}}
                <i v-if="loading" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
            </button>
        </div>
    </form>
</template>

<script lang="ts" setup >
import { serviceMessage } from "@/composable/public/messages";
import axios from "axios";
import moment from "moment";
import { computed, onMounted, ref } from "vue";
import { useStore } from "vuex";
import ConfirmVue from '../../confirmation/index.vue'
const store = useStore()
const product = computed(()=> store.getters['Product/product'])
const {showMessage}= serviceMessage()
const form = ref<{discount: number,startDate:string,endDate:string}>({
    discount: 0,
    endDate:'',
    startDate: '',
})
const stateModal = ref<boolean>(false)
const loading = ref<boolean>(false)
const mounted = onMounted(()=>{
    if(product.value.data.discount != null) form.value = product.value.data.discount
})

const SaveData = async() => {
    loading.value = true
    axios.post(`/addDiscountProduct/${product.value.data.id}`,form.value)
    .then((response) => {
        if(response.data.message) return showMessage(response.data.message,response.data.type)
         showMessage("Success","success")
        product.value.data = response.data
    }).catch((err) => {
        showMessage(err.response.data.message,"error")
        console.log(err);
    }).finally(()=>{
        loading.value = false
    });
}

const deleteDiscount = (async()=>{
    await axios.delete(`/deleteDiscount/${product.value.data.id}`)
    .then((response) => {
        showMessage("Success","success")
        if(response.data != "") product.value.data = response.data
}).catch((err) => {
        console.log(err);
    }).finally(()=>{
        stateModal.value = false
    });
})
</script>

<style lang="scss" scoped>
.Principal{
    .save{
        @include botao_normal;
    }.delete{
        @include botao_descartar
    }
    .formControl{
        >span{
            >span{
                right: 10px;
            }
        }
        @include form-control;
    }

}
</style>
