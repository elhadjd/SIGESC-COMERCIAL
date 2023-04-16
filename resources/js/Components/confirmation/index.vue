<template>
  <div class="Principal">
    <div class="Modal">
        <div class="Header">
            <strong>Confirmação</strong>
        </div>
        <div class="Container">
            {{'Deseja realmente '+message+' ?'}}
        </div>
        <div class="Footer">
            <button @click="$emit('Confirme',message,'confirmou')">Confirmar</button>
            <button class="Descartar" @click="$emit('descartou')">Fechar</button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted ,ref } from "@vue/runtime-core"

const props = defineProps({SmsConfirm: String})

const message = ref()
onMounted(()=>{
    message.value = props.SmsConfirm
})
</script>

<style lang="scss" scoped>
.Principal{
    @include modal
}
</style>
