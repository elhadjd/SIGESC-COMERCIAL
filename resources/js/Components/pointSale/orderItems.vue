<template>
    <div class="principal">
        <div class="Header">
            <div class="Header-left"><h2 @click="$emit('fechar')">{{$t('words.order')}}</h2></div>
        </div>
        <div class="Container">
            <div class="d-flex bg-white botoes rounded-0">
                <button type="button" @click="DevolverFatura" class="annular">
                    Devolver Artigos
                </button>
            </div>
            <div class="FormListPedidos">
                <div class="FormListPedido">
                    {{ order.state }}
                </div>
                <div class="FormList">
                    <div class="mt-4 text-secondary">
                        <div class="d-flex">
                            <div class="font-weight-600 InformacoesEncomenda">
                                <div>Ref {{$t('words.order')}}</div>
                                <div>{{$t('words.sessions')}}</div>
                                <div>{{$t('words.box')}}</div>
                                <div>{{$t('words.client')}}</div>
                            </div>
                            <div class="border ms-5 mt-1"></div>
                            <div class="Infos ms-4">
                                <div>{{ "Orden" + order.id }}</div>
                                <div>{{ "Sessão" + order.session?.id }}</div>
                                <div>{{ order.session.caixa?.name }}</div>
                                <div>{{ order.cliente }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="BorderBottom mt-3">
                        <div class="d-flex titloListPedidos text-secondary border-0 bg-light rounded-0" >
                            <div class="w-25 boder-left">{{$t('words.article')}}</div>
                            <div class="boder-left text-end">{{$t('words.quantity')}}</div>
                            <div class="boder-left text-end">{{$t('words.price')}}</div>
                            <div class="boder-left text-end">{{$t('words.total')}}</div>
                        </div>
                        <div v-for="item in order.items" :key="item.id"
                            class="d-flex ListPedidos text-secondary">
                            <div class="w-25">{{ item.product?.nome }}</div>
                            <div class="text-end">{{ item.quantity + "Un(s)" }}</div>
                            <div class="text-end">
                            {{ formatMoney(item.price_sold) }}
                            </div>
                            <div class="text-end">{{ formatMoney(item.total) }}</div>
                        </div>
                    </div>
                    <div class="w-100 form-control-sm text-secondary">
                        <div class="d-flex Totaless w-25">
                            <div class="TitleTotaies w-50">
                                <div>{{$t('words.total')}} :</div>
                                <div v-for="item in order.payments" :key="item.id">{{item.method?.method_translate[0].translate}} :</div>
                                <div>{{$t('words.change')}} :</div>
                                <div>{{$t('words.profit')}} :</div>
                            </div>
                            <div class="Totaies w-50">
                                <div>
                                    {{ formatMoney(order.total) }}
                                </div>
                                <div v-for="payment in order.payments" :key="payment.id">{{formatMoney(payment.amountPaid)}}</div>
                                <div>{{formatMoney(change)}}</div>
                                <div>{{ formatMoney(order.total - order.total_costs ) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Toast />
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "@vue/runtime-core";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import { useStore } from "vuex";

const props = defineProps({
  order: Object
});
const store = useStore()
const order = ref(props.order);
const amountPaid = ref(0)
const emits = defineEmits(['fechar','message']);
const change = ref(0)
const toast = useToast();

const FormatDinheiro = new Intl.NumberFormat("pt-AO", {
  style: "currency",
  currency: "AOA",
});

onMounted(()=>{
    changes()
})

const changes = (()=>{
    amountPaid.value = 0
    props.order.payments.forEach((amount)=>{
        amountPaid.value += Number(amount.amountPaid)
    })
    change.value = Number(amountPaid.value) - Number(props.order.total)
})

const DevolverFatura = () => {
  if (order.value.state === "Anulado") {
    emits('message','error','Essa Fatura já foi Anulada')
    return false;
  } else {
    store.state.pos.StateProgress = true;
    axios
      .put(`CancelInvoice/${props.order.id}`)
      .then((response) => {
        emits('message',response.data.type ,response.data.message)
        order.value = response.data.data
      })
      .catch((err) => {
        console.log(err);
      }).finally(()=>{
        store.state.pos.StateProgress = false;
      });
  }
};
</script>

<style lang="scss" scoped>
@import "../../../assets/filterSearch/css/encomenda.scss";
@include components;
</style>
