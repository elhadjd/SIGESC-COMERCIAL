<template>
  <div class="Principal">
    <div class="forme">
        <div class="Header">
            <span>{{data.cliente}}</span>
        </div>
        <div class="Container">
            <div class="Items">
                <div class="Lista" v-for="item in data.items" :key="item.id">
                    <div class="nomes">{{item.product.nome}}</div>
                    <div class="Quantidade">
                        <div>{{item.quantity+',00Unidades(s)'}}</div>
                        <div>{{formatMoney(item.price_sold)}}</div>
                        <div>{{formatMoney(item.total)}}</div>
                    </div>
                </div>
            </div>

            <div class="Total">
                <div class="title">
                    <div>Toatal</div>
                    <div v-for="method in data.payments" :key="method.id">{{method.method.name}}</div>
                    <div>Troco</div>
                </div>
                <div class="valores">
                    <div>{{formatMoney(data.total)}}</div>
                    <div v-for="method in data.payments" :key="method.id">{{formatMoney(method.amountPaid)}}</div>
                    <div>{{formatMoney(change)}}</div>
                </div>
            </div>
        </div>
        <div class="Footer">
            <button class="PedirReembolso" @click="invoice.state = true">Pedir devolução</button>
            <button @click="$emit('close')" class="Fechar">Fechar</button>
        </div>
    </div>

    <InvoiceCancel
        @close="invoice.state = $event"
        v-if="invoice.state"
        :invoice="data"
    />
  </div>

</template>

<script setup>
import InvoiceCancel from './invoiceCancel.vue';
import { onMounted, reactive, ref } from "@vue/runtime-core";
const emits = defineEmits(['close'])
const props = defineProps({
    dados: Object
});

const invoice = reactive({
    state:false
});

const data = ref(props.dados)

const change = ref(0);

onMounted(()=>{
    changes()
})

const changes = (()=>{
    let amountPaid = 0
    data.value.payments.forEach((amount)=>{
        amountPaid += Number(amount.amountPaid)
    })
    change.value = (Number(amountPaid) - Number(data.value.total))
})

</script>

<style  lang="scss" scoped>
    @import '../../../assets/Pos/css/MostrarPedido.scss';
</style>
