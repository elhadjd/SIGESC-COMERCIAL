<template>
   <div class="form-image">
      <div>
         <img :src="image.img" />
         <span>
            <label for="image">
                <input type="file" id="image" @change="onFileChange" />
            </label>
         </span>
      </div>
   </div>
   <div class="box">
      <label for="name">{{$t('words.company')}}:</label>
      <input type="text" required v-model="company.name" id="name" />
   </div>
   <div class="box">
      <label for="nif">Nif:</label>
      <input type="text" required v-model="company.nif" id="nif" />
   </div>
   <div class="box">
      <label htmlFor="phone">{{$t('words.phone')}}:</label>
      <input type="text" required v-model="company.phone" id="phone" />
   </div>
   <div class="box">
      <label htmlFor="activity">{{$t('words.activityType')}}:</label>
      <button type="button" id="activity" @click="activities.state = !activities.state">{{company.activity?.name?company.activity.name:'Seleciona o tipo de atividade'}}</button>
      <div class="drop" v-if="activities.state">
         <span v-for="item in activities.data" :key="item.id" @click="chooseActivity(item)">{{item.name}}</span>
      </div>
   </div>
   <div class="box">
      <label for="country">{{$t('words.country')}}:</label>
      <button @click="country.state = ! country.state" id="country" type="button">
      <span>{{company.country?.name ? company.country?.name : $t('words.country')}}</span>
      </button>
      <div class="drop" v-if="country.state" >
         <input type="text" @keyup="searchCountry" :placeholder="$t('words.country')" />
         <span v-for="country,idx in country.data" @click="chooseCountry(country)" :key="idx">{{country.name}}</span>
      </div>
   </div>
   <div class="box">
      <label htmlFor="city">{{$t('words.city')}}:</label>
      <input type="text" required :placeholder="$t('words.city')" v-model="company.city" id="city" />
   </div>
   <div class="box">
        <label for="currency">{{$t('words.currency')}}: </label>
        <button type="button" @click="showCurrency = !showCurrency" id="currency">{{company.currency_company?.code || $t('words.currency')}}</button>
        <div class="drop"  v-if="showCurrency">
            <input type="text" @keyup="(e)=>SearchCurrency(e.target.value)" :placeholder="$t('words.search')">
            <span v-for="currency in allCurrencies" :key="currency.code" @click="changeCurrency(currency)">{{currency.currency}}</span>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { computed, onMounted, reactive, ref, watch } from "vue";
import {useUploadImage} from '@/composable/public/UploadImage'
import { useStore } from "vuex";
import currencyCodes from 'currency-codes'
const store = useStore();
const allCurrencies = ref(currencyCodes.data);
const country = ref({
	state: false,
	data: [],
	store: [],
    choose: null
});
const showCurrency = ref(false)
const activities = reactive({
    state: false,
    data: []
})

const company = computed(()=> store.getters['Start/start'].company);

const image = reactive({
	img: company.value.imagem ? company.value.imagem : "/produtos/image/produto-sem-imagem.png",
});

const {createImg,onFileChange} = useUploadImage(company.value, image);

onMounted(async()=>{
    await getCountry();
    await getActivities()
})
const SearchCurrency = ((text)=>{
    const filter = currencyCodes.data.filter((currency)=>{
        return currency.currency.toLocaleLowerCase().includes(text.toLocaleLowerCase()) ||
        currency.code.toLocaleLowerCase().includes(text.toLocaleLowerCase())
    })
    allCurrencies.value = filter
})

const changeCurrency = ((currency)=>{
    company.value.currency_company = currency
    showCurrency.value = false
})
watch(company.value,(newValue)=>{
  if(VerifyObjectValues(newValue)){
    store.commit('Start/StartSaveCompany',newValue)
  }
})

function VerifyObjectValues(object) {
    if (!object.activity.id == 0 || !object.country.name == "") {
        return false
    } else {
        if (!object.currency_company.digits == 0){
            return false
        }else{
            for (let propriety in object) {
                if (object[propriety] === null) {
                    console.log(object[propriety]);
                    return false;
                }
            }
        }
        return true;
    }
}
async function getCountry() {
  axios.get('/data/country.json').then((response) => {
     country.value.data = response.data.data
     country.value.store = response.data.data
  }).catch((err) => {
    console.log(err);
  })
}

async function getActivities() {
    await axios.get('getActivity')
    .then((response) => {
        activities.data = response.data
    }).catch((err) => {
        console.log(err);
    });
}

async function searchCountry(event) {
    let string = event.target.value
    country.value.data = country.value.store.filter((item)=>{
        return String(item.name).toLocaleLowerCase().includes(string.toLocaleLowerCase());
    })

}

const RemoveImage =(()=>{
    image.img = "/produtos/image/produto-sem-imagem.png"
})

const chooseActivity = ((activity)=>{
    company.value.activity = activity
    activities.state = false
})

const chooseCountry = ((item)=>{
    company.value.country = item
    country.value.state = false
})
</script>

<style lang="scss" scoped>
@import '../../../assets/start/form';
</style>
