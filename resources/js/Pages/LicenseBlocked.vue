<template>
	<div class="Principal">
		<div class="sisgesc">
			<h1>Licenças</h1>
			<h1><span>S</span>ISGESC</h1>
		</div>

		<div class="content">
			<div v-if="license.state === 'expired'" class="expired">
				<img src="LicenseExpired/images/expired.jpg" />
				<strong>Sua Licença Expirou contacte o Administrador</strong>
				<Link :href="route('login')">Voltar</Link>
			</div>
			<div v-else-if="license.state === 'blocked'">
				<img src="LicenseExpired/images/blocked.png" />
				<strong
					>Sua Licença foi Bloqueada por violação dos termos de uso, contacte o
					Administrador</strong
				>
				<Link :href="route('login')">Voltar</Link>
			</div>
		</div>
	</div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
	license: Object,
});
</script>
<style lang="scss" scoped>
@import "../../assets/Start/welcome";
.content {
	width: 400px;
	height: 170px;
	padding: 10px;
	display: flex;
	justify-content: center;
	align-items: center;
	border-radius: 4px;
	background-color: white;
	> div {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		img {
			width: 100px;
			margin-bottom: 5px;
		}
		a {
			text-decoration: none;
			@include botao_normal;
		}
	}
}
</style>