<template>
  <div>
    <Modal :SmsConfirm="SmsConfirm" @Confirme="Arquivado" @descartou="StateModal = false" v-if="StateModal"/>
    <section class="agrupar">
        <span @click="state = !state" class="dropdown-toggle w-20">Ação</span>
        <div>
            <div v-if="state" class="listGroup">
                <span @click="deleteProduct">Apagar</span>
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
import { computed, ref } from "@vue/runtime-core";
import { useToast } from "primevue/usetoast";
import { useStore } from "vuex";
import { serviceMessage } from "@/composable/public/messages";
const store = useStore()
const SmsConfirm = ref()
const StateModal = ref(false)
const product = computed(()=>store.getters['Product/product'])
const emits = defineEmits(['Voltar'])
const state = ref(false)
const {showMessage} = serviceMessage()
const deleteProduct = (()=>{
    if (product.value.data.qtd>0) {
        showMessage('Este produto não pode ser deletado','info');
    } else {
        StateModal.value = true
        SmsConfirm.value = 'Apagar'
    }
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
                id: product.value.data.id,
            },
        })
        .then((response) => {
            product.value.data = response.data.produto_arquivado
            showMessage('Produto arquivado com sucesso','info');
            StateModal.value = false
        })
        .catch((erro) => {
            console.log(erro);
        });
    } else {
        axios.delete(`/deleteProduct/${product.value.data.id}`)
        .then((Response) => {
            showMessage('Deletado com successo','success')
            StateModal.value = false;
            emits('Voltar')
        })
        .catch((err) => {console.log(err);});
    }
})
</script>

<style lang="scss" scoped>
@include dropList;
.agrupar{
    >span{
        display: flex;
        justify-content: center !important;
        width: 100px !important;
    }
    @media screen and (max-width: 500px) {
        position: absolute;
        left: 50%;
        right: 50%;
    }
}
</style>
