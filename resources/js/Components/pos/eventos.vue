<template>
    <div class="FormPagamentoCima">
        <Cliente @cliente="client"/>
        <div class="QtdPrcImpr">
            <div class="">Imprimir</div>
            <div @click="$emit('tipo','preco')">Preço</div>
            <div >Desconto</div>
            <div @click="$emit('tipo','quantidade')">Quantidade</div>
        </div>
    </div>
    <div class="FormPagamentoBaixo">
        <div class="PagamentoEsquerda" @click="$emit('payment',true)">
            <i class="fa fa-credit-card-alt"></i>
            <div>Pagamento</div>
        </div>
        <div class="PagamentoDireita">
            <div
                v-for="(i, index) in 9"
                class="nomeros"
                id="numero"
                :key="index"
                @click="numero(i)"
                >
                {{ i }}
            </div>
            <div class="nomeros" @click="numero(0)" id="numero">{{ 0 }}</div>
            <div class="nomeros" @click="numero('.')" id="numero">.</div>
            <div id="numeros" @click="$emit('remover')">
                <i class="fa fa-ban" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from '@vue/runtime-core'
import Cliente from './cliente.vue'

const emits = defineEmits(['Alterar','cliente','remover','payment','tipo'])

const client = ((event)=>{
    emits('cliente',event)
})

const numero = ((event)=>{
    emits('Alterar',event)
})

</script>

<style lang="scss" scoped>
@import '../../../assets/Pos/css/Eventos';
</style>
