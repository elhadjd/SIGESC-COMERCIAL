<template>
	<div class="MenuRelatorio">
		<div class="Header">
			<div class="Header-left">
				<strong> Relatorio </strong>
				<div>
					<button @click="ShowRelat = 'cards'">Calendario</button>
					<button @click="ShowRelat = 'painel'">Painel</button>
				</div>
			</div>
			<div class="Header-right">
				<span class="p-input-icon-right w-100">
					<i class="pi pi-search" />
					<input
						@keyup="PesquisarOrden"
						v-model="NumeroOrden"
						type="search"
						class="Pesquisar"
						placeholder="Pesquisar por id da orden , Exemplo(110)"
					/>
				</span>
				<div class="">
					<Agrupar v-if="ShowRelat == 'painel'" @Agrup="Agrup" />
				</div>
			</div>
		</div>
		<div class="Container">
			<Chart
				v-if="ShowRelat == 'painel'"
				class="Painel"
				type="bar"
				:data="chartData"
				:options="chartOptions"
			/>
			<RelatorioCards v-if="ShowRelat == 'cards'" />
		</div>
	</div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import Chart from "primevue/chart";
import axios from "axios";
import RelatorioCards from "./RelatorioCards.vue";
import Agrupar from "@/components/pointSale/relatorio/agrupar.vue";
import { useStore } from "vuex";
const store = useStore();
const ShowRelat = ref("cards");
const _numDias = () => {
	var objData = new Date(),
		numAno = objData.getFullYear(),
		numMes = objData.getMonth() + 1,
		numDias = new Date(numAno, numMes, 0).getDate();
	var dia = String(objData.getDate()).padStart("0");
	const dias = numDias - dia - numDias + Number(dia) + Number(dia);

	return dias;
};
const Agrup = computed(() => {
	return store.state.Painel;
});
const chartData = ref({
	labels: [],
	datasets: [
		{
			type: "line",
			label: "Observação",
			borderColor: "#42A5F5",
			borderWidth: 2,
			fill: true,
			data: [],
		},
		{
			type: "bar",
			label: "Valor",
			backgroundColor: "#FFA726",
			data: [],
		},
	],
});

const dias = ref([]);
onMounted(() => {
	for (let index = 1; index <= _numDias(); index++) {
		dias.value.push(index);
	}
	chartData.value.labels = dias.value;
	axios
		.get(`/PDV/analisDay/${_numDias()}`)
		.then((Response) => {
			chartData.value.datasets[0].data = Response.data;
			chartData.value.datasets[1].data = Response.data;
		})
		.catch((err) => {});
});
const PorDia = ref();

const chartOptions = ref({
	plugins: {
		legend: {
			labels: {
				color: "#495057",
			},
		},
	},
	scales: {
		x: {
			ticks: {
				color: "#495057",
			},
			grid: {
				color: "#ebedef",
			},
		},
		y: {
			ticks: {
				color: "#495057",
			},
			grid: {
				color: "#ebedef",
			},
		},
	},
});

watch(Agrup, (novo) => {
	chartData.value.labels = novo.outro;
	chartData.value.datasets[0].data = novo.result;
	chartData.value.datasets[1].data = novo.result;
});
</script>
<style scoped lang="scss">
@import "../../../../assets/PontoVenda/css/relatorio/relatorio";
</style>