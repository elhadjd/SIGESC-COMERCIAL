<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <h2>{{$t('words.user')}}s</h2>
            <button @click="CreateUser">{{`${$t('words.added')} ${$t('words.user')}`}}</button>
         </div>
         <div class="Header-right">
            <span class="p-input-icon-right d-flex w-100">
                <i class="pi pi-search" />
                <input @keyup="Pesquisar" type="text" :placeholder="$t('words.search')" class="Pesquisar"/>
            </span>
         </div>
      </div>
      <div class="Container">
         <div class="FormList">
            <div class="Titulos">
               <strong>{{$t('words.image')}}</strong>
               <strong>Email</strong>
               <strong>{{$t('words.name')}}</strong>
               <strong>{{$t('words.surname')}}</strong>
               <strong>{{$t('words.level')}}</strong>
               <strong>{{$t('words.state')}}</strong>
            </div>
            <div class="ListUsers">
               <div v-for="user in users" @click="ShowUserSingle(user)" :key="user.id" class="Users">
                  <span class="Imagem">
                  <img :src="`/login/image/${user.image}`">
                  </span>
                  <span>{{user.email}}</span>
                  <span>{{user.name}}</span>
                  <span>{{user.surname}}</span>
                  <span>{{user.roles[0]?.name}}</span>
                  <span>{{user.state}}</span>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "@vue/runtime-core";
import axios from "axios";
const StoreUsers = ref()

const users = ref([])

const emits = defineEmits(['message','ShowSingleUser'])

const Onmounted = onMounted(async()=>{
    const locale = localStorage.getItem('locale');
    await axios.get(`getConfig/${locale || 'en'}`)
    .then((Response) => {
        StoreUsers.value = Response.data.users
        users.value = Response.data.users
    }).catch((err) => {
        console.log(err);
    });
})

const user = reactive({
    id: '',
    surname: null,
    name: null,
    email: null,
    password: null,
    company_id: null,
    image: String('registro-sem-imagen.png'),
    state: 'active',
    data: null,
    remember_token: null,
    created_at: null,
    updated_at: null
})

const ShowUserSingle = ((event)=>{
    emits('ShowSingleUser',event)
})

const CreateUser = (()=>{
    axios.post('newUser')
    .then((response) => {
        emits('ShowSingleUser',response.data)
    }).catch((err) => {
        console.log();
    });

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
