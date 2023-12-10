<template>
<Transition name="bounce">
  <div class="Principal">
    <div class="Form">
        <div class="Header">
            <h3>{{`${$t('words.notice')} ${$t('words.of')} ${$t('words.article')}`}}</h3>
        </div>
        <div class="Container">
            <div class="Info-One">
                <div>
                    <h4>{{dados.nome}}</h4>
                </div>
                <div>
                    <h4 class="Money">{{formatMoney(dados.preçovenda)}}</h4>
                    <span>isento de IVA-Resumo de exclusão</span>
                </div>
            </div>
            <div class="Info-two">
                <div>
                    <div class="Title">
                        <h4>Financial</h4>
                        <hr WIDTH=100%/>
                    </div>
                </div>
                <div class="Finanças">
                    <div class="Finanças-One">
                        <div>
                            <span>{{$t('words.sale')}}</span>
                            <span>{{$t('words.cost')}}</span>
                            <span>{{$t('words.profit')}}</span>
                        </div>
                        <div>
                            <span>{{formatMoney(dados.preçovenda)}}</span>
                            <span>{{formatMoney(dados.preçocust)}}</span>
                            <span>{{formatMoney(dados.preçovenda - dados.preçocust)}}</span>
                        </div>
                    </div>
                    <div class="Finanças-Two">

                    </div>
                </div>
            </div>
            <div class="Info-tree">
                <div>
                    <div class="Title">
                        <h4>{{$t('words.inventory')}}</h4>
                        <hr WIDTH=100%/>
                    </div>
                </div>
                <div class="Inventario">
                    <strong>Stock disponivel</strong>
                    <div v-for="stock in dados.stock" :key="stock.id">
                        <span>{{stock.armagen.name}}</span>
                        <span>{{stock.quantity+',00Unidade(s)'}}</span>
                    </div>
                </div>
            </div>
            <div class="Info-for">
                <div>
                    <div class="Title">
                        <h4>{{$t('words.discount')}}</h4>
                        <hr WIDTH=100%/>
                    </div>
                </div>
                <div class="prices">
                    <strong>{{$t('phrases.priceProduct')}}</strong>
                    <div v-for="item in dados.list_price" :key="item.id">
                        <span>{{"Na compra de >="+item.quantity+',00Unidade(s)'}}</span>
                        <span>{{formatMoney(item.price_discount)}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="Footer">
            <button @click="$emit('CloseInfo')">{{$t('words.close')}}</button>
        </div>
    </div>
  </div>
</Transition>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core"

const dados = ref([])

const props = defineProps({
    Information: Object
})

const FormatarDineiro = new Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})

onMounted(()=>{
    dados.value = props.Information
})

const open = ref(false)
</script>

<style scoped lang="scss">
@import '../../../assets/Pos/css/InfoProd.scss';
</style>
