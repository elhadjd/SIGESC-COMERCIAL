<template>
    <div class="formMessage">
        <span class="close" @click="$emit('closeMessage')">X</span>
        <div class="icons">
            <svg width="35px" height="35px" viewBox="0 0 512 512" id="icon" xmlns="http://www.w3.org/2000/svg">
                <path d="M259.92,262.91,216.4,149.77a9,9,0,0,0-16.8,0L156.08,262.91a9,9,0,0,1-5.17,5.17L37.77,311.6a9,9,0,0,0,0,16.8l113.14,43.52a9,9,0,0,1,5.17,5.17L199.6,490.23a9,9,0,0,0,16.8,0l43.52-113.14a9,9,0,0,1,5.17-5.17L378.23,328.4a9,9,0,0,0,0-16.8L265.09,268.08A9,9,0,0,1,259.92,262.91Z" fill="none" stroke="#00a5cf" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/>
                <polygon points="108 68 88 16 68 68 16 88 68 108 88 160 108 108 160 88 108 68" fill="none" stroke="#00a5cf" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/>
                <polygon points="426.67 117.33 400 48 373.33 117.33 304 144 373.33 170.67 400 240 426.67 170.67 496 144 426.67 117.33"  stroke="#00a5cf" fill="none" class="text-red-200" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/>
            </svg>
            <span>{{message.title}}</span>
        </div>
        <div class="message">
            <span>{{message.message}}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue"

interface message{
    type: string,
    message: string,
    title: string
}
const emits = defineEmits(['closeMessage'])
const props = defineProps({
    message: Object
})
const message = ref<message>({
    message: props.message.message,
    title: props.message.title,
    type: props.message.type
})

</script>

<style scoped lang="scss">
.formMessage{
    position: absolute;
    display: flex;
    flex-direction: column !important;
    gap: 5px;
    right: 0;
    top: 30px;
    background-color: var(--buttons-hover);
    color: #00a5cf;
    width: 300px;
    height: auto;
    padding: 10px;
    z-index: 1080;
    border-radius: 10px;
    .close{
        position: absolute;
        padding: 5px;
        right: 10px;
        width: 30px;
        height: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        font: 15pt arial;
        font-weight: 600;
        border-radius: 50%;
        &:hover{
            background-color: #00a5cf;
            color: white;
        }
    }
    .message{
        font-size: 14px;
        font-weight: 400;
    }
    .icons{
        display: flex; 
        align-items: center;
        
        >span{
            font-size: 15px;
            font-weight: 500 !important;
            text-align: center;
        }
    }
    @media screen and (max-width: 600px) {
        right: 15px;
        top: 200px;
        background-color: #00a5cf;
        color: #FFF;
        .icons{
            #icon{
                fill: #FFF;
                path,
                polygon{
                    fill: #FFF !important;
                }
            }
        }
    }
}
</style>
