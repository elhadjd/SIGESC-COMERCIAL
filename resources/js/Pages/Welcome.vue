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

const props = defineProps({
    company: Object
})

</script>

<style lang="scss" scoped>
@import '../../assets/Start/welcome';
</style>