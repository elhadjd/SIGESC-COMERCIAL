<template>
   <div class="Principal">
      <div class="Modal">
         <div class="Header">
            <h2>Atuazar a Senha</h2>
         </div>
         <div class="Container">
            <div>
               <label for="SenhaAtiga">Senha Atual</label>
               <input v-model="password.SenhaAtual" type="password" id="SenhaAtiga">
            </div>
            <div class="ms-2">
               <label for="NovaSenha">Nova Senha</label>
               <input v-model="password.NovaSenha" type="password" id="NovaSenha">
            </div>
         </div>
         <div class="Footer">
            <button @click="$emit('FecharModal')" class="Descartar">Fechar</button>
            <button @click="Atualizar">Atualizar</button>
         </div>
      </div>
   </div>
</template>

<script setup>
import { ref } from "@vue/runtime-core";
import axios from "axios";

const emits = defineEmits(['message','FecharModal'])

const props = defineProps({
    user: Object
})

const password = ref({
   SenhaAtual: null,
   NovaSenha: null
})

const Atualizar = (() => {
   if (password.value.SenhaAtual == "" || password.value.SenhaAtual == null || password.value.NovaSenha == "" || password.value.NovaSenha == null) {
      emits('message', 'Os campos não podem ser vázios', 'info')
   } else {
      if (password.value.NovaSenha.length <= 5) {
         emits('message', 'A senha deve ser mair do que 5 digitos', 'info')
      } else {
         const regex = /^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%*()_+^&}{:;?.])(?:([0-9a-zA-Z!@#$%;*(){}_+^&])(?!\1)){6,}$/;
         if (regex.test(password.value.NovaSenha)) {
            return UpdatePassword(password.value)
         } else {
            emits('message', 'Formato de senha invalida', 'info')
         }
      }
   }
})

const UpdatePassword = ((password) => {
   axios.post(`/UpdatePassword/${props.user.id}`, password)
      .then((Response) => {
         if (Response.data.tipo == 'success') {
            emits('FecharModal');
         }
         emits('message', Response.data.message, Response.data.tipo)
      }).catch((err) => {
         console.log(err);
      });
})
</script>

<style scoped lang="scss">
    .Principal{
        @include modal;
    }
</style>