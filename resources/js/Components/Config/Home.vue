<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <h2>
               Configurações
            </h2>
         </div>
      </div>
      <div class="Container">
         <div class="CardApps">
            <div class="Apps">
               <div class="App" v-for="app in company?.license?.app_license" :key="app.id">
                  <div class="Imagem">
                     <img :src="'/login/image/'+app.apps.image" >
                  </div>
                  <div class="AppName">
                     {{app.apps.name}}
                  </div>
               </div>
            </div>
            <div class="Footer">
               <span class="TotalApps">
               {{company.license?.app_license.length+' Modulos Instalados'}}
               </span>
               <span class="AddApp">Adicionar Modulo</span>
            </div>
         </div>
         <div class="CardApps">
            <div class="Apps">
               <div class="App" @click="ShowUserSingle(user)" v-for="user in company.users?.slice(0,6)" :key="user.id">
                  <div class="Imagem">
                     <img :src="'/login/image/'+user.image" >
                  </div>
                  <div class="AppName">
                     {{user.surname}}
                  </div>
               </div>
            </div>
            <div class="Footer">
               <span class="TotalApps">
               {{company.users?.length + ' Usuario Conectado'}}
               </span>
               <span @click="$emit('modulos','users')" class="AddApp">Lista de Usuarios</span>
            </div>
         </div>
         <div class="CardApps">
            <div class="content">
               <div @click="showCompany('company')" class="company">
                  <div class="Imagem">
                     <img :src="'/company/image/'+company.image" >
                  </div>
                  <div class="name">
                     {{company.name}}
                  </div>
               </div>
               <div :class="license.state ? 'expired' : 'version'">
                {{license.expiration}}
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import moment from "moment";
import { useStore } from "vuex";
const store = useStore()
const emits = defineEmits('ShowSingleUser')

const company = ref([]);
const license = ref({
    state: false,
    expiration: 0,
})

const Onmounted = onMounted(async ()=>{
   await getConfig();
   await expiration()
})

async function expiration() {
    const targetDate = moment(company.value.license.to, 'YYYY-MM-DD H:mm:ss');
    let timeRemaining = targetDate.diff(moment());

    const intervalId = setInterval(() => {
    timeRemaining -= 1000;
    const duration = moment.duration(timeRemaining);

    license.value.expiration = duration.months() +" Meses: "+ duration.days() + 'Dias: ' + duration.hours() + 'Horas: ' + duration.minutes() + ':' + duration.seconds();
    if (duration._data.months <=0 && duration._data.days <= 2) return license.value.state = true
    if (timeRemaining <= 0) {
        clearInterval(intervalId);
        console.log('Tempo acabou!');
    }
    }, 1000);
}
const getConfig = async () => {
     await axios.get('getConfig')
    .then((response) => {
        company.value = response.data
    }).catch((err) => {
        console.log(err);
    });
}

const ShowUserSingle = ((event)=>{
    emits('ShowSingleUser',event)
})

const showCompany = (()=>{
    emits('showCompany',company.value)
})
</script>

<style lang="scss" scoped>
@import '../../../assets/config/scss/menu';
</style>
