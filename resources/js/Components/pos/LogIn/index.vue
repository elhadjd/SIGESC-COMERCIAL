<template>
<div class="Principal">
    <div class="Formulario">
        <div class="Header">
            <h1>{{user.nome}}</h1>
            <div>
                <img :src="`/login/image/${user.image}`" alt="">
            </div>

        </div>
        <div class="Container">
            <input @keypress.enter="Entrar" type="password" placeholder="Digite seu codigo secreto" v-model="EnviarCaixa.password">
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
const emits = defineEmits(['login','message'])
const user = computed(()=>store.state.publico.user)

const props = defineProps({
    session: Object,
})

const store = useStore()

const Session = ref([])

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
                    store.state.pos.Controlo.state = true
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
    Session.value = props.session
    EnviarCaixa.value.id = Session.value.id

    if (Session.value.password != null || Session.value.password !='') {
        if (store.state.pos.Controlo.state === true) {
            emits('login',true)
        } else {
            emits('login',false)
        }
    } else {
        emits('login',true)
    }
})
</script>

<style lang="scss" scoped>
@import '../../../../assets/Pos/css/LogIn/index';
</style>
