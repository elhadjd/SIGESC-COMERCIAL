<template>
  <div class="Principal">
    <div class="Modal">
        <div class="Header">
            <h1>{{props.artigo.nome}}</h1>
        </div>
        <div class="Container">
            <div class="Preco">
                <input type="text" ref="inputRef" v-model="caixas.preco" placeholder="Preço da caixa">
                <input v-if="props.artigo.gasto" type="text" placeholder="Gasto da caixa" v-model="caixas.gasto">
            </div>
            <div class="Quantidades">
                <input type="number" v-model="caixas.TotalCaixas" placeholder="Quantidade">
                <input type="number" v-model="caixas.quantidade" placeholder="Quantidade da caixa">
            </div>
            
        </div>
        <div class="Footer">
            <button class="Descartar" @click="$emit('Close')">Fechar</button>
            <button @click="Confirmar">Confirmar</button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "@vue/runtime-core";
import { useCurrencyInput } from "vue-currency-input";

const { inputRef } = useCurrencyInput({currency: 'AOA' })

const emits = defineEmits(['message','Confirmou'])

const props = defineProps({
    artigo: Object
})

const caixas = reactive({
    TotalCaixas: null,
    preco: null,
    quantidade: null,
    gasto: null,
    resultado: {
        QuatidadeFinal: null,
        PrecoUnidade: null,
        gasto: null,
        id: props.artigo.id
    }
})

const Confirmar = (()=>{
    if (caixas.TotalCaixas == null || caixas.preco == null || caixas.quantidade == null) {
        emits('message','Os campos não pode ser vazio','info')
    } else {
        caixas.resultado.PrecoUnidade = caixas.preco / caixas.quantidade 
        caixas.resultado.QuatidadeFinal = caixas.quantidade * caixas. TotalCaixas
        caixas.resultado.gasto = caixas.gasto
        emits('Confirmou',caixas.resultado)
    }
})
</script>

<style lang="scss" scoped>
    .Principal{
        @include modal;
        align-items: center;
        .Header{
            h1{
                font-size: 20px !important;
            }
        }
        .Container{
            flex-direction: column;
            .Preco{
                width: 100% !important;
                display: flex;
                justify-content: center;
                gap: 1rem;
                input{
                    width: 92% !important;
                }
            }
            .Quantidades{
                display: flex;
                justify-content: space-between;
            }
        }
    }
</style>