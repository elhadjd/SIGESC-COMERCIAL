<template>
	<div class="Principal">
		<div class="Form-calendar">
			<div class="Header-calendar">
				<span class="text-star">
					<i
						@click="getMonth(Data.month - 1)"
						class="fa fa-chevron-left"
						aria-hidden="true"
					></i>
				</span>
				<span>
					{{ Data.Active.month + " " + Data.Active.year }}
				</span>
				<span class="text-end">
					<i
						@click="getMonth(Data.month + 1)"
						class="fa fa-chevron-right"
						aria-hidden="true"
					></i>
				</span>
			</div>
			<div class="Container-calendar">
				<div class="month">
					<span
						@click="getMonth(month.month)"
						v-for="month in Month"
						:key="month.month"
						:class="month.month == Data.month ? 'MonthActive' : ''"
						>{{ month.name }}</span
					>
				</div>
				<div class="days">
					<div
						@click="RelatorBayDay(dia)"
						:class="
							Data.day >= dia ||
							(Data.Active.date.fin >= dia && Data.Active.date.inicio <= dia)
								? 'day DayActive'
								: 'day'
						"
						v-for="(dia, index) in Data.days"
						:key="index"
					>
						<span>{{ dia }}</span>
						<strong></strong>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { onMounted, reactive, ref } from "@vue/runtime-core";
import axios from "axios";
import { useStore } from "vuex";

const emits = defineEmits(["RelatorByMonth", "progres"]);
const props = defineProps({
	Orders: Object,
});

const Data = reactive({
	days: [{}],
	month: null,
	year: null,
	day: null,
	Active: {
		month: null,
		year: null,
		date: {
			inicio: null,
			fin: null,
		},
	},
});

const Month = ref([
	{ name: "Janeiro", month: 1, money: 0 },
	{ name: "Fevereiro", month: 2, money: 0 },
	{ name: "Março", month: 3, money: 0 },
	{ name: "Abril", month: 4, money: 0 },
	{ name: "Maio", month: 5, money: 0 },
	{ name: "Junho", month: 6, money: 0 },
	{ name: "Julho", month: 7, money: 0 },
	{ name: "Agosto", month: 8, money: 0 },
	{ name: "Setembro", month: 9, money: 0 },
	{ name: "Outubro", month: 10, money: 0 },
	{ name: "Novembro", month: 11, money: 0 },
	{ name: "Dezembro", month: 12, money: 0 },
]);

onMounted(() => {
	DaysInMonthPrincipal();
});

const DaysInMonthPrincipal = () => {
	var objData = new Date(),
		ano = objData.getFullYear(),
		mes = objData.getMonth() + 1,
		dias = new Date(ano, mes, 0).getDate();
	Data.days = dias;
	Data.year = ano;
	Data.month = mes;
	Data.day = String(objData.getDate()).padStart("0");
	getMonth(Data.month);
	return Data;
};

const getMonth = async (mes) => {
	var objData = new Date(),
		ano = objData.getFullYear(),
		mes = mes,
		dias = new Date(ano, mes, 0).getDate();
	if (mes > 0 && mes <= 12) {
		Data.days = dias;
		Data.year = ano;
		Data.month = mes;
		Data.day = String(objData.getDate()).padStart("0");
		switch (mes) {
			case 1:
				Data.Active.month = "01";
				break;
			case 2:
				Data.Active.month = "02";
				break;
			case 3:
				Data.Active.month = "03";
				break;
			case 4:
				Data.Active.month = "04";
				break;
			case 5:
				Data.Active.month = "05";
				break;
			case 6:
				Data.Active.month = "06";
				break;
			case 7:
				Data.Active.month = "07";
				break;
			case 8:
				Data.Active.month = "08";
				break;
			case 9:
				Data.Active.month = "09";
				break;
			case 10:
				Data.Active.month = "10";
				break;
			case 11:
				Data.Active.month = "11";
				break;
			case 12:
				Data.Active.month = "12";
				break;
		}
		Data.Active.year = Data.year;
		return getByMonth(`${Data.Active.month}/${Data.Active.year}`);
	}
};

const getByMonth = (date) => {
	return Request("/PDV/getRelatorByMonth", date);
};

const Request = (rota, param = null) => {
	emits("progres");
	axios
		.get(`${rota}/${param}`)
		.then((response) => {
			emits("progres");
			emits("RelatorByMonth", response.data);
		})
		.catch((err) => {
			emits("progres");
			console.log(err);
		});
};

const RelatorBayDay = (day) => {
	if (Data.Active.date.fin == null && Data.Active.date.inicio != null) {
		Data.Active.date.fin = day;
		return Request(
			"IntervalDateRelator",
			`${Data.Active.month}/${Data.Active.year}/${Data.Active.date.inicio}/${Data.Active.date.fin}`
		);
	}
	Data.Active.date.inicio = day;
	Data.Active.date.fin = null;
};
</script>

<style scoped lang="scss">
@import "../../../../assets/PontoVenda/css/relatorio/calendar";
</style>