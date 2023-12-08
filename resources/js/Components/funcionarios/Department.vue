<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <span @click="formDepartment.state ? formDepartment.state = false:''" :class="formDepartment.state?'Voltar':''">
               <FontAwesomeIcon v-if="formDepartment.state" icon="fa-solid fa-outdent" />
               <h2>Departamentos</h2>
            </span>
            <span>
               <button @click="!formDepartment.state ? showFormDepartment():saveDepartment()">
                  <FontAwesomeIcon :icon="!formDepartment.state ? 'fa-solid fa-plus':'fa-solid fa-floppy-disk'"/>
                  {{!formDepartment.state ? "Novo departamento": "Guardar"}}
               </button>
            </span>
         </div>
         <div class="Header-right">
            <span v-if="!formDepartment.state" class="p-input-icon-right w-100">
            <i class="pi pi-search" />
            <input type="search" @keyup="search" placeholder="Digite nome do departamento">
            </span>
         </div>
      </div>
      <div class="Container">
         <div v-if="!formDepartment.state" class="Main">
            <div v-for="department in departments" :key="department.id" class="form-Content">
               <div @click="showDepartment(department)">
                  <span>{{department.name}}</span>
               </div>
               <div>
                  <button>
                  {{department.workers?.length}}
                  Funcionarios
                  </button>
               </div>
            </div>
         </div>
         <FormDepartment :department="formDepartment.department" @message="sendMessage" @closeFormDepartment="getDepartments" v-else/>
      </div>
   </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import FormDepartment from './formDepartment.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import axios from "axios";
import useEventsBus from "@/Eventbus";

const departments = ref([])
const workers = ref()
const storeDepartment = ref([])
const {emit} = useEventsBus()
const emits = defineEmits('message')
const formDepartment = ref({
    department: [],
    state: false,
})

const showDepartment = ((department)=>{
    formDepartment.value.department[0] = department
    formDepartment.value.state = true
})

onMounted(()=>{
    getDepartments()
})

const getDepartments = (()=>{
    axios.get('getDepartments')
    .then((response) => {
        formDepartment.value.state = false
        storeDepartment.value = response.data
        departments.value = response.data
    }).catch((err) => {
        console.log(err);
    });
})


const showFormDepartment = (()=>{
    formDepartment.value.state = true
})

const saveDepartment = (()=>{
    emit('saveDepartment',1+1)
})

const sendMessage = ((message,type)=>{
    emits('message',message,type)
})

const search = ((event)=>{
    const filter = storeDepartment.value.filter((department)=>{
        return String(department.name).toLowerCase().includes(event.target.value.toLowerCase())
    })
    departments.value = filter
})


</script>

<style scoped lang="scss">
@import '../../../assets/funcionarios/scss/department';
</style>
