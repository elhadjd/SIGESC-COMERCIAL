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
                    class="text-center"
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
                    :key="index">
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
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
const {t} = useI18n()
const emits = defineEmits(["RelatorByMonth", "progres"]);
const props = defineProps({
	Orders: Object,
});

const dates = ref([])

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
	{ name: t('month.January'), month: 1, money: 0 },
	{ name: t('month.February'), month: 2, money: 0 },
	{ name: t('month.March'), month: 3, money: 0 },
	{ name: t('month.April'), month: 4, money: 0 },
	{ name: t('month.May'), month: 5, money: 0 },
	{ name: t('month.June'), month: 6, money: 0 },
	{ name: t('month.July'), month: 7, money: 0 },
	{ name: t('month.August'), month: 8, money: 0 },
	{ name: t('month.September'), month: 9, money: 0 },
	{ name: t('month.October'), month: 10, money: 0 },
	{ name: t('month.November'), month: 11, money: 0 },
	{ name: t('month.December'), month: 12, money: 0 },
]);

onMounted(() => {
    checkDate(new Date().getMonth() + 1)
	DaysInMonthPrincipal();
});

const checkDate = ((mes)=>{
    dates.value = []
    if(mes == 0){
        var objData = new Date(),
		ano = objData.getFullYear()-1,
		mes = 12,
		dias = new Date(ano, mes, 0).getDate();
        dates.value = [mes,ano,dias]
    }else if(mes >= 12){
        var objData = new Date(),
		ano = objData.getFullYear()+1,
		mes = 1,
		dias = new Date(ano, mes, 0).getDate();
        dates.value = [mes,ano,dias]
    }else{
        var objData = new Date(),
		ano = objData.getFullYear(),
		mese = mes,
		dias = new Date(ano, mes, 0).getDate();
        dates.value = [mese,ano,dias]
    }
})

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
	if(mes == 0){
        dates.value[1] = dates.value[1] -1
        dates.value[0] = 12
    }else if(mes > 12){
        dates.value[1] = dates.value[1] +1
        dates.value[0] = 1
    }else{
        dates.value[0] = mes
    }
	if (dates.value[0] > 0 && dates.value[0] <= 12) {
		Data.days = dates.value[2];
		Data.year = dates.value[1];
		Data.month = dates.value[0];
		Data.day = String(new Date().getDate()).padStart("0");
		switch (dates.value[0]) {
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
