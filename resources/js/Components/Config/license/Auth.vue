<template>
    <div class="Principal">
        <div class="Contact">
            <h1>Por favor contact o administrador do sistema evita bloqueio do sistema</h1>
            <span>+244 929147445</span>
        </div>
        <div class="Modal">
            <form @submit.prevent.stop="HandelSubmitForm">
                <div class="Header">
                    <h2><i class="fa fa-info"></i> Estas numa aria <strong>Reservada</strong> para o administrador do sistema</h2>
                </div>
                <div class="Container">
                    <div class="form-Control">
                        <label for="email">E-mail:</label>
                        <input @change="handelChangeInput" v-model="form.email" type="email" id="email">
                    </div>
                    <div class="form-Control">
                        <label for="password">Senha:</label>
                        <input @change="handelChangeInput" v-model="form.password" type="password" id="password">
                    </div>
                </div>
                <div class="Footer">
                    <p v-show="tentative.tentative >0">Resta no maximo {{tentative.number - tentative.tentative}} vezes</p>
                    <button type="button" @click="$emit('close')" class="Descartar">Voltar</button>
                    <button type="submit">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import { onMounted, ref } from "@vue/runtime-core"
import axios from "axios";
const emits = defineEmits(['message','access','close'])

const form = ref(props.Auth)

const tentative = ref({
    number: 3,
    tentative: 0,
})

const props = defineProps({
    Auth: Object,
})

const handelChangeInput = ((e)=>{
    props.Auth[e.id] = e.target.value
})

const HandelSubmitForm = (async ()=>{
    tentative.value.tentative ++
    await axios.post('/Programador',form.value)
    .then((response) => {
        if (response.data != 1) {
            emits('message','error','Dados não corresponde')
            if (tentative.value.tentative === tentative.value.number) {
                emits('close')
                return false
            }
             return false
        }
        emits('access')
    }).catch((err) => {
        console.log(err);
    });
})
</script>

<style scoped lang="scss">
.Principal{
    flex-direction: column;
    .Contact{
        display: flex;
        flex-direction: column;
        align-items: center;
        color: rgba(0, 0, 0, 0.597);
        h1{
            font-size: 25px;
            font-weight: bold;
        }
        span{
            font-size: 15px;
            font-weight: bold;
        }
    }
    @include modal;
    .Modal{
        height: unset !important;
        max-height: 250px;
        .Header{
            height: unset;
            height: 15%;
            h2{
                display: flex;
                i{
                    width: 18px;
                    height: 18px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background-color: #017e84;
                    margin-right: 1rem;
                    border-radius: 50%;
                    color: white;
                }
                strong{
                    color: #017e84;
                    margin: 0px 3px 0px 3px;
                }
            }
        }
        .Container{
            display: flex;
            flex-direction: column;
            align-items: unset;
            height: unset;
            height: 70%;
            .form-Control{
                display: flex;
                flex-direction: column;
                margin-bottom: 1rem;
                label{
                    font-weight: bold;
                    margin-bottom: 0.5rem;
                }
                input{
                    box-shadow: unset;
                    border: none;
                    border-radius: 3px;
                    background-color: unset;
                    padding: 8px !important;
                    outline-color: #017e84;
                    font: 12pt arial;
                    font-weight: 500;
                    color: #777 !important;
                    box-shadow: #017e842f 0px 7px 29px 0px !important;
                }
            }
        }
        .Footer{
            height: unset;
            height: 15%;
        }
    }

}
</style>
