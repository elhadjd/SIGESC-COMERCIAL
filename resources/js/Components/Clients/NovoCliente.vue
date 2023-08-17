<template>
<div class="principal">
    <div class="Header">
        <div class="Header-left">
            <h2>Cliente</h2>
            <div class="buttons">
                <button @click="saveClient">Guardar</button>
                <button class="botao_descartar" @click="$emit('close')">Fechar</button>
                <button @click="deleteClient" class="botao_descartar delete">
                    <span>Eliminar</span>
                    <i class="fa fa-trash"></i>
                </button>
                <button @click="archive" class="botao_descartar archive">
                    <span>{{ClientData.state == 1 ? 'A':'Desa'}}rquivar</span>
                    <i :class="ClientData.state == 1 ? 'fa fa-archive' : 'fa fa-refresh'"></i>
                </button>
            </div>
        </div>
        <Toast/>
    </div>
    <Confirmation v-if="confirm" :SmsConfirm="SmsConfirm" @descartou="confirm=!confirm" @Confirme="Confirme"/>
    <div class="Container">
        <div class="Form">
            <div class="form-container">
                <div class="Header">
                    <div class="card-header">
                        <font-awesome-icon icon="fa-solid fa-cart-shopping" />
                        <span>
                            <strong>{{orders}}</strong>
                            Pedidos
                        </span>
                    </div>
                </div>

                <div class="Main">
                    <div class="Name-Img-control">
                        <div class="form-nome">
                            <input :disabled="ClientData.state == 0" type="text" v-model="ClientData.surname" placeholder="Apelido"/>
                        </div>

                        <div class="form-image">
                            <span class="archived" v-if="ClientData.state == 0">
                                ARQUIVADO
                            </span>
                            <div >
                                <div>
                                    <img :src="element.img" alt="">
                                    <span v-if="ClientData.state == 1">
                                        <label for="image">
                                            <FontAwesomeIcon icon="fa-solid fa-pen-to-square"/>
                                            <input type="file" id="image" @change="onFileChange">
                                        </label>
                                        <FontAwesomeIcon @click="DeletarImagem" icon="fa-solid fa-trash"/>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-basic">

                        <div class="form-content">
                            <div class="form-Control">
                                <label for="name">Nome: </label>
                                <input id="name" :disabled="ClientData.state === 0" type="text" v-model="ClientData.name" placeholder="Nome"/>
                            </div>

                            <div class="form-Control">
                                <label for="phone">Telefone: </label>
                                <input id="phone" :disabled="ClientData.state == 0" type="text" v-model="ClientData.phone" placeholder="Telefone"/>
                            </div>

                            <div class="form-Control">
                                <label for="email">E-mail: </label>
                                <input id="email" :disabled="ClientData.state == 0" type="text" v-model="ClientData.email" placeholder="Email"/>
                            </div>

                        </div>
                         <div class="form-content">
                            <div class="form-Control">
                                <label for="country">Nacionalidade: </label>
                                <input id="country" :disabled="ClientData.state == 0" type="text" v-model="ClientData.country" placeholder="Pais"/>
                            </div>

                            <div class="form-Control">
                                <label for="city">cidade: </label>
                                <input id="city" :disabled="ClientData.state == 0" type="text" v-model="ClientData.city" placeholder="Cidade"/>
                            </div>

                            <div class="form-Control">
                                <label for="sede">Rua: </label>
                                <input class="sede" :disabled="ClientData.state == 0" type="text" v-model="ClientData.rua" placeholder="Rua"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import Confirmation from '@/components/confirmation/index.vue'
import { onMounted, ref, reactive, watch , computed} from "vue";
import { useStore } from "vuex";
import Confirm from '@/components/confirmation/confirm.vue'
import Toast from 'primevue/toast'
import { useToast } from "primevue/usetoast";
import axios from 'axios';

import {useUploadImage} from '@/composable/public/UploadImage'

const emits = defineEmits(['close'])
const toaste = useToast()
const SmsConfirm = ref()
const confirm = ref(false)
const props = defineProps({
    dataClient: Object
});
const orders = ref(0)
const ClientData = ref([]);
const element = reactive({
  image: "clientes/image/produto-sem-imagem.png",
});

const archive = (()=>{
    ClientData.value.state = ClientData.value.state == 0 ? 1 : 0;
})

const deleteClient = (()=>{
    confirm.value = true
    SmsConfirm.value = 'apagar'
})

const store = useStore()


const {createImg,onFileChange,object} = useUploadImage(ClientData.value,element)
onMounted(()=>{
    if (props.dataClient != null) {
        axios.get(`/clientOrders/${props.dataClient.id}`)
        .then((response) => {
            orders.value = response.data
        }).catch((err) => {
            console.log(err);
        });
        ClientData.value = props.dataClient
        element.img = '/Clientes/image/'+props.dataClient.image
    }
})

const saveClient = (()=>{
    ClientData.value.images = !object.imagem ? null :  object.imagem
    axios.post(`/updateClient/${ClientData.value.id}`,{...ClientData.value})
    .then((Response) => {
        message(Response.data.message,Response.data.type)
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        emits('close')
    });
})

const message = ((message,type)=>{
    toaste.add({
        severity: type,
        summary: 'menssagem',
        detail: message,
        life: 5000
    })

})

const deleteConfirm = ((event , outro)=>{
    axios.post(`/deleteClient/${ClientData.value.id}`)
    .then((Response) => {
        confirm.value = false
        message(Response.data.message,Response.data.type)
        if (Response.data.type == 'success') {
            setTimeout(()=>{
                emits('close')
            },5000)
        }
    }).catch((err) => {
        console.log(err);
    });
})

const DeletarImagem = (()=>{
    element.img = '/Clientes/image/produto-sem-imagem.png'
    ClientData.value.image = 'produto-sem-imagem.png'
})
</script>

<style scoped lang="scss">
@include components;
.Form{
    background-color: #f9f9f9f9;
    width: 100%;
    height: 100%;
    justify-content: center;
    align-items: center;

    @include formulary;
}
</style>
