<template>
  <div class="Principal">
    <div class="Modal h-96">
        <div class="Header">
            <strong>Confirmation</strong>
        </div>
        <div class="Container">
            {{'Deseja realmente '+message+' ?'}}
        </div>
        <div class="Footer flex flex-row">
            <button @click.prevent.stop="$emit('Confirme',message,'confirmou')" type="button">{{$t('words.confirm')}}</button>
            <button type="button" class="Descartar" @click="$emit('descartou')">{{$t('words.close')}}</button>
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
