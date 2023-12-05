<template>
  <div class="Principal">
    <div class="sisgesc">
        <h1>Bem Vindo ao </h1>
        <h1><span>S</span>ISGESC</h1>
    </div>
    <div class="company">
        <span>{{company.name}}</span>
        <button><Link :href="route('dashboard')">Iniciar</Link></button>
    </div>
  </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3"
import { onMounted } from "vue";
import { useStore } from "vuex";
const store = useStore()
const props = defineProps({
    company: Object,
})

onMounted(()=>{
    localStorage.clear()
})

</script>

<style lang="scss" scoped>
@import '../../../assets/Start/welcome';
</style>