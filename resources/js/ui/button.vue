<template>
  <button :class="props.className" @click="$emit('click')" :type="props.type">
    <slot></slot>
  </button>
</template>

<script setup>
import { onMounted } from "vue"
const emits = defineEmits(['click'])
const props = defineProps({
    className: String,
    type: String
})
onMounted(()=>{
})
</script>
