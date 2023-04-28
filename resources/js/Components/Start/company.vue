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
		<label for="name">Empresa:</label>
		<input type="text" required v-model="company.name" id="name" />
	</div>

	<div class="box">
		<label for="nif">Nif:</label>
		<input type="text" required v-model="company.nif" id="nif" />
	</div>

	<div class="box">
		<label htmlFor="phone">Telefone:</label>
		<input type="text" required v-model="company.phone" id="phone" />
	</div>

    <div class="box">
		<label htmlFor="activity">Tipo de atividade:</label>
		<button type="button" id="activity" @click="activities.state = !activities.state">{{company.activity?.name}}</button>
        <div class="drop" v-if="activities.state">
            <span v-for="item in activities.data" :key="item.id" @click="chooseActivity(item)">{{item.name}}</span>
        </div>
	</div>

	<div class="box">
		<label for="country">Pais:</label>
        <button @click="country.state = ! country.state" id="country" type="button">
            <span>{{company.country != [] ? company.country?.name : 'Escolhe seu pais'}}</span>
        </button>
        <div class="drop" v-if="country.state" >
            <input type="text" @keyup="searchCountry" placeholder="Nome do pais" />
            <span v-for="country,idx in country.data" @click="chooseCountry(country)" :key="idx">{{country.name}}</span>
        </div>
	</div>
</template>

<script setup>
import axios from "axios";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { onMounted, reactive, ref, watch } from "vue";
import {useUploadImage} from '@/composable/public/UploadImage'
import { useStore } from "vuex";



const store = useStore();

const country = ref({
	state: false,
	data: [],
	store: [],
    choose: null
});

const activities = reactive({
    state: false,
    data: []
})

const company = ref(store.state.start.company);

const image = reactive({
	img: company.value.imagem ? company.value.imagem : "/produtos/image/produto-sem-imagem.png",
});

const {createImg,onFileChange} = useUploadImage(company.value, image);


onMounted(async ()=>{
    await getCountry();
    await getActivities()
})

watch(company.value,(newValue)=>{
  if(VerifyObjectValues(newValue)){
    store.commit('StartSaveCompany',newValue)
  }
})

function VerifyObjectValues(object) {
  if (!object.activity.name || !object.country.name) {
    return false
  } else {
    for (let propriety in object) {
    if (object[propriety] === null) {
      return false;
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
    await axios.get('/config/getActivity')
    .then((response) => {
        activities.data = response.data
    }).catch((err) => {
        console.log(err);
    });
}

async function searchCountry(event) {
    let string = event.target.value
    const filter = country.value.store.filter((item)=>{
        return String(item.name).toLocaleLowerCase().includes(string.toLocaleLowerCase());
    })

    country.value.data = filter
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
.form-image{
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    
    #image{
        display: none;
    }
    
    >div{
        position: relative;
        width: 90px;
        height: 90px;
        img{
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px dotted #ddd;
        }
        label{
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
        }
    }
}
.box {
    position: relative;
	@include form-control;
    align-items: unset;
    @include dopDown;
    .form-image{
        height: 100px !important;
        img{
            height: 100% !important;
        }
    }
}

</style>
