<template>
  <div class="Principal">
    <div class="forme">
        <div class="Header">
            <span>{{data.cliente}}</span>
        </div>
        <div class="Container">
            <div class="Items">
                <div class="Lista" v-for="item in data.items" :key="item.id">
                    <div class="nomes">{{item.product.name}}</div>
                    <div class="Quantidade">
                        <div>{{item.quantity+',00Unidades(s)'}}</div>
                        <div>{{FormatarDineiro.format(item.price_sold)}}</div>
                        <div>{{FormatarDineiro.format(item.total)}}</div>
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
                    <div>{{FormatarDineiro.format(data.total)}}</div>
                    <div v-for="method in data.payments" :key="method.id">{{FormatarDineiro.format(method.amountPaid)}}</div>
                    <div>{{changes()}}</div>
                </div>
            </div>
        </div>
        <div class="Footer">
            <button class="PedirReembolso">Pedir devolução</button>
            <button @click="$emit('fechar')" class="Fechar">Fechar</button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from "@vue/runtime-core";

const FormatarDineiro = new Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})

const props = defineProps({
    dados: Object
});

const data = props.dados

const changes = (()=>{
    let amountPaid = 0
    data.payments.forEach((amount)=>{
        amountPaid += Number(amount.amountPaid)
    })
    return FormatarDineiro.format(Number(amountPaid) - Number(data.total))
})

</script>

<style  lang="scss" scoped>
    @import '../../../assets/Pos/css/MostrarPedido.scss';
</style>
