<template>
<Toast />
<forget-password v-if="resetPassword" @closeModal="resetPassword = false"/>
<div class='principal relative'>
    <!-- <span class="absolute h-10 w-10 bg-white rounded-full">s</span> -->
    <header class="flex">
        <h1><span>S</span>IGESC</h1>
    </header>
    <div class='container'>
        <div class="info">
            <div class='content'>
                <div class="text-sm font-sm" v-for="(infoItem, index) in $tm('login.info')" :key="index">
                    <div><font-awesome-icon icon="fa-solid fa-circle-check" /></div>
                    <div>
                        <span class="truncate">{{infoItem.title}}</span>
                        <span>{{infoItem.description}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form">
            <form class="" @submit.prevent="submit">

                <header>
                    <span>{{$t('login.title')}}</span>
                </header>

                <div class='box'>
                    <label htmlFor="email">Email:</label>
                    <input type="email" v-model="form.email" id='email' />
                </div>

                <div class='box'>
                    <label htmlFor="password">{{ $t('login.password') }}:</label>
                    <input type="password" v-model="form.password" id='password' />
                </div>

                <div class='checkbox'>
                    <input type="checkbox" v-model="form.session" name="checkbox" id="checkbox" />
                    <label htmlFor="checkbox">Permanece connectado por uma semana</label>
                </div>
                <div class='buttons'>
                    <button type='submit flex items-center justify-between'>
                        {{$t('words.enter')}}
                        <i v-if="form.processing" class="fa fa-spinner fa-pulse fa-3x  fa-fw" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="flex flex-row justify-between space-x-2">
                    <Link :href="route('startCompany')">{{$t('words.start')}} {{$t('words.one')}} {{$t('words.company')}}</Link>
                    <button @click="resetPassword = true" class="text-base font-base truncate text-[--bgButtons]" type="button">Forget password</button>
                </div>
                <div class="relative w-full mt-3 flex justify-center selectLocale">
                    <button type="button" @click="languages.state = !languages.state">{{form.locale.name || 'Languages'}}</button>
                    <div v-if="languages.state" class="drop w-full">
                        <span v-for="language,index in languages.data" @click="()=>selectLanguage(language)" :key="index">{{language.name}}</span>
                    </div>
                </div>
                <div class="flex justify-between w-full h-auto mt-2 md:mt-14 items-center">
                    <a class="text-gray-500" href="https://sisgesc.net">{{$t('phrases.visitSite')}}</a>
                    <a class="text-gray-500" href="https://sisgesc.net/contact">{{$t('phrases.contactUs')}}</a>
                </div>
            </form>
        </div>
    </div>
</div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import forgetPassword from './forgetPassword.vue'
import { useForm,Link } from "@inertiajs/vue3";
import { onMounted, ref } from "@vue/runtime-core";
import axios from 'axios';
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import { useI18n } from 'vue-i18n';
const { t,te,tm,locale } = useI18n();
const resetPassword = ref(false)

const form = useForm({
    email: null,
    password: null,
    path: window.location.pathname,
    locale: [],
    session: false,
});
const toast = useToast();
const connection = ref({
  state: false,
});


const languages = ref({
    state: false,
    data: [
        {
            local: 'fr',
            name: 'Francé'
        },
        {
            local: 'pt',
            name: 'Portugaise'
        },
        {
            local: 'en',
            name: 'Ingles'
        }
    ]
})


const selectLanguage = ((language)=>{
    locale.value = language.local
    form.locale = language
    localStorage.setItem('locale',language.local)
    languages.value.state = false
})

const submit = async() => {
    if(!form.processing){
        if (form.email == null || form.email == '') {
            document.querySelector("#email").style.borderBottom = "1px solid red";
        } else if (form.password == null || form.password == '') {
            document.querySelector("#password").style.borderBottom = "1px solid red";
        } else {
            form.post(`/auth/logar/${form.locale.local || 'en'}`, {
                onSuccess: (Response) => {
                    toast.add({
                        severity: "error",
                        summary: "menssagem de erro",
                        detail: Response.props.erro || 'Erro ao fazer login, por favor contacta o administrador',
                        life: 5000,
                    });
                },
            });
        }
    }

};
</script>

<style scoped lang="scss">
@import '../../../assets/login/css/login';
.form{
    height: auto !important;
    max-height: 500px;
    .box{
        margin-bottom: 10px !important;

    }
}
.selectLocale{
    @include form-control;
    @include dopDown;
    .drop{
        margin-left: unset;
        margin-top: 3px;
    }
}
</style>
