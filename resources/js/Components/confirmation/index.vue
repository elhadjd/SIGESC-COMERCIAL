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
            <button @click.prevent.stop="$emit('Confirme',message,'confirmou')" type="button">Confirmar</button>
            <button type="button" class="Descartar" @click="$emit('descartou')">Fechar</button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted ,ref } from "@vue/runtime-core"

const props = defineProps({SmsConfirm: String})

const emits = defineEmits(['descartou','Confirme'])

const message = ref()
onMounted(()=>{
    message.value = props.SmsConfirm
})
</script>

<style lang="scss" scoped>
.Principal{
    z-index: 1080 !important;
    @include modal;
    .Modal{
        z-index: 1080 !important;
    }
}
</style>
