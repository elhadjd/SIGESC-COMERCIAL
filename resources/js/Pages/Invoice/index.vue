<template>
	<div class="MenuFaturacao bg-white">
		<Home />
        <Progress v-if="store.state.pos.StateProgress"/>
	</div>
</template>
<script setup>
import Progress from '@/Components/confirmation/progress.vue'
import { useStore } from 'vuex'
import Home from '../../Components/Invoice/Home.vue'

const store = useStore()

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
