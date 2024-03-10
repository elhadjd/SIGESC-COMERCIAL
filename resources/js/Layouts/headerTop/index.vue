<template>
  <div class="bg-white user-select-none relative w-full h-10 space-x-2 flex flex-row justify-between items-enter px-2 md:px-6">
    <logoVue/>
    <div class="flex flex-row items-center space-x-5 ">
        <span class="hidden md:flex truncate font-base text-base text-gray-500">{{user.surname}}</span>
        <span class="w-10 h-10">
            <img class="bg-white w-full h-full rounded-full" :src="element.img" :alt="user.surname" />
        </span>
        <span class="cursor-pointer logout" @click="logout">Logout</span>
        <img @click="openModalLanguages" class="bg-white w-10 rounded-full cursor-pointer" :src="`/app/image/${user.user_language.code}.png`" alt="">
        <div v-if="languages.state" class="absolute z-50 cursor-pointer w-48 h-32 bg-white drop-shadow-md rounded right-8 top-14">
            <div @click="changeLanguage(language)" v-for="language in languages.data" :key="language.local" class="p-2 px-2 border-b hover:bg-gray-100 text-gray-600 font-base text-base">
                <span>{{language.name}}</span>
            </div>
        </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";
import logoVue from './logo.vue'
import {UserTypeScript} from '@/types/userTypeScript'
import { reactive, ref } from 'vue';
import { getImages } from '@/composable/public/getImages';
import { Inertia } from "@inertiajs/inertia";
import axios from "axios";
import { serviceMessage } from "@/composable/public/messages";
import { useI18n } from "vue-i18n";
const {t, locale} = useI18n()
const props = defineProps({
    user: Object
})
const {showMessage} = serviceMessage()
const openModalLanguages = (()=>{
    languages.value.state = languages.value.state ? false : true
})

const changeLanguage = ((language: any)=>{
    axios.post(`/config/changeLanguage/${props.user.id}`,{...language})
    .then((response) => {
        showMessage(response.data.message);
        locale.value = response.data.data.code
        localStorage.setItem('locale',response.data.data.code)
        location.reload()
    }).catch((err) => {
        console.log(err);
        showMessage(t('message.serverError'),'error')
    })
    .finally(()=>{
        languages.value.state = false
        
    });
})

const languages = ref<{data: {name: string,local: string,image: string,}[], state: boolean}>({
    state: false,
    data: [
        {
            image: 'Portugais',
            local: 'pt',
            name: 'Portugais'
        },
        {
            image: 'Ingles',
            local: 'en',
            name: 'Ingles'
        },
        {
            image: 'Frances',
            local: 'fr',
            name: 'Francé'
        }
    ]
})
const logout = () => {
  Inertia.post("/auth/logout");
};

const element = reactive({
    img: '/login/image/' + props.user.image
})
const {getImage} = getImages(element);
</script>

<style scoped lang="scss">
.logout{
            color: var(--bgButtons);
}
</style>
