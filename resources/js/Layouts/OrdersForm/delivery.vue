<template>
    <div class="Principal">
        <div class="Modal relative flex flex-col">
            <div class="flex justify-center">
                <span class="closeModal absolute ml-2 rounded-full flex justify-center items-center" @click="$emit('closeModal')">x</span>
                <h2 class="h1 font-sm">Informações da entrega</h2>
            </div>
            <div class="w-full h-full flex flex-row">
                <div class="cad flex w-1/2 items-center justify-center">
                    <slot name="cad" class="flex"/>
                </div>
                <div class="infoDelivery border border-gray-100 border-dashed flex-auto justify-star">
                    <div class="flex p-2 flex-row">
                        <span class="flex w-1/3">Cidade:</span>
                        <span class="flex-auto">{{delivery.city}} </span>
                    </div>
                    <div class="flex p-2 flex-row">
                        <span class="flex w-1/3">Municipio:</span>
                        <span class="flex-auto">{{delivery.county}} </span>
                    </div>
                    <div class="flex p-2 flex-row">
                        <span class="flex w-1/3">Bairo:</span>
                        <span class="flex-auto">{{delivery.neighborhood}} </span>
                    </div>
                    <div class="flex p-2 flex-row">
                        <span class="flex w-1/3">Rua:</span>
                        <span class="flex-auto">{{delivery.road}} </span>
                    </div>
                    <div class="flex p-2 flex-row">
                        <span class="flex w-1/3">Numero de casa:</span>
                        <span class="flex-auto">{{delivery.housNumber}} </span>
                    </div>
                    <div class="flex p-2 flex-row">
                        <span class="flex w-1/3">Nº de telefone:</span>
                        <span class="flex-auto">{{client.phone}} </span>
                    </div>
                    <div class="flex p-2 flex-row">
                        <span class="flex w-1/3">Escrito por cliente:</span>
                        <span class="flex-auto">{{delivery.comment}} </span>
                    </div>
                    <div class="flex p-2 flex-row">
                        <span class="flex w-1/3">Localisação:</span>
                        <a href="#" @click="goToLocation" class="flex-auto">{{'clica aqui para ver a localisação na mapa'}} </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue"

const props = defineProps({
    delivery: Object,
    client: Object
})
const emits = defineEmits(['closeModal'])
const goToLocation = (()=>{
    const googleMapsUrl = `https://www.google.com/maps?q=${props.delivery.localisation}`;
    window.open(googleMapsUrl, '_blank');
})
</script>

<style scoped lang="scss">
.Principal{
    @include modal;
    .Modal{
        background-color: white;
        height: 450px;
        width: 900px !important;
        border-radius: 5px;
        padding: 10px;
        >div:nth-child(1){
            h2{
                color: var(--bgButtons);
                font: 18pt arial !important;
            }
        }
        >div:nth-child(2){
            .cad{
                color: var(--bgButtons);
            }
        }
        .closeModal{
            width: 30px;
            height: 30px;
            top: 0;
            right: 0;
            font: 16pt arial;
            font-weight: 600;
            cursor: pointer;
            color: var(--bgButtons);
            &:hover{
                background-color: var(--bgButtons);
                color: white;
            }
        }
    }
}
@media screen and (max-width: 800px) {
    .Modal{
        height: auto !important;
        >div:nth-child(2){
            flex-direction: column !important;
            >div:nth-child(1){
                height: auto !important;
                padding: 10px;
                width: 100%;
                margin-top: 10px;
                justify-content: center;
            }
            .infoDelivery{
                height: auto !important;
            }
        }
        
    }
}
</style>
