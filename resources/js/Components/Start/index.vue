<template>
   <div class="Container">
      <header>
         <h1><span>S</span>ISGESC</h1>
      </header>
      <form @submit.prevent="submitForm" class="form">
         <div class="Header">
            <div>
               <font-awesome-icon icon="fa-regular fa-building" />
               <span>Empresa</span>
               <div class="step"></div>
            </div>
            <div>
               <font-awesome-icon icon="fa-regular fa-user" />
               <span>Perfil</span>
               <div :class="start.step > 0 ? 'step' : ''"></div>
            </div>
            <div>
               <font-awesome-icon icon="fa-solid fa-certificate" />
               <span>License</span>
               <div :class="start.step > 1 ? 'step' : ''"></div>
            </div>
         </div>
         <div class="Content">
            <company v-if="start.step === 0" />
            <Admin v-else-if="start.step === 1"/>
            <License v-else/>
         </div>
         <div class="Footer">
            <button type="button" v-if="start.step > 0" @click="back" class="Descartar">Voltar</button>
            <button type="submit" :disabled="inSubmit">{{start.step > 1 ? 'Concluir' : 'Avançar'}}</button>
         </div>
      </form>
   </div>
   <Toast/>
</template>


<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import company from "./company.vue";
import License from "./License.vue";
import Admin from "./Admin.vue";
import { computed, ref } from "vue";
import { useStore } from 'vuex';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
const inSubmit = ref(false)
const store = useStore()
const toast = useToast()
const start = computed(()=> store.getters['Start/start'])

const submitForm = (()=>{
    if (start.value.step > 1) {
        return saveForm()
    }
    nextStep()
})


const back = (()=> {
    start.value.step --
    store.commit('Start/BackStep', start.value.step)
})
const nextStep = (()=>{
    if (start.value.step > 0) {
        if (start.value.user.password != start.value.user.password1) {
            return message('As senhas não coincidem ','info')
        }
    }
    start.value.step ++
    store.commit('Start/nextStep',start.value.step)
})

const saveForm = (()=>{
    inSubmit.value = true
    const form = useForm(store.state.Start.start)
    form.post('/saveCompany',{
        onSuccess: (Response)=>{
            inSubmit.value = false
        },
        onError:(err)=>{
            message('Empresa ou email do usuario ja existe por favor verifica e tenta novamente','warn')
            console.log(err);
            inSubmit.value = false
        }
    })
})

const message = ((message , type)=>{
    toast.add({
        severity: type,
        summary: 'Informação',
        detail: message,
        life: 3000
    })
})

</script>

<style scoped lang="scss">
@import '../../../assets/Start/index';

</style>
