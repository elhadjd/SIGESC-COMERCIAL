<template>
   <Transition name="bounce">
      <UpdatePassword v-if="StateUpdatePwd" @FecharModal="StateUpdatePwd = false" @message="EmitirMessagem" :user="user"/>
   </Transition>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <h2 @click="$emit('ListUsers','users')">Usuario</h2>
            <div>
               <button v-if="EstadoForm" @click="SaveUser">Guardar</button>
               <button @click="EstadoForm = false" v-if="EstadoForm" class="Descartar">Descartar</button>
               <button v-if="!EstadoForm" @click="EstadoForm = true">Editar</button>
               <button @click="StateUpdatePwd = true" class="AtualizarSenha">Atualizar Senha</button>
            </div>
         </div>
         <div class="Header-right">
         </div>
      </div>
      <div class="Container">
         <div class="Form">
            <div class="form-container">
                <div class="Main">
                    <div class="Name-Img-control">
                        <div class="form-nome">
                            <input :disabled="!EstadoForm" type="text" placeholder="Nome" v-model="user.name" id="nome">
                            <input :disabled="!EstadoForm" type="text" placeholder="Apelido" v-model="user.surname" id="surname">
                        </div>
                        <div class="form-image">
                            <div>
                                <img :src="element.img" alt="">
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
                                <label for="email">Email</label>
                                <input :disabled="!EstadoForm" type="email" v-model="user.email" id="email"/>
                            </div>
                            <div class="form-Control">
                                <label for="apelido">Apelido</label>
                                <input :disabled="!EstadoForm" type="text" v-model="user.surname" id="apelido"/>
                            </div>
                            <div class="form-Control">
                                <label for="level">Nivel</label>
                                <select :disabled="!EstadoForm" id="level" v-model="user.nivel">
                                    <option v-if="user.nivel == '' || user.nivel == null" value="">Seleciona o nivel de acesso</option>
                                    <option :selected="user.nivel == 'admin'" value="admin">Administrador</option>
                                    <option :selected="user.nivel == 'user'" value="user">Usuario</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ContainerFooter">
                    <div class="TituloFooter">
                        <span :class="step === 'password'?'active':''"
                        @click="step = 'password'">Segurança</span>
                        <span :class="step === 'info'?'active':''"
                        @click="step = 'info'">Informação do usuario</span>
                        <span :class="step === 'config'?'active':''"
                        @click="step = 'config'">Configuração</span>
                    </div>
                    <div v-if="user.email == null && step == 'password'" class="DivSenha">
                        <div class="regras-senha">
                            <ul>
                                <li>A senha deve ter no minimo 6 digitos</li>
                                <li>A Senha deve conter um caracter especial</li>
                                <li>A Senha deve conter número e letra</li>
                            </ul>
                        </div>
                        <div>
                            <span>
                                <label for="senha">Senha</label>
                                <input :disabled="!EstadoForm" v-model="senha.senha1" type="password" name="password" id="senha">
                            </span>
                            <span>
                                <label for="senha1">Confima Senha</label>
                                <input :disabled="!EstadoForm" v-model="senha.senha2" type="password" name="password" id="senha1">
                            </span>
                        </div>

                    </div>
                    <KeepAlive>
                        <info-user @changeInput="handleInputs" v-if="step === 'info'" :data="user"/>
                    </KeepAlive>
                    <KeepAlive>
                        <config v-if="step === 'config'" @changeInput="handleInputConfig" :data="user"/>
                    </KeepAlive>
                </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import UpdatePassword from './UpdatePassword.vue'
import infoUser from './infoUser.vue'
import config from './config.vue'
import {ref,reactive, onMounted} from 'vue';
import { getImages } from '@/composable/public/getImages';
import {useUploadImage} from '@/composable/public/UploadImage'
const props = defineProps({
    SingleUser: Object
})
const user = ref(props.SingleUser);
const emits = defineEmits(['message','ListUsers']);
const step = ref('password')
const StateUpdatePwd = ref(false)
const element = ref({
    img:'/login/image/'+user.value.image
})
const {onFileChange,createImg} = useUploadImage(user.value,element.value)
const {getImage,RemoveImage} = getImages(element.value);
const senha = reactive({
    senha1: null,
    senha2: null
})

const EmitirMessagem = ((message,tipo)=>{
    emits('message',message,tipo)
})

const EstadoForm = ref(true)

onMounted(async ()=>{
    await getImage()
    step.value = 'password'
    if (user.value?.perfil == null) user.value.perfil = []
    if (user.value?.config == null) user.value.config = []
})

function handleInputs(e) {
    user.value.perfil[e.target.id] = e.target.value
    console.log(user.value);
}

function handleInputConfig(e) {
    user.value.config[e.target.id] = e.target.checked
}

const SaveUser = (()=>{
    if (user.value.id != '') {
        return InsertUser(`SaveUser/${user.value.id}`,user.value)
    }
    if (senha.senha1 == "" || senha.senha1 == null || senha.senha2 == "" || senha.senha2 == null) {
       emits('message','Os campos não podem ser vázios','info')
    } else {
        if (senha.senha1 != senha.senha2) {
            emits('message','As Senhas não podem ser diferente','info')
        } else {
            if (senha.senha1.length <=5) {
                emits('message','A senha deve ser mair do que 5 digitos','info')
            } else {
                // const regex = /^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%*()_+^&}{:;?.])(?:([0-9a-zA-Z!@#$%;*(){}_+^&])(?!\1)){6,}$/;
                // if (regex.test(senha.senha1)) {
                //     user.value.password = senha.senha1;
                // } else {
                //     emits('message','Formato de senha invalida','info')
                // }
                return InsertUser(`SaveUser`,user.value)
            }
        }
    }
})

const InsertUser = ((route,User)=>{
    axios.post(route,{...User,...senha})
    .then((response) => {
        if (response.data.type == "success") {
            user.value = response.data.data
            EstadoForm.value = false
        }
        emits('message',response.data.message,response.data.type)
    }).catch((erro) => {
        emits('message','Aconteceu um no sistema por favor verifique as informações e tenta novament','error')
        console.log(erro);
    });
})

</script>

<style lang="scss" scoped>
@import '../../../../assets/config/scss/NewUser';
</style>
