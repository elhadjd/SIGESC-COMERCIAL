<template>
	<div class="MenuFaturacao bg-white">
		<Home />
        
	</div>
</template>
<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import Home from '../Components/Invoice/Home.vue'
import ModalConfirm from "../components/Invoice/ModalConfirm.vue";
</script>

<style scoped>
* {
	margin: 0;
	padding: 0;
}
.MenuFaturacao {
	width: 100vw;
	height: 100vh;
}
</style>