<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <h2>Lista de Usuario</h2>
            <button @click="CreateUser">Adicionar Usuario</button>
         </div>
         <div class="Header-right">
            <span class="p-input-icon-right d-flex w-100">
                <i class="pi pi-search" />
                <input @keyup="Pesquisar" type="text" placeholder="Pesquisar..." class="Pesquisar"/>
            </span>
         </div>
      </div>
      <div class="Container">
         <div class="FormList">
            <div class="Titulos">
               <strong>Imagem</strong>
               <strong>Email</strong>
               <strong>Nome</strong>
               <strong>Apelido</strong>
               <strong>Nivel Acesso</strong>
               <strong>Estado</strong>
            </div>
            <div class="ListUsers">
               <div v-for="user in users" @click="ShowUserSingle(user)" :key="user.id" class="Users">
                  <span class="Imagem">
                  <img :src="`/login/image/${user.image}`">
                  </span>
                  <span>{{user.email}}</span>
                  <span>{{user.name}}</span>
                  <span>{{user.surname}}</span>
                  <span>{{user.nivel}}</span>
                  <span>{{user.state}}</span>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "@vue/runtime-core";
const StoreUsers = ref()

const users = ref([])

const emits = defineEmits(['message','ShowSingleUser'])

const Onmounted = onMounted(()=>{
    axios.get('getConfig')
    .then((Response) => {
        StoreUsers.value = Response.data.users
        users.value = Response.data.users
    }).catch((err) => {
        console.log(err);
    });
})

const user = reactive({
    id: '',
    apelido: null,
    nome_completo: null,
    email: null,
    password: null,
    id_empresa: null,
    imagem: String('registro-sem-imagen.png'),
    estado: 'Ativo',
    data: null,
    remember_token: null,
    created_at: null,
    updated_at: null
})

const ShowUserSingle = ((event)=>{
    emits('ShowSingleUser',event)
})

const CreateUser = (()=>{
    emits('ShowSingleUser',user)
})

const Pesquisar = ((event)=>{
    const Filter = StoreUsers.value.filter(object=>{
        return object.apelido.toLowerCase().includes(event.target.value.toLowerCase())
        || object.nome_completo.toLowerCase().includes(event.target.value.toLowerCase())
        || object.email.toLowerCase().includes(event.target.value.toLowerCase())
    })
    if (Filter.length > 0) {
        users.value = Filter
    }else{
        emits('message','Nemhum usuario encontrado com este nome','info')
    }
})
</script>

<style lang="scss" scoped>
@import '../../../../assets/config/scss/users';
</style>
