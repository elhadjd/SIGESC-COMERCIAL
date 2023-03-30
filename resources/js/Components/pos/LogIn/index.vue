<template>
<div class="Principal">
    <div class="Formulario">
        <div class="Header">
            <h1>{{session.nome}}</h1>
            <div>
                <img :src="`/login/image/${user.image}`" alt="">
            </div>

        </div>
        <div class="Container">
            <input @keypress.enter="Entrar" type="password" v-model="EnviarCaixa.password">
        </div>
        <div class="Footer">
            <Link :href="route('pontodevenda')" class="sair">Voltar</Link>
            <button class="entrar" @click="Entrar">Entrar</button>
        </div>
    </div>
</div>
</template>

<script setup>
import useEventsBus from "@/Eventbus"
import { Link } from "@inertiajs/vue3"
import { computed, onMounted, ref } from "@vue/runtime-core"
import axios from "axios"
import { useStore } from "vuex"

const {emit} = useEventsBus();
const emits = defineEmits(['LogIn','message'])
const user = computed(()=>store.state.user)

const props = defineProps({
    session: Object,
})

const store = useStore()

const session = ref([])

const EnviarCaixa = ref({
    id: null,
    password: null
})

const Entrar = (()=>{
    if (EnviarCaixa.value.password === null || EnviarCaixa.value.password === '') {
        emits('message','error','Por favor preencho o campo')
    } else {
        if (EnviarCaixa.value.id === null || EnviarCaixa.value.id === '') {
            emits('message','error','Erro no sistema por favor atualize a tua pagina e tenta novamente')
        }else{
            axios.post(`/PDV/PasswordCash/${EnviarCaixa.value.id}`,{password:EnviarCaixa.value,})
            .then((Response) => {
                emits('message',Response.data.type,Response.data.message)
                if (Response.data.type == 'success') {
                    store.state.Controlo.state = true
                    emit('Logado');
                    return OnMounted()
                }
            }).catch((err) => {
                console.log(err);
            });
        }
    }
})

const OnMounted = onMounted(async ()=>{
    session.value = props.session
    EnviarCaixa.value.id = session.value.id

    if (session.value.password != null || session.value.password !='') {
        if (store.state.Controlo.state === true) {
            emits('LogIn',true)
        } else {
            emits('LogIn',false)
        }
    } else {
        emits('LogIn',true)
    }
})
</script>

<style lang="scss" scoped>
@import '../../../../assets/Pos/css/LogIn/index';
</style>
