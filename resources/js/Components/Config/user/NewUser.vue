<template>
   <Transition name="bounce">
      <UpdatePassword v-if="StateUpdatePwd" @FecharModal="StateUpdatePwd = false" @message="EmitirMessagem" :user="user"/>
   </Transition>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <h2 @click="$emit('Users')">Usuario</h2>
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
         <div class="Formulario">
            <div class="HeaderForm">
               <div class="HeaderFormInputs">
                  <div class="Nome">
                     <label for="nome">Nome</label>
                     <input :disabled="!EstadoForm" type="text" v-model="user.name" id="nome">
                  </div>
                  <div class="FormInputsFooter">
                     <div class="Email">
                        <label for="email">Email</label>
                        <input :disabled="!EstadoForm" type="email" v-model="user.email" id="email"/>
                     </div>
                     <div class="Email">
                        <label for="apelido">Apelido</label>
                        <input :disabled="!EstadoForm" type="text" v-model="user.surname" id="apelido"/>
                     </div>
                  </div>
                  <select :disabled="!EstadoForm" v-model="user.nivel" class="Nivel">
                     <option v-if="user.nivel == '' || user.nivel == null" value="">Seleciona o nivel de acesso</option>
                     <option :selected="user.nivel == 'admin'" value="admin">Administrador</option>
                     <option :selected="user.nivel == 'user'" value="user">Usuario</option>
                  </select>
               </div>
               <div class="Imagem">
                  <div class="EditarImage">
                     <label for="imagem">
                     <input :disabled="!EstadoForm" @change="onFileChange" type="file" id="imagem" class="position-absolute d-none">
                     <i class="fa fa-pencil-square-o"></i>
                     </label>
                     <i class="fa fa-ban"></i>
                  </div>
                  <img class="img-thumbnail" :src="img">
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
               <div v-if="user.id == '' && step == 'password'" class="DivSenha">
                  <div>
                     <label for="senha">Senha</label>
                     <input :disabled="!EstadoForm" v-model="senha.senha1" type="password" name="password" id="senha">
                  </div>
                  <div>
                     <label for="senha">Confima Senha</label>
                     <input :disabled="!EstadoForm" v-model="senha.senha2" type="password" name="password" id="senha">
                  </div>
                  <div class="regras-senha">
                     <ul>
                        <li>A senha deve ter no minimo 6 digitos</li>
                        <li>A Senha deve conter um caracter especial</li>
                        <li>A Senha deve conter número e letra</li>
                     </ul>
                  </div>
               </div>
               <info-user v-if="step === 'info'"/>
               <config v-if="step === 'config'"/>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import UpdatePassword from './UpdatePassword.vue'
import infoUser from './infoUser.vue'
import config from './config.vue'
import {ref,reactive, onMounted} from 'vue';
const props = defineProps({
    SingleUser: Object
})
const user = ref(props.SingleUser);

const emits = defineEmits(['message']);
const step = ref('password')
const StateUpdatePwd = ref(false)

const senha = reactive({
    senha1: null,
    senha2: null
})

const EmitirMessagem = ((message,tipo)=>{
    emits('message',message,tipo)
})

const EstadoForm = ref(false)

const img = ref();

onMounted(async ()=>{
    step.value = 'password'
    if (user.value.id == '') {
        EstadoForm.value = true
    }
    img.value = '/login/image/'+user.value.image
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
    var image = new Image;
    var reader = new FileReader;

    reader.onload = (e) => {
        user.value.image = e.target.result;
        img.value = user.value.image
    };
    reader.readAsDataURL(file);
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
                const regex = /^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%*()_+^&}{:;?.])(?:([0-9a-zA-Z!@#$%;*(){}_+^&])(?!\1)){6,}$/;
                if (regex.test(senha.senha1)) {
                    user.value.password = senha.senha1;
                    return InsertUser(`SaveUser`,user.value)
                } else {
                    emits('message','Formato de senha invalida','info')
                }
            }
        }
    }
})

const InsertUser = ((route,User)=>{
    axios.post(route,{...User})
    .then((response) => {
        if (response.data.tipo == "success") {
            user.value = response.data.data
            EstadoForm.value = false
        }
        emits('message',response.data.message,response.data.type)
    }).catch((erro) => {
        console.log(erro);
    });
})

</script>

<style lang="scss" scoped>
@import '../../../../assets/config/scss/NewUser';
</style>
