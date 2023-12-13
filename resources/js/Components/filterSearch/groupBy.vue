<template>
    <div class="groupBy">
        <div class="">
            <button @click="AbrirForm">
                <i class="fa fa-bars"></i>
                <span>{{$t('words.group')}}</span>
            </button>
        </div>
        <div v-if="stateForm" class="Form1 shadow-lg">
            <div>
                <div class="Opcoes" @click="option('users')">{{$t('words.user')}}</div>
                <div class="SubOpcao shadow-lg" v-if="opcaos == 'users'">
                    <label v-for="item in lists" :key="item.id">
                        <input type="radio" class="InputRadio" :id="item.id" :value="item.id" v-model="user" @click="Select(item.id,'user_id')"/>
                        <span :for="item.id">{{item.surname}}</span>
                    </label>
                </div>
            </div>
            <div>
                <div class="Opcoes" @click="option('Estado')">{{$t('words.state')}}</div>
                <div v-if="opcaos == 'Estado'" class="SubOpcao shadow-lg">
                    <label for="Pago">
                        <input type="radio" class="InputRadio" id="Pago" value="Pago" v-model="state" @click="Select('Pago','state')"/>
                        <span>{{$t('words.pay')}}</span>
                    </label>
                    <label>
                        <input type="radio" class="InputRadio" id="Anulado" value="Anulado" v-model="state" @click="Select('Anulado','state')"/>
                        <span for="Anulado">{{$t('words.annul')}}</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from '@vue/runtime-core'
import axios from 'axios';
import { useStore } from 'vuex';

const emits = defineEmits(['ListaDefault'])
const store = useStore()
const stateForm = ref()
const opcaos = ref(null);
const user = ref()
const client = ref()
const state = ref()

const ListaDefault = ref([])
const columns = reactive({
    colun0: null,
    colun1: null,
    value: null,
})
const lists = ref()

const users = computed(()=>store.state.publico.company.users)

const AbrirForm = (()=>{
    stateForm.value = !stateForm.value
    columns.colun0 = null
    columns.colun1 = null
})

const Select = ((event,outro)=>{
    axios.get(`groupBy/${event}/${outro}`)
    .then((Response) => {
        ListaDefault.value = Response.data
        emits('ListaDefault',ListaDefault.value)
    }).catch((err) => {
        console.log(err);
    });
})


const SearchClient = ((event)=>{
    if (event.target.value.length == 3) {
    } else if(event.target.value.length > 3) {
        const filtrar = lists.value.find(o=>{
            return o.nome.includes(event.target.value)
        })
        lists.value = filtrar
    }
})


const option = ((event)=>{
    if (event == opcaos.value)return opcaos.value = null
    opcaos.value = event
    lists.value = users.value;
})
</script>

<style scoped lang="scss">
@import '../../../assets/filterSearch/css/filter';
</style>
