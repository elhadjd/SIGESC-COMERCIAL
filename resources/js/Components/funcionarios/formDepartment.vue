<template>
    <div class="principal-form-department">
        <div class="form-container">
            <div class="Main">
                <div class="Name-Img-control">
                    <div class="form-nome">
                        <input type="text" v-model="department.name" placeholder="Nome de departamento">
                    </div>
                </div>
            </div>
            <div class="Footer">

            </div>
        </div>
    </div>
</template>

<script setup>
import useEventsBus from '@/Eventbus'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { onMounted, ref, watch } from '@vue/runtime-core'
import axios from 'axios'

const {bus} = useEventsBus()
const emits = defineEmits(['message','closeFormDepartment'])
const props = defineProps({
    department: Object
})
const department = ref({
    id: "",
    name: null,
    Workers: 0,
})
onMounted(()=>{
    if (props.department.length >0) {
        department.value = props.department[0]
    }
})



watch(()=>bus.value.get('saveDepartment'),(payload)=>{
    saveDepartment()
})

const saveDepartment = (()=>{
    if (department.value.name == null || department.value.name == "") return emits('message','Nome do departamento obligatorio','info')
    axios.post('saveDepartment',{...department.value})
    .then((response) => {
        emits('closeFormDepartment')
        // emits('message',response.data.message,response.data.type)
    }).catch((err) => {
        console.log(err);
    });
})
</script>

<style lang="scss" scoped>
@import '../../../assets/funcionarios/scss/formDepartment';
</style>
