<template>
    <orderItems v-if="stateFormItems" @message="message" @fechar="stateFormItems = false" :order="ListPedido" />
    <div v-else class="principal">
        <div class="Header">
            <div class="Header-left">
                <h2>Ordens</h2>
            </div>
            <div class="Header-right">
                <KeepAlive>
                    <filter-orders @ListDefault="List"/>
                </KeepAlive>
            </div>
        </div>
        <div class="Container">
            <div class="Title">
                <div>Ref {{$t('words.order')}}</div>
                <div>{{$t('apps.pdvName')}}</div>
                <div>{{$t('words.sessions')}}</div>
                <div>{{$t('words.client')}}</div>
                <div>{{$t('words.date')}}</div>
                <div>{{$t('words.employee')}}</div>
                <div class="TotalOrden">{{$t('words.total')}}</div>
                <div class="px-3">{{$t('words.state')}}</div>
            </div>
            <div class="list_items">
                <div v-for="order in ListOrders"
                    :key="order.id" @click="Encomendas(order)" class="rows" >
                    <div> <strong>{{ "Orden " + order.number }}</strong></div>
                    <div>{{ order.session.caixa.name }}</div>
                    <div>{{ order.session_id }}</div>
                    <div>{{ order.cliente }}</div>
                    <div>{{ formatDate(order.created_at) }}</div>
                    <div>{{ order.user.surname }}</div>
                    <div class="TotalOrden"> {{ formatMoney(order.total) }} </div>
                    <div>{{ order.state }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from "@vue/runtime-core";
import filterOrders from "@/components/filterSearch/index.vue";
import orderItems from "./orderItems.vue";
import { useStore } from "vuex";
import moment from "moment";

const emit = defineEmits(['message']);

const ListPedido = ref();

const store = useStore();

const stateFormItems = ref(false);

const ListOrders = ref([]);

const formatDate = (date) => {
  return moment(date).format("DD-MM-YYYY hh:mm:ss");
};

const Encomendas = (event) => {
  stateFormItems.value = true;
  ListPedido.value = event;
};

const message = (type, message) => {
    emit('message', type,message)
}

const BuscarLista = (event) => {
  if (store.state.pos.ListEncomenda.MostrarFiltro >= 2) {
    store.state.pos.ListEncomenda.MostrarFiltro -= 1;
  } else {
    store.state.pos.ListEncomenda.MostrarFiltro += 1;
  }
};

const List = (list) => {
  ListOrders.value = list.data;
};

const FormetDineiro = new Intl.NumberFormat("pt-AO", {
  style: "currency",
  currency: "AOA",
});
</script>


<style scoped lang="scss">
@include components;
.Container{
    @include form_lists;
    overflow-x: auto;
    .list_items,
    .Title{
        min-width: 1050px !important;
    }
}
</style>
