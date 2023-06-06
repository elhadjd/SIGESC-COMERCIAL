<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <div>
               <h2>Empresa</h2>
            </div>
            <div>
               <button @click="saveCompany">
                  <FontAwesomeIcon icon="fa-solid fa-floppy-disk" />
                  Guardar
               </button>
            </div>
         </div>
      </div>

      <div class="Container">
         <div class="Form">
            <div class="form-container">
               <div class="Header">
                  <button>*</button>
               </div>
               <div class="Main">
                  <div class="Name-Img-control">
                     <div class="form-nome">
                        <input type="text" v-model="company.name" placeholder="Nome de empresa" name="company-name">
                        <input type="text" v-model="company.nif" placeholder="Nif de empresa" name="nif-company">
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
                           <label for="phone">Telefone: </label>
                           <input type="text" v-model="company.phone" name="phone" id="phone">
                        </div>
                        <div class="form-Control">
                           <label for="manager">Gerente: </label>
                           <button type="button" @click="DropShow('manager')" id="manager">{{company.manager?.name}}</button>
                           <div class="drop"  v-if="showDrop == 'manager'">
                              <span v-for="manager in managers" :key="manager.id" @click="selectManager(manager)">{{manager.surname}}</span>
                           </div>
                        </div>
                     </div>
                     <div class="form-content">
                        <div class="form-Control">
                           <label for="city">Cidade: </label>
                           <input type="text" v-model="company.city" name="city" id="city">
                        </div>
                        <div class="form-Control">
                           <label for="cede">Cede: </label>
                           <input type="text" v-model="company.sede" name="cede" id="cede">
                        </div>
                        <div class="form-Control">
                           <label for="NumHouse">Numero de casa: </label>
                           <input type="text" v-model="company.house_number" name="NumHouse" id="NumHouse">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="Footer">
                  <div class="form-Control">
                     <label for="hour">Tipo de atividade comercial: </label>
                     <input type="text" disabled v-model="company.activity.name">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { onMounted, ref } from '@vue/runtime-core'
import axios from 'axios'
import {useUploadImage} from '@/composable/public/UploadImage'

const props = defineProps({
    company: Object
})

const showDrop = ref("")
const company = ref(props.company)
const managers = ref([])

const image = ref({
    img:'/company/image/'+company.value.image
})

const emits = defineEmits(['message','close'])

const {onFileChange,createImg} = useUploadImage(company.value,image.value)

onMounted(()=>{
    getUser();
})

async function getUser() {
    await axios.get('getConfig')
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
        return user.nivel == 'admin'
    })

    managers.value = filterUser
})

const DropShow = ((drop)=>{
    if (showDrop.value == drop) return showDrop.value = ""
    showDrop.value = drop
})

const RemoveImage = () => {
    image.value.img = '/login/image/logo.png';
    company.value.image = 'logo.png'
}

const saveCompany = (()=>{
    axios.post('saveCompany',{...company.value})
    .then((response) => {
        emits('message',response.data.message,response.data.type)
        emits('close')
    }).catch((err) => {
        console.log(err);
    });
})
</script>

<style scoped lang="scss">
@import '../../../assets/config/scss/company';
</style>
