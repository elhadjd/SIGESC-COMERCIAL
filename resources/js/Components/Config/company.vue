<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <div>
               <h2>{{$t('words.company')}}</h2>
            </div>
            <div>
               <button @click="saveCompany">
                  <FontAwesomeIcon icon="fa-solid fa-floppy-disk" />
                  {{$t('words.save')}}
                  <FontAwesomeIcon v-if="progress" icon="fa-solid fa-spinner" shake />
               </button>
               <button class="botao_descartar" @click="$emit('close')">{{$t('words.close')}}</button>
            </div>
         </div>
      </div>

      <div class="Container">
         <div class="Form">
            <div class="form-container">
               <div class="Header">
                  <VerifyEmailVue :company="company"/>
               </div>
               <div class="Main">
                  <div class="Name-Img-control">
                     <div class="form-nome">
                        <input type="text" id="name" v-model="company.name" :placeholder="$t('phrases.companyName')" name="company-name normal-case">
                        <input type="text" id="nif" v-model="company.nif" :placeholder="$t('phrases.nifCompany')" name="nif-company normal-case">
                     </div>
                     <div class="form-image">
                        <div>
                           <img :src="image.img" alt="">
                           <span>
                              <label for="image">
                                 <FontAwesomeIcon icon="fa-solid fa-pen-to-square"/>
                                 <input type="file" id="image" @change="onFileChange">
                              </label>
                              <FontAwesomeIcon @click="RemoveImage" icon="fa-solid fa-trash"/>
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="info-basic">
                     <div class="form-content">
                        <div class="form-Control">
                           <label for="email">E-mail: </label>
                           <input type="email" v-model="company.email" name="email" id="email">
                        </div>
                        <div class="form-Control">
                           <label for="phone">{{$t('words.phone')}}: </label>
                           <input type="text" v-model="company.phone" name="phone" id="phone">
                        </div>
                        <div class="form-Control">
                           <label for="manager">{{$t('words.manager')}}: </label>
                           <button type="button" @click="DropShow('manager')" id="manager">{{company.manager?.name}}</button>
                           <div class="drop"  v-if="showDrop == 'manager'">
                              <span v-for="manager in managers" :key="manager.id" @click="selectManager(manager)">{{manager.surname}}</span>
                           </div>
                        </div>
                     </div>
                     <div class="form-content">
                        <div class="form-Control">
                           <label for="city">{{$t('words.city')}}: </label>
                           <input type="text" v-model="company.city" name="city" id="city">
                        </div>
                        <div class="form-Control">
                           <label for="cede">{{$t('words.neighborhood')}}: </label>
                           <input type="text" v-model="company.sede" name="cede" id="cede">
                        </div>
                        <div class="form-Control">
                           <label for="NumHouse">{{$t('words.houseNumber')}}: </label>
                           <input type="text" v-model="company.house_number" name="NumHouse" id="NumHouse">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="Footer h-full flex flex-row">
                <div class="form-content w-1/2">
                  <div class="form-Control">
                     <label for="hour">{{$t('words.activityType')}}: </label>
                     <input type="text" disabled v-model="company.activity.name">
                  </div>
                  <div class="form-Control">
                     <label for="location">{{$t('words.localisation')}}: </label>
                     <button type="button" @click="localisation">{{String(company.longitude)+String(company.latitude)}}</button>
                  </div>
                </div>
                <div class="form-content w-1/2">
                  <div class="form-Control">
                    <label for="currency">{{$t('words.currency')}}: </label>
                    <button type="button" @click="showDrop = 'currency'" id="currency">{{company.currency_company?.code|| $t('words.currency')}}</button>
                    <div class="drop"  v-if="showDrop == 'currency'">
                        <input type="text" @keyup="(e)=>SearchCurrency(e.target.value)" :placeholder="$t('words.search')">
                        <span v-for="currency in currencyAll" :key="currency.code" @click="changeCurrency(currency)">{{currency.currency}}</span>
                    </div>
                  </div>
                </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import VerifyEmailVue from './verifyEmail.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { onMounted, reactive, ref } from '@vue/runtime-core'
import axios from 'axios'
import {useUploadImage} from '@/composable/public/UploadImage'
import { getImages } from '@/composable/public/getImages'
import {useCompanyService} from './services/companyService'
const props = defineProps({
    company: Object
})
const emits = defineEmits(['message','close'])
const {
    currencyAll,
    changeCurrency,
    company,
    showDrop,
    SearchCurrency,
    progress,
    saveCompany
    } = useCompanyService(props,emits)
const managers = ref([])
const image = reactive({
    img:'/company/image/'+company.value.image
})

const {onFileChange,createImg} = useUploadImage(company.value,image)
const {getImage,RemoveImage} = getImages(image);
onMounted(async()=>{
    await getImage()
    await getUser();
})

const localisation = (()=>{
    if ("geolocation" in navigator) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          company.value.latitude = position.coords.latitude;
          company.value.longitude = position.coords.longitude;
        //   const googleMapsLink = `https://www.google.com/maps/@${latitude},${longitude},15z`;
        },
        (error) => {
          console.error(`Erro ao obter localização: ${error.message}`);
        }
      );
    } else {
      console.log("Geolocalização não suportada pelo navegador");
    }
})

async function getUser() {
    const locale = localStorage.getItem('locale');
    await axios.get(`getConfig/${locale || 'en'}`)
    .then((response) => {
        filterUsersAdmin(response.data.users)
    }).catch((err) => {
        console.log(err);
    });
}

const selectManager = ((manager)=>{
    showDrop.value = ""
    company.value.manager = manager
})

const filterUsersAdmin = ((users)=>{
    const filterUser = users.filter((user)=>{
        return user.roles.name == 'Admin'
    })

    managers.value = filterUser
})

const DropShow = ((drop)=>{
    if (showDrop.value == drop) return showDrop.value = ""
    showDrop.value = drop
})

</script>

<style scoped lang="scss">
@import '../../../assets/config/scss/company';
</style>
