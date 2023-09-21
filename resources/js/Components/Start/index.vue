<template>
   <div class="Container">
        <Link :href="route('login')">
            <font-awesome-icon icon="fa-solid fa-left-long" />
        </Link>
        <header>
            <h1><span>S</span>IGESC</h1>
        </header>
        <div class="buttons">
            <button @click="stateForm = 'new'">Nova empresa</button>
            <button @click="stateForm = 'payment'">Pagar licença</button>
            <button @click="stateForm = 'license'">Ativar licença</button>
            <button @click="stateForm = 'renew'">Renovar licença</button>
        </div>
        <Counts :data="accountClient.data" v-if="stateForm == 'counts'"/>
        <form v-if="stateForm == 'license'" class="mini-form">
            <div>
                <div class="Form-control">
                    <label for="nif">Nif:</label>
                    <input type="text" required v-model="license.nif" placeholder="Digita seu nif" id="nif">
                </div>
                <div class="Form-control">
                    <label for="license">Licença:</label>
                    <input type="text" required v-model="license.hash" placeholder="Digita sua licensa" id="license">
                </div>
            </div>
            <div>
                <button @submit.prevent.stop="activeLicense">
                    Validar
                    <i v-if="license.state == 0" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
                    <font-awesome-icon
                    :icon="`fa-solid ${license.state == 1 ? 'fa-check' : license.state == 2 ? 'fa-xmark': ''}`" />
                </button>
            </div>
        </form>
        <renew-license  v-if="stateForm == 'renew'"/>
        <div v-if="stateForm == 'payment'" class="mini-form">
            <div>
                <div class="Form-control">
                    <label for="email">Email:</label>
                    <input type="email" v-model="accountClient.email" placeholder="Digita email" id="email">
                </div>
                <div class="Form-control">
                    <label for="nif">Nif:</label>
                    <input type="text" v-model="accountClient.nif" placeholder="Digita seu nif" id="nif">
                </div>
            </div>
            <div>
                <button @click="RequestAmount">
                    Validar
                    <i v-if="accountClient.state" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <form v-if="stateForm == 'new'" @submit.prevent="submitForm" class="form">
            <div class="Header">
                <div>
                    <font-awesome-icon icon="fa-regular fa-building" />
                    <span>Empresa</span>
                    <div class="step"></div>
                </div>
                <div>
                    <font-awesome-icon icon="fa-regular fa-user" />
                    <span>Perfil</span>
                    <div :class="start.step > 0 ? 'step' : ''"></div>
                </div>
                <div>
                    <font-awesome-icon icon="fa-solid fa-certificate" />
                    <span>License</span>
                    <div :class="start.step > 1 ? 'step' : ''"></div>
                </div>
            </div>
            <div class="Content">
                <company v-if="start.step === 0" />
                <Admin v-else-if="start.step === 1"/>
                <License v-else/>
            </div>
            <div class="Footer">
                <button type="button" v-if="start.step > 0" @click="back" class="Descartar">Voltar</button>
                <button type="submit"  :disabled="inSubmit.state">
                    {{start.step > 1 ? 'Concluir' : 'Avançar'}}
                    <i v-if="inSubmit.state && start.step > 1" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
                </button>
            </div>
        </form>
    </div>
   <Toast/>
</template>


<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import company from "./company.vue";
import License from "./License.vue";
import Admin from "./Admin.vue";
import renewLicense from './licensesPremium/renewLicense.vue'
import { computed, reactive, ref, watch } from "vue";
import { useStore } from 'vuex';
import Counts from './licensesPremium/counts.vue'
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';
import { useForm,Link,router } from '@inertiajs/vue3';
import {Request} from '@/composable/public/RequestApi'
const stateForm = ref(null)
const inSubmit = reactive({
    state: false
})
const accountClient = reactive({
    email: null,
    nif: null,
    data: [],
    state: false
})

const license = ref([])
const store = useStore()
const toast = useToast()
const start = computed(()=> store.getters['Start/start'])

const submitForm = (()=>{
    if (start.value.step > 1) {
        return saveForm()
    }
    nextStep()
})

const {ReqPost} = Request(inSubmit)

const back = (()=> {
    start.value.step --
    store.commit('Start/BackStep', start.value.step)
})
const nextStep = (()=>{
    if (start.value.step > 0) {
        if (start.value.user.password != start.value.user.password1) {
            return message('As senhas não coincidem ','info')
        }
    }
    start.value.step ++
    store.commit('Start/nextStep',start.value.step)
})

const saveForm = (()=>{
    if (!start.value.license || store.state.Start.start.license == null) return message('Selecina seu plano ','info')
    return saveData()
})

async function SaveCompany() {
    inSubmit.state = true
    const response = await ReqPost('SaveCompany',store.state.Start.start)
    inSubmit.state = false
    if (!response.data) return message(response.message,'error');
}

async function saveData(accounts = null) {
    store.state.Start.start.accounts = accounts
    const form = useForm(store.state.Start.start)
    inSubmit.state = true
    await axios.post('/saveCompany',{...store.state.Start.start})
    .then((Response) => {
        message(Response.message,Response.data.type)
        router.get(`/welcome/${Response.data.data.id}`)
    }).catch((err) => {
        message(err.response.message,'warn')
        console.log(err);
    }).finally(()=>{
        inSubmit.state = false
    });
}

async function RequestAmount() {
    accountClient.state = true
    const response = await ReqPost('RequestAmount',accountClient)
    accountClient.state = false
    if (response.message) return message(response.message,'error');
    if (response.client == null) return message('Dados não encontrado, por favor verifique e tente novamente','info');
    accountClient.data = response
    stateForm.value = 'counts'
}

async function activeLicense() {
    license.value.state = 0
    axios.post('activeLicense',{...license.value})
    .then((response) => {
        license.value.state = 1
        console.log(response.data);
        return message(response.data.message,response.data.type)
    }).catch((err) => {
        console.log(err);
        license.value.state = 2
        return message(err.response.data.message,'error')
    });
}

const message = ((message , type)=>{
    toast.add({
        severity: type,
        summary: 'Informação',
        detail: message,
        life: 3000
    })
})

</script>

<style scoped lang="scss">
@import '../../../assets/Start/index';
</style>
