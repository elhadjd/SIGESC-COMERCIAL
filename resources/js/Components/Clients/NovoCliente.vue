<template>
<div class="principal">
    <Toast/>
    <Confirmation v-if="confirm" :SmsConfirm="SmsConfirm" @descartou="confirm=!confirm" @Confirme="Confirme"/>
    <div class="Container">
        <div class="Form">
            <div class="form-container">
                <div class="Header">
                    <div class="card-header Action dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        Ação
                    </div>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li v-if="DadosCliente.state == 0" @click="Desarquivar"><a class="dropdown-item" href="#">Desarquivar</a></li>
                        <li v-else @click="Arquivar"><a class="dropdown-item" href="#">Arquivar</a></li>
                        <li @click="Apagar"><a class="dropdown-item" href="#">Apagar</a></li>
                    </ul>
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
                            <input :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.surname" placeholder="Apelido"/>
                        </div>

                        <div class="form-image">
                            <div class="Arquivado" v-if="DadosCliente.state == 0">
                                <h2>ARQUIVADO</h2>
                            </div>
                            <div >
                                <div>
                                    <img :src="element.img" alt="">
                                    <span>
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
                                <input id="name" :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.name" placeholder="Nome"/>
                            </div>

                            <div class="form-Control">
                                <label for="phone">Telefone: </label>
                                <input id="phone" :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.phone" placeholder="Telefone"/>
                            </div>

                            <div class="form-Control">
                                <label for="email">E-mail: </label>
                                <input id="email" :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.email" placeholder="Email"/>
                            </div>

                        </div>
                         <div class="form-content">
                            <div class="form-Control">
                                <label for="country">Nacionalidade: </label>
                                <input id="country" :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.country" placeholder="Pais"/>
                            </div>

                            <div class="form-Control">
                                <label for="city">cidade: </label>
                                <input id="city" :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.city" placeholder="Cidade"/>
                            </div>

                            <div class="form-Control">
                                <label for="sede">Rua: </label>
                                <input class="sede" :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.rua" placeholder="sede"/>
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
import { onMounted, ref, reactive, watch ,defineProps, computed} from "vue";
import { useStore } from "vuex";
import Confirm from '@/components/confirmation/confirm.vue'
import Toast from 'primevue/toast'
import { useToast } from "primevue/usetoast";
import axios from 'axios';

import {useUploadImage} from '@/composable/public/UploadImage'

const emits = defineEmits(['voltar'])
const toaste = useToast()
const SmsConfirm = ref()
const confirm = ref(false)
const props = defineProps({
    Guardar: Boolean,
    Dados: Object
});
const orders = ref(0)

const element = reactive({
  image: "clientes/image/produto-sem-imagem.png",
});

const Arquivar = (()=>{
    DadosCliente.value.state = 0;
})

const Apagar = (()=>{
    confirm.value = true
    SmsConfirm.value = 'apagar'
})

const store = useStore()

const DadosCliente = ref([]);
const {createImg,onFileChange,object} = useUploadImage(DadosCliente.value,element)
onMounted(()=>{
    if (props.Dados != null) {
        axios.get(`/clientOrders/${props.Dados.id}`)
        .then((response) => {
            orders.value = response.data
        }).catch((err) => {
            console.log(err);
        });
        DadosCliente.value = props.Dados
        element.img = '/Clientes/image/'+props.Dados.image
    }
})

const Desarquivar = (()=>{
    DadosCliente.value.state = 1;
})

const GuardarCliente = (()=>{
    DadosCliente.value.images = !object.imagem ? null :  object.imagem
    axios.post(`/updateClient/${DadosCliente.value.id}`,{...DadosCliente.value})
    .then((Response) => {
        store.state.GuardarCliente = false
        message(Response.data.message,Response.data.type)
    }).catch((err) => {
        console.log(err);
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

const Confirme = ((event , outro)=>{
    axios.post(`/deleteClient/${DadosCliente.value.id}`)
    .then((Response) => {
        confirm.value = false
        message(Response.data.message,Response.data.type)
        if (Response.data.tipo == 'success') {
            setTimeout(()=>{
            emits('voltar')
        },5000)
        }
    }).catch((err) => {
        console.log(err);
    });
})

const DeletarImagem = (()=>{
    element.img = 'clientes/image/registro-sem-imagen.png'
    DadosCliente.value.image = 'registro-sem-imagen.png'
})

const guardar = computed(()=>{
    return store.state.GuardarCliente
})

watch(guardar,(novo)=>{
    GuardarCliente()
})

</script>

<style scoped lang="scss">
.principal{
    .Container{
        width: 100%;
        height: 90%;
        .Form{
            background-color: #f9f9f9f9;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;

            @include formulary;
        }
    }
}
</style>
