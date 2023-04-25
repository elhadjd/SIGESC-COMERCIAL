<template>
  <Toast/>
  <Progress v-if="store.state.StateProgress"/>
    <div class="mt-0" style="height:100vh;width: 100vw">
        <Headers @modulos="modulos" :menus="menus"/>
        <div class="Containers">
            <home v-if="modul == 'Funcionarios'" @message="message"/>
            <salarySum v-if="modul == 'Relatório'" @message="message"/>
            <department v-if="modul == 'Departamentos'" @message="message"/>
        </div>
    </div>
</template>

<script setup>
import Headers from '../Layouts/header.vue'
import home from '@/Components/funcionarios/Menu.vue'
import salarySum from '@/Components/funcionarios/SalarySum.vue'
import department from '@/Components/funcionarios/Department.vue'
import Index from '@/layouts/index.vue'
import { ref } from '@vue/runtime-core';
import { useToast } from 'primevue/usetoast';
import Progress from '../components/confirmation/progress.vue'
import Toast from 'primevue/toast'
import { useStore } from 'vuex';
const store = useStore()
const modul = ref('Funcionarios')
const toast = useToast()
const menus = ref([
    {"menu": "Funcionarios"},
    {"menu": "Departamentos"},
    {"menu": "Relatório"},
])

const message = ((message,type)=>{
    toast.add({
        severity: type,
        summary: 'Message',
        detail: message,
        life: 5000,
    })
})


const modulos = ((event)=>{
    modul.value = event
})
</script>

<style lang="scss">
*{
    font-family: 'Roboto', sans-serif;
    box-sizing: border-box;
    user-select: none;
}
.Containers{
    width: 100%;
    height: 92%;
}
</style>
