<template>
   <div class="Principal">
      <div class="form-container">
         <div class="Header">
            <button>Contactos</button>
         </div>
         <div class="Content">
            <div class="Name-Img-control">
               <div class="form-nome">
                  <input type="text" v-model="form.name" placeholder="Nome completo" id="name">
                  <input type="text" v-model="form.cargo" placeholder="Cargo" id="cargo">
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
                     <input type="email" name="email" id="email" v-model="form.email" >
                  </div>
                  <div class="form-Control">
                     <label for="phone">Telefone: </label>
                     <input type="text" name="phone" id="phone" v-model="form.phone" >
                  </div>
                  <div class="form-Control">
                     <label for="celular">Celular: </label>
                     <input type="text" name="celular" v-model="form.celular" id="celular">
                  </div>
               </div>
               <div class="form-content">
                  <div class="form-Control">
                     <label for="department">Dapartamento: </label>
                     <input @click="DropShow('department')" type="text" name="department" id="department" :value="form.workers_department_id?.name" >
                     <div v-if="drop == 'department'" class="drop">
                        <span @click="selectDepartment(department)" v-for="department in departments" :key="department.id">
                        {{department.name}}
                        </span>
                     </div>
                  </div>
                  <div class="form-Control">
                     <label for="manager">Gerente: </label>
                     <input @click="DropShow('manager')" type="text" name="manager" id="manager" :value="form.manager?.surname" >
                     <div v-if="drop == 'manager'" class="drop">
                        <span @click="selectManager(manager)" v-for="manager in users" :key="manager.id">
                        {{manager.nivel != 'admin' ? '' : manager.surname}}
                        </span>
                     </div>
                  </div>
                  <div class="form-Control">
                     <label for="train">Trenador: </label>
                     <input @click="DropShow('trainer')" type="text" name="train" id="train" :value="form.trainer?.surname" >
                     <div v-if="drop == 'trainer'" class="drop">
                        <span @click="selectTrainer(trainer)" v-for="trainer in users" :key="trainer.id">
                        {{trainer.surname}}
                        </span>
                     </div>
                  </div>
               </div>
            </div>
            <div class="buttons">
               <button>Atividades</button>
            </div>
         </div>
         <div class="Footer">
            <div class="form-Control">
               <label for="hour">Horario de trabalho: </label>
               <input type="text" @click="DropShow('hours')" name="hour" id="hour" v-model="form.hourWork" >
               <div v-if="drop == 'hours'" class="drop">
                  <span @click="selectHours(hour)" v-for="hour,index in hours" :key="index">
                  {{hour.name}}
                  </span>
               </div>
            </div>
            <div class="form-Control">
               <label for="salary">Salario: </label>
               <input name="salary" id="salary" :type="type" :value="numberStr"
               @input="onInput" @blur="onBlur" @focus="onFocus">
            </div>
            <div class="form-Control">
               <label for="dailyExpense">Dispesa diaria: </label>
               <input name="dailyExpense" v-model="form.dailyExpense" id="dailyExpense" >
            </div>
            <div class="form-Control">
               <label for="users_id">Usuario correspondente: </label>
               <input name="users_id"  @click="DropShow('users_id')" :value="form.user?.surname" id="users_id" >
               <div v-if="drop == 'users_id'" class="drop">
                  <span @click="selectUser(user)" v-for="user in users" :key="user.id">
                  {{user.surname}}
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import useEventsBus from '@/Eventbus'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { onMounted, reactive, ref, watch } from '@vue/runtime-core'
import {useUploadImage} from '@/composable/public/UploadImage'
import axios from 'axios'
import { useStore } from 'vuex'
const store = useStore()
const users = ref([])
const {bus,emit} = useEventsBus()
const emits = defineEmits(['message'])
const props = defineProps({
    worker: Object
})

const hours = ref([
    {name: "Padrão 66 Hora por semana"},
    {name: "Basico 40 Hora por semana"},
    {name: "Extra 72 Hora por semana"}
])

const departments = ref([])

const drop = ref("")

const image = ref({
    img: `/worker/image/${props.worker.image}`
})
const form = ref(props.worker)

const {createImg,onFileChange} = useUploadImage(form.value,image.value)


onMounted(()=>{
    store.state.StateProgress = true
    axios.get('/config/users')
    .then((response) => {
        users.value = response.data.users
        store.state.StateProgress = false
        getDepartments()
    }).catch((err) => {
        store.state.StateProgress = false
        emits('message','Aconteceu um erro ao selecionar os funcionario s333 ','info')
        console.log(err);
    });
})

const getDepartments=(()=>{
    axios.get('getDepartments')
    .then((response) => {
        departments.value = response.data
        filterDepartments()
    }).catch((err) => {
        console.log(err);
    });
})

const filterDepartments = (()=>{
    const filter = departments.value.filter((department)=>{
        return department.id == form.value.workers_department_id
    })
    if (filter.length>0) {
        return form.value.workers_department_id = filter[0]
    }
})

const DropShow = ((type)=>{
    if (drop.value === type) {
        return drop.value = ""
    }
    drop.value = type
})

const selectDepartment = ((department)=>{
    form.value.workers_department_id = department
    return drop.value = ""
})

const selectHours = ((hour)=>{
    form.value.hourWork = hour.name
    return drop.value = ""
})

const selectTrainer = ((trainer) =>{
    form.value.trainer = trainer
    return drop.value = ""
})

const selectManager = ((manager)=>{
    form.value.manager = manager
    return drop.value = ""
})

const selectUser = ((user)=>{
    form.value.user = user
    return drop.value = ""
})


const format = (n)=>{
    return new Intl.NumberFormat('PT-AO', {
    style: 'currency',
    currency: 'AOA',
  }).format(n);
}

const number = ref(0);

const type = ref('text');

const numberStr = ref(format(number.value));

const onInput = ({ target }) => {
    if (target.value != '' ) {
    number.value = parseInt(target.value);
    }
};
const onFocus = () => {
    if (number.value != '' ) {
    numberStr.value = number.value;
    type.value = 'number';
    }
};
const onBlur = () => {
    if (number.value != '' ) {
        type.value = 'text';
        numberStr.value = format(number.value);
        form.value.salary = number.value
    }
};

const RemoveImage = () => {
    form.value.image = "/worker/image/produto-sem-imagem.png"
}

watch(()=>bus.value.get('SaveWorker'),(payload)=>{
    return SaveWorker()
})


const SaveWorker = (()=>{
    if (form.value.name == "") return emits('message','Nome do funcionario e Obligatorio','info')
    axios.post(`saverWorker/${form.value.id}`,{...form.value}).then((response) => {
        emit('closeFormWorker',+1)
        emits('message',response.data.message,response.data.type)
    }).catch((err) => {
        emits('message','Aconteceu um erro no sistema por favor tenta novamente','error')
        console.log(err);
    });
})

</script>

<style scoped lang="scss">
@import '../../../assets/funcionarios/scss/NewWorker';
</style>
