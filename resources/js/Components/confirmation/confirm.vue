<template>
  <div>
    <Modal :SmsConfirm="SmsConfirm" @Confirme="Arquivado" @descartou="StateModal = false" v-if="StateModal"/>
    <section class="agrupar">
        <span @click="state = !state" class="dropdown-toggle">Agrupar</span>
        <div>
            <div v-if="state" class="listGroup">
                <span @click="deleteProduct">Apagar</span>
                <!-- <span>Arquivar</span> -->
            </div>
        </div>
    </section>
  </div>
  <Toast/>
</template>

<script setup>
import { ChevronDownIcon } from "@heroicons/vue/20/solid";
import Toast from "primevue/toast";
import axios from "axios";
import Modal from '../confirmation/index.vue'
import { ref } from "@vue/runtime-core";
import { useToast } from "primevue/usetoast";
const props = defineProps({
    product: Object
})
const toast = useToast()
const SmsConfirm = ref()
const StateModal = ref(false)
const Product = ref(props.product)
const emits = defineEmits(['produto','message'])
const state = ref(false)

const deleteProduct = (()=>{
    console.log(Product.value);
    if (Product.value.qtd>0) {
        message('Este produto não pode ser deletado','info');
    } else {
        StateModal.value = true
        SmsConfirm.value = 'Apagar'
    }
})

const message = ((message,tipo)=>{
    toast.add({
        severity: tipo,
        summary: 'Message',
        detail: message,
        life: 5000
    })
})

const Arquivar = (()=>{
    StateModal.value = true
    SmsConfirm.value = 'Arquivar'
})

const Arquivado = (()=>{
    if (SmsConfirm.value == 'Arquivar') {
        axios({
            method: "PATCH",
            url: "/ArquivarProduto",
            data: {
                id: Product.value.id,
            },
        })
        .then((response) => {
            Product.value = response.data.produto_arquivado
            message('Produto arquivado com sucesso','info');
            emits('produto',response.data.produto_arquivado);
            StateModal.value = false
        })
        .catch((erro) => {
            console.log(erro);
        });
    } else {
        axios.delete(`/deleteProduct/${Product.value.id}`)
        .then((Response) => {
            message('Deletado com successo','success')
            StateModal.value = false;
            emits('Voltar')
        })
        .catch((err) => {console.log(err);});
    }
})
</script>

<style lang="scss" scoped>
@include dropList;
</style>
