<template>
    <div class="Principal">
        <Auth :Auth="auth" v-if="!auth.isAuth" @access="access" @close="$emit('close')"/>
        <div v-else class="Container">
            <form @submit.prevent.stop="HandleActiveLicense" class="Form-container">
                <h1>Ativar a licença</h1>
                <div class="Form-control">
                    <input type="number" required v-model="license" placeholder="Preencha a licença...">
                    <button type="submit">Ativar</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import {onMounted, ref } from '@vue/runtime-core'
import axios from 'axios'
import Auth from './Auth.vue'
const license = ref('');
const emits = defineEmits(['message'])
const auth = ref({
    isAuth: false,
    email: '',
    password: '',
})

const HandleActiveLicense = ((e)=>{
    axios.post('/ActiveLicense',{license:license.value})
    .then((response) => {
        emits('message',response.data.message,response.data.tipo)
    }).catch((err) => {
        emits('message','Aconteçeu um erro no sistema','error')
        console.log(err);
    });
})

const access = (()=>{
    auth.value.isAuth = true
})
</script>

<style scoped lang="scss">
.Principal{
    width: 100%;
    height: 100%;
    background-color: rgba(204, 204, 204, 0.064);
    display: flex;
    justify-content: center;
    align-items: center;
    .Container{
        width: 500px;
        height: 200px;
        margin: 0 auto;
        background-color: white !important;
        box-shadow: rgb(100, 100, 111, 0.2) 0px 7px 29px 0px;
        .Form-container{
            padding: 15px;
            height: 100%;
            h1{
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 25px;
                color: rgba(0, 0, 0, 0.649);
                font-weight: bold;
            }
            .Form-control{
                width: 100%;
                display: flex;
                justify-content: center;
                align-items: flex-end;
                height: 75%;
                flex-direction: column;
                input{
                    border: none;
                    outline-color: #017e84;
                    border-radius: 3px;
                    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
                    margin-bottom: 1rem;
                    width: 100%;
                }
                button{
                    @include botao_normal
                }
            }
        }
    }
}

</style>