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
               <div class="Container">
                  <div class="Name-Img-control">
                     <div class="form-nome">
                        <input type="text" v-model="company.nome_de_empresa" placeholder="Nome de empresa" name="company-name">
                        <input type="text" v-model="company.nif_empresa" placeholder="Nif de empresa" name="nif-company">
                     </div>
                     <div class="form-image">
                        <div>
                           <img :src="image" alt="">
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
                           <input type="text" @click="DropShow('manager')" v-model="company.manager" name="manager" id="manager">
                           <div class="drop"  v-if="showDrop == 'manager'">
                              <span v-for="manager in managers" :key="manager.id" @click="selectManager(manager)">{{manager.apelido}}</span>
                           </div>
                        </div>
                     </div>
                     <div class="form-content">
                        <div class="form-Control">
                           <label for="city">Cidade: </label>
                           <input type="text" v-model="company.cidade" name="city" id="city">
                        </div>
                        <div class="form-Control">
                           <label for="cede">Cede: </label>
                           <input type="text" v-model="company.sede" name="cede" id="cede">
                        </div>
                        <div class="form-Control">
                           <label for="NumHouse">Numero de casa: </label>
                           <input type="text" v-model="company.numero_de_casa" name="NumHouse" id="NumHouse">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="Footer">
                  <div class="form-Control">
                     <label for="hour">Tipo de atividade comercial: </label>
                     <input type="text" v-model="company.tipo_de_atividade" @click="DropShow('activityType')" name="hour" id="hour">
                     <div class="drop" v-if="showDrop == 'activityType'">
                        <span @click="selectActivityType('Loja')">Loja</span>
                        <span @click="selectActivityType('Shopping')">Shopping</span>
                        <span @click="selectActivityType('Restourante')">Restourante</span>
                        <span @click="selectActivityType('Farmacia')">Farmacia</span>
                        <span @click="selectActivityType('Pagaria')">Pagaria</span>
                        <span @click="selectActivityType('Salao de beleza')">Salao de beleza</span>
                     </div>
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

const props = defineProps({
    company: Object
})

const showDrop = ref("")
const company = ref(props.company)
const managers = ref([])
const image = ref('/login/image/'+company.value.imagem)
const emits = defineEmits(['message','close'])

onMounted(()=>{
    axios.get('/users')
    .then((response) => {
        filterUsersAdmin(response.data)
    }).catch((err) => {
        console.log(err);
    });
})

const selectManager = ((manager)=>{
    showDrop.value = ""
    company.value.manager = manager.apelido
})

const filterUsersAdmin = ((users)=>{
    const filterUser = users.filter((user)=>{
        return user.nivel == 'Administrador'
    })

    managers.value = filterUser
})
const DropShow = ((drop)=>{
    if (showDrop.value == drop) return showDrop.value = ""
    showDrop.value = drop
})

const selectActivityType = ((activity)=>{
    company.value.tipo_de_atividade = activity
    showDrop.value = ""
})


const onFileChange = (e) => {
    var files = e.target.files || e.dataTransfer.files;
    // Verificar o formato da imagem
    let arquivo = files[0].name;
    let extension = arquivo.indexOf('.') < 1 ? '' : arquivo.split('.').pop();
    if (extension == "") {
        return false;
    } else {
        // verificar se este formato existe no Array
        let formatos_permitidos = ['jpg', 'png', 'gif', 'jpeg', 'JPG', 'PNG', 'GIF', 'JPEG'];
        let resultado = formatos_permitidos.includes(extension);
        if (resultado == false) {
            return false;
        } else {
            // Tamanho da foto em MB
            var tamanho_maximo = 2242880
            var fsizet = 0;
            for (var i = 0; i <= e.target.files.length - 1; i++) {
            // tamanho do arquivo
            var fsize = e.target.files.item(i).size;
            // total calculado
            fsizet = fsizet + fsize;
            }
            if (fsizet > tamanho_maximo) {
                console.log('tamanho de imagem muito grande');
            } else {
                createImg(files[0]);
            }
        }
    }
}

const createImg = (file) => {
    var imagem = new Image;
    var reader = new FileReader;

    reader.onload = (e) => {
    image.value = e.target.result;
    company.value.imagem = image.value
    };

    reader.readAsDataURL(file);
}

const RemoveImage = () => {
    image.value = '/login/image/logo.png';
    company.value.imagem = 'logo.png'
}

const saveCompany = (()=>{
    axios.post('/saveCompany',{company:company.value})
    .then((response) => {
        emits('message',response.data.message,response.data.tipo)
        emits('close')
    }).catch((err) => {
        console.log(err);
    });
})
</script>

<style scoped lang="scss">
@import '../../../assets/config/scss/company'
</style>
