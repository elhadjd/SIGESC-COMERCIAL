<template>
    <div class="principal-modal">
        <div class="Modal">
            <div class="Header">
                <h1>Senha</h1>
            </div>
            <div class="Container">
                <input type="password" v-model="senha" placeholder="Digite a senha">
            </div>
            <div class="Footer">
                <button class="Descartar" @click="$emit('fecharModal')">Descartar</button>
                <button @click="guardar">Guardar</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "@vue/runtime-core"
import axios from "axios"

const senha = ref()

const emits = defineEmits(['message','fecharModal'])

const props = defineProps({id: Number})

const guardar = (()=>{
    axios.post(`/PasswordCash/${props.id}`,{senha:senha.value})
    .then((Response) => {
        emits('message',Response.data.tipo,Response.data.messagem)
        emits('fecharModal')
    }).catch((err) => {
        console.log(err);
    });
})
</script>

<style scoped lang="scss">
.principal-modal{
    @include modal;
}
</style>
