<template>
  <div>
    <!-- <Modal :SmsConfirm="SmsConfirm" @Confirme="Arquivado" @descartou="StateModal = false" v-if="StateModal"/>
    <Menu as="div" class="relative inline-block mt-3 text-left">
      <div>
        <MenuButton
          class="
            inline-flex
            w-full
            p-0
            justify-center
            rounded-md
            border border-gray-300
            bg-white
            px-4
            py-2
            text-sm
            font-medium
            text-gray-700
            shadow-sm
            hover:bg-gray-50
            focus:outline-none
            focus:ring-2
            focus:ring-indigo-500
            focus:ring-offset-2
            focus:ring-offset-gray-100
          "
        >
          Action
          <ChevronDownIcon class="-mr-1 ml-2 h-5 w-5" aria-hidden="true" />
        </MenuButton>
      </div>

      <transition
        enter-active-class="transition ease-out duration-100"
        enter-from-class="transform opacity-0 scale-95"
        enter-to-class="transform opacity-100 scale-100"
        leave-active-class="transition ease-in duration-75"
        leave-from-class="transform opacity-100 scale-100"
        leave-to-class="transform opacity-0 scale-95"
      >
        <MenuItems
          class="
            absolute
            right-0
            z-10
            mt-2
            w-40
            origin-top-right
            rounded-md
            bg-white
            shadow-lg
            ring-1 ring-black ring-opacity-5
            focus:outline-none
          "
        >
          <div class="py-1">
            <MenuItem v-if="Product.estado == 1" v-slot="{ active }">
              <a
                href="#"
                @click="Arquivar"
                :class="[
                  active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                  'block px-4 py-2 text-sm',
                ]">Desarquivar</a
              >
            </MenuItem>

            <MenuItem v-else v-slot="{ active }">
              <a
                href="#"
                @click="Arquivar"
                :class="[
                  active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                  'block px-4 py-2 text-sm',
                ]"
                >Arquivar</a
              >
            </MenuItem>
            <MenuItem v-slot="{ active }">
              <a
                href="#"
                @click="ApagarProd"
                :class="[
                  active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                  'block px-4 py-2 text-sm',
                ]"
                >Apagar</a
              >
            </MenuItem>
          </div>
        </MenuItems>
      </transition>
    </Menu>
    <Toast /> -->

  </div>
</template>

<script setup>
import { ChevronDownIcon } from "@heroicons/vue/20/solid";
import ConfirmDialog from "primevue/confirmdialog";
import Button from "primevue/button";
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

const ApagarProd = (()=>{
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
        axios.delete("/deletarproduto", {
        params: { id: Product.value.id },
        })
        .then((Response) => {
            message('Deletado com successo','success')
            StateModal.value = false;
            emits('Voltar')
        })
        .catch((err) => {console.log(err);});
    }
})
</script>

<style>
</style>
