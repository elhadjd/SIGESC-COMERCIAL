<template>
<Progress v-if="$store.state.StateProgress" />
<div class="principal">
    <div class="Header">
        <div class="Header-left">
            <h2> {{$t('words.report')}} </h2>
            <span>
                <button @click="$emit('change')">{{$t('words.calendar')}}</button>
                <button @click="$emit('change')">{{$t('words.panel')}}</button>
            </span>
        </div>
        <div class="Header-right">
            <div class="">
                <Agrupar v-if="ShowRelat == 'painel'" @Agrup="Agrup" />
            </div>
        </div>
    </div>
    <div class="Container">
        <div class="Principal">
            <div class="Header-Cards">
                <div class="carde truncate">
                    <p>{{ cards.ListOrder.list.length }}</p>
                    <i class="fa fa-shopping-bag shop" aria-hidden="true"></i>
                    <span class="truncate">{{`${$t('words.order')} ${$t('words.requested')}`}}</span>
                </div>
                <div class="carde truncate">
                    <p>{{ cards.ListOrder.canceladas }}</p>
                    <i class="fa fa-ban text-danger shop" aria-hidden="true"></i>
                    <span class="truncate">{{`${$t('words.order')} ${$t('words.canceled')}`}}</span>
                </div>
                <div class="carde truncate">
                    <p>{{ cards.ListOrder.list.length - cards.ListOrder.canceladas }}</p>
                    <i class="fa fa-check concluida" aria-hidden="true"></i>
                    <span class="truncate">{{`${$t('words.order')} ${$t('words.completed')}`}}</span>
                </div>
            </div>
            <div class="Container-cards">
                <div class="Container-left">
                    <div class="cards">
                        <div class="card">
                            <div class="TotalVenda">
                                <span></span>
                                <div>{{$t('phrases.totalSale')}}</div>
                                <strong>{{
                                    FormatCurrency.format(cards.relat.TotalVenda)
                                }}</strong>
                            </div>
                        </div>
                        <div class="card">
                            <div class="TotalGasto">
                                <span></span>
                                <div>{{$t('words.expenses')}}</div>
                                <strong>{{
                                    FormatCurrency.format(cards.relat.TotalGasto)
                                }}</strong>
                            </div>
                        </div>
                        <div class="card">
                            <div class="TotalCusto">
                                <span></span>
                                <div>{{$t('phrases.totalCost')}}</div>
                                <strong>{{
                                    FormatCurrency.format(cards.relat.CustoProd)
                                }}</strong>
                            </div>
                        </div>
                        <div class="card">
                            <div class="TotalLucro">
                                <span></span>
                                <div>{{$t('phrases.totalProfit')}}</div>
                                <strong>{{
                                    FormatCurrency.format(cards.relat.TotalLucro)
                                }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="ListOrders">
                        <div class="InputSearch">
                            <span class="p-input-icon-right w-100">
                                <i class="pi pi-search" />
                                <input
                                    type="text"
                                    @keyup="FilterOrder"
                                    :placeholder="$t('words.search')"
                                />
                            </span>
                        </div>
                        <div class="list">
                            <div
                                v-for="item in cards.ListOrder.list.slice(0, 20)"
                                :key="item.id"
                                class="items"
                            >
                                <span>{{ item.number }}</span>
                                <span>{{ item.user.surname }}</span>
                                <span>{{ item.cliente }}</span>
                                <strong>{{ FormatCurrency.format(item.total) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Container-right">
                    <calender
                        :Orders="cards.ListOrder.list"
                        @progres="progres"
                        @RelatorByMonth="RelatorByMonth"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import OrderList from "../orders.vue";
import Progress from "@/components/confirmation/progress.vue";
import { onMounted, reactive, ref } from "vue";
import { useStore } from "vuex";
import calender from "./calender.vue";
const FormatCurrency = new Intl.NumberFormat("pt-ao", {
	style: "currency",
	currency: "AOA",
});
const emits = defineEmits(["message","change"]);
const store = useStore();
const cards = reactive({
	ListOrder: {
		list: [],
		storeList: [],
		canceladas: [],
	},
	relat: {
		TotalGasto: 0,
		Totalvenda: 0,
		TotalLucro: 0,
		CustoProd: 0,
	},
});

const progres = () => {
	store.state.StateProgress = !store.state.StateProgress;
};

const message = (message, tipo) => {
	emits("message", message, tipo);
};

const RelatorByMonth = async (Relator) => {
	cards.ListOrder.list = Relator.encomendas;
	cards.ListOrder.storeList = Relator.encomendas;
	cards.relat = Relator.relat;
	AnalisInvoice(cards.ListOrder.list);
};

const AnalisInvoice = (list) => {
	const FilterInvoice = list.filter((item) => {
		return String(item.estado).includes("Anulado");
	});
	cards.ListOrder.canceladas = FilterInvoice.length;
};
</script>

<style lang="scss" scoped>
@import "../../../../assets/PontoVenda/css/relatorio/relatorioCards";
</style>