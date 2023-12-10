<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <span>
               <h2>{{$t('words.store')}}</h2>
            </span>
            <div class="buttons">
               <button @click="!formArmagen.state ? showFormArmagen([]): saveArmagen()" type="button">
                  <FontAwesomeIcon :icon="!formArmagen.state ? 'fa-solid fa-plus':'fa-solid fa-floppy-disk'"/>
                  {{!formArmagen.state ? $t('words.new'): $t('words.save')}}
               </button>
               <button v-if="formArmagen.state" @click="formArmagen.state = false" type="button">
                  <font-awesome-icon icon="fa-solid fa-xmark" />
                  {{$t('words.close')}}
               </button>
            </div>
         </div>
         <div class="Header-right">
            <span>
            <input type="text" @keyup="search" :placeholder="$t('words.search')">
            </span>
         </div>
      </div>

      <div class="Container">
         <div v-if="!formArmagen.state" class="Main">
            <div v-for="armagen in formArmagen.armagens" :key="armagen.id" @click="showFormArmagen(armagen)" class="form-Content">
               <span>{{armagen.name}}</span>
               <span>stock +(1.000.000)</span>
            </div>
         </div>
         <NovoArmagen v-else @closeForm="OnMounted" @message="message" :armagen="formArmagen.armagen[0]"/>
      </div>
   </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import NovoArmagen from './newStore.vue'
import { onMounted, ref } from '@vue/runtime-core'
import useEventsBus from '@/Eventbus'
import axios from 'axios'
const {emit} = useEventsBus()
const emits = defineEmits(['message'])
const formArmagen = ref({
    armagen: [],
    armagens: [],
    storeArmagens:[],
    state: false,
})

const OnMounted = onMounted(()=>{
    formArmagen.value.state = false
    getArmagens()
})

const showFormArmagen = ((armagen)=>{
    formArmagen.value.armagen[0] = armagen
    formArmagen.value.state = true
})
const saveArmagen = (()=>{
    emit('saveArmagen',1+1)
})

const getArmagens = (()=>{
    axios.get('/getArmagens')
    .then((response) => {
        formArmagen.value.armagens = response.data.armagens
        formArmagen.value.storeArmagens = response.data.armagens
    }).catch((err) => {
        console.log(err);
    });
})
const message = ((message,type)=>{
    emits('message',message,type)
})

const search = ((event)=>{
    const filter = formArmagen.value.storeArmagens.filter((armagen)=>{
        return String(armagen.NomeArmagen).toLowerCase().includes(event.target.value.toLowerCase())
    })
    if (filter.length == 0) return message('Nenhum registro encontrado','info')
    formArmagen.value.armagens = filter
})
</script>

<style scoped lang="scss">
@import '../../../assets/stock/scss/armagens';
</style>