<template>
  <div class="principal">
    <form @submit.prevent.stop="!form.state ? Request() : resetPassword()" class="Modal md:bg-red-700">
        <div class="Header">
            <span>
                <h2>{{$t('words.resetPassword')}}</h2>
            </span>
        </div>
        <div class="Container flex flex-row items-start justify-between">
            <div v-if="!form.state" class="flex-auto flex-col space-y-2 mt-4">
                <span class="flex items-end flex-row w-full">
                    <label for="email">E-mail</label>
                    <input type="email" required v-model="form.email" id="email" placeholder="E-mail">
                </span>
                <span class="flex flex-row items-end w-full">
                    <label class="truncate" for="name">{{$t('words.name')}} </label>
                    <input type="text" required id="name" v-model="form.name" :placeholder="$t('words.name')">
                </span>
            </div>
            <div v-if="!form.state" class="flex-auto flex-col space-y-2 mt-4">
                <span class="flex flex-row items-end w-full">
                    <label class="truncate" for="surname">{{$t('words.surname')}} </label>
                    <input type="text" required v-model="form.surname" id="surname" :placeholder="$t('words.surname')">
                </span>
                <span class="flex flex-row items-end w-full">
                    <label class="truncate" for="phone">{{$t('words.phone')}} </label>
                    <input type="text" required v-model="form.phone" id="phone" :placeholder="$t('words.phone')">
                </span>
            </div>
            <div v-if="form.state" class="flex-auto flex-col space-y-2 mt-4">
                <span class="flex flex-row items-end w-full">
                    <label class="truncate" for="password">{{$t('login.password')}} </label>
                    <input type="password" required class="text-base font-base" v-model="formPassword.password" id="password" :placeholder="$t('login.password')">
                </span>
                <span class="flex flex-row items-end w-full">
                    <label class="truncate" for="confirmPassword">{{$t('phrases.confirmPassword')}} </label>
                    <input type="password" required class="text-base font-base" v-model="formPassword.passwordTwo" id="confirmPassword" :placeholder="$t('phrases.confirmPassword')">
                </span>
            </div>
        </div>
        <div class="Footer">
            <button @click="$emit('closeModal')" type="button" class="Descartar">{{$t('words.close')}}</button>
            <button
            class="truncate" type="submit">{{!form.state ? $t('words.requested'): $t('words.resetPassword')}}</button>
        </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { serviceMessage } from "@/composable/public/messages"
import axios from "axios"
import { ref } from "vue"
import { useI18n } from "vue-i18n"
const {locale} = useI18n()
const {showMessage}= serviceMessage()
const emits = defineEmits(['closeModal'])
interface Form{
    state: boolean,
    email: string,
    name: string,
    surname: string,
    phone: string,
}

interface FormPassword{
    password: string,
    passwordTwo: string,
    check: number,
    userId: number
}

const form = ref<Form>({
    state: false,
    email: '',
    name: '',
    phone: '+244',
    surname: '',
})

const formPassword = ref<FormPassword>({
    password: '',
    passwordTwo: '',
    check: 0,
    userId: 0
})

async function resetPassword(){
    if(formPassword.value.check == 0) {
        form.value.state = false
    }
    if(formPassword.value.password == '' || formPassword.value.passwordTwo == '') return showMessage('Por favor preenche os campos','warn')
    await axios.post(`/auth/resetPassword/${locale.value}/${formPassword.value.check}`,{...formPassword.value})
    .then((response)=>{
        if(response.data.message) showMessage(response.data.message,response.data.type)
        if(response.data.type == 'warn') return form.value.state = false
        if(response.data.type == 'success') emits('closeModal')
    }).catch((error)=>{
        console.error(error)
        showMessage(error.response.data.message,'error')
    })
}

async function Request(){
    await axios.post(`/auth/resetPassword/${locale.value}`,{...form.value}).
    then((response)=>{
        if(response.data.message) return showMessage(response.data.message,response.data.type)
        if(response.data.check) {
            formPassword.value.check = response.data.check
            formPassword.value.userId = response.data.userId
            form.value.state = true
        }else{
            showMessage('Erro','error')
        }
    }).catch((error)=>{
        showMessage(error.response.data.message,'error')
        console.log(error);
    })
}
</script>

<style scoped lang="scss">
.principal{
    @include modal;
    .Modal{
        width: 75%;
        height: 300px;
        @media screen and (max-width: 850px) {
            width: 100%;
            height: 440px;
            .Container{
                display: flex !important;
                flex-direction: column !important;
                >div{
                    width: 100%;
                }
            }
        }
        .Container{
            align-items: flex-start;
            padding: 10px;
            >div{
                flex-direction: column !important;
                @include form-control;
            }
        }
    }
}
</style>
