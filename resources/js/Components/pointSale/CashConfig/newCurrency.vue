<template>
  <div class="Principal">
    <div class="Modal">
        <div class="Header">
            <h1>{{$t('words.currency')}}</h1>
        </div>
        <div class="Container">
            <input type="text" v-model="currency.nome" placeholder="Exteção Exemplo AOA" >
            <input type="text" v-model="currency.pais" placeholder="Pais Exemplo Angola" >
            <!-- <div class="drop">
                <span v-for="currenc,index in currency" :key="index">
                    {{currenc.name}}
                    <strong>{{currenc.symbol}}</strong>
                </span>
            </div> -->
        </div>
        <div class="Footer">
            <button @click="$emit('close')" class="Descartar">{{$t('words.close')}}</button>
            <button @click="SaveCurrency">{{$t('words.save')}}</button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import axios, { Axios } from "axios";
const props = defineProps({
    currency: Object
})
const emits = defineEmits(['message','close'])
const currency = ref([])

onMounted(()=>{
    currency.value = props.currency
})

const SaveCurrency = (()=>{
    axios.post('/saveCurrency',{currency: currency.value})
    .then((response) => {
        emits('message',response.data.type,response.data.message)
        emits('close')
    }).catch((err) => {
        console.log(err);
    });
})
const currencys = ref(
    [
        {
            "symbol": "$",
            "name": "US Dollar"
        },
        {
            "symbol": "CA$",
            "name": "Canadian Dollar",
        },
        {
            "symbol": "€",
            "name": "Euro",
        },
        {
            "symbol": "AED",
            "name": "United Arab Emirates Dirham",
        },
        {
            "symbol": "AOA",
            "name": "Kuaza Angolana",
	    },
    ]
)
</script>

<style scoped lang="scss">
.Principal{
    @include modal;
    .Container{
        position: relative;
        .drop{
            border: 1px solid #eee;
            border-radius: 3px;
            position: absolute;
            background-color: white;
            width: 60%;
            right: 0;
            top: 54px;
            display: flex;
            flex-direction: column;
            height: 100px;
            overflow-y: auto;
            @include scroll;
            span{
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 3px 5px 3px 5px;
                cursor: pointer;
                &:hover{
                    background-color: rgba(0, 0, 0, 0.036);
                }
            }
        }
        input{
            margin-left: 10px;
            width: 50% !important;
        }
    }
}

</style>
