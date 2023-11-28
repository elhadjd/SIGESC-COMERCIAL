<template>
    <ButtonVue @click="verifyEmail" :type="'submit'" :class="!props.company.email_verify?.isVerify ? 'btnVerify': 'isVerify'">
        <span v-if="!props.company.email_verify?.isVerify">
            Verificar email
            <FontAwesomeIcon v-if="stateProgress == 1" icon="fa-solid fa-spinner" shake />
            <font-awesome-icon v-else icon="fa-regular fa-envelope" fade />
        </span>
        <span v-else>
            E-mail verificado
            <font-awesome-icon icon="fa-solid fa-envelope-circle-check" />
        </span>
    </ButtonVue>
    <div class="Principal" v-if="stateProgress > 1">
        <div class="Modal">
            <div class="Header">
                <h2>Verificação de email</h2>
            </div>
            <div class="Container">
                <label for="code">Codigo</label>
                <input type="text" v-model="code" id="code" placeholder="Code">
            </div>
            <div class="Footer">
                <button @click="verifyEmail">
                    Não recebi email
                    <FontAwesomeIcon v-if="stateProgress == 1" icon="fa-solid fa-spinner" shake />
                </button>
                <button @click="stateProgress = 0" class="Descartar">Fechar</button>
                <button @click="()=>validateCode(code)">
                    Confirmar
                    <FontAwesomeIcon v-if="stateProgress == 3" icon="fa-solid fa-spinner" shake />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang='ts'>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import ButtonVue from '@/ui/button.vue'
import { onMounted, ref } from 'vue'
import axios from "axios";
import { serviceMessage } from "@/composable/public/messages";
const emits = defineEmits(['selectCompany'])
const props = defineProps({
    company: Object
})
const code = ref<string>('')
const {showMessage } = serviceMessage()
const stateProgress = ref<number>(0)
onMounted(()=>{
    console.log(props.company);
})

const verifyEmail = async()=>{
    if(props.company.email == null||props.company.email == '') return showMessage('O campo email esta vazio por favor preenche e tenta novamente ','info')
    stateProgress.value = 1
    if (props.company.email_verify?.isVerify) return
    await axios.post(`/sendCodeVerifyEmail/${props.company.id}`)
    .then((response) => {
        if(response.data.message) {
            return showMessage(response.data.message,response.data.type)
        }
    }).catch((err) => {
        console.log();
        showMessage('Erro de servidor ','error')
    }).finally(()=>{
        stateProgress.value = 2
    });
}

async function validateCode(code: string) {
    stateProgress.value = 3
    await axios.post(`/validateCode/${props.company.id}/${code}`)
    .then((response) => {
        showMessage(response.data.message,response.data.type)
        if(response.data.type == 'success') {
            stateProgress.value = 0
            props.company.email_verify = response.data.data
            return
        }
        stateProgress.value = 5
    }).catch((err) => {
        showMessage('Erro de servidor '+err.response.data.message,'error')
    });
}
</script>

<style scoped lang="scss">
.btnVerify{
    @include botao_normal;
}
.isVerify{
    @include botao_descartar;
    color: var(--bgButtons);
}
.Principal{
    @include modal
}
</style>
