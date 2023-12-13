<template>
  <div class="principal">
    <Toast/>
    <Confirmation v-if="confirm" :SmsConfirm="SmsConfirm" @descartou="confirm = false" @Confirme="Confirme"/>
    <div class="Container" v-if="PretedFornecedor != null">
        <ProdutosFornecedor class="Form" @Fechar="OnMounted" :IdFornecedor="singleSupplier.id" v-if="PretedFornecedor == 'produtos'"/>
    </div>
    <div v-else class="Container">
        <div class="Form">
            <div class="form-container">
                <div class="Header">
                    <button class="card-header Action dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        {{$t('words.action')}}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li v-if="singleSupplier.state == 0" @click="Desarquivar"><a class="dropdown-item" href="#">Desarquivar</a></li>
                        <li v-else @click="Arquivar"><a class="dropdown-item" href="#">{{$t('words.archive')}}</a></li>
                        <li @click="Apagar"><a class="dropdown-item" href="#">{{$t('words.delete')}}</a></li>
                    </ul>
                    <button class="card-header">
                        <font-awesome-icon icon="fa-solid fa-cart-shopping" />
                        <span>
                            <strong>{{PretendFornecedor.orders.length}}</strong>
                            {{$t('words.order')}}s
                        </span>
                    </button>
                    <button class="card-header" @click="produtos('produtos')">
                        <font-awesome-icon icon="fa-brands fa-product-hunt" />
                        <span>
                            <strong>{{PretendFornecedor.products.length}}</strong>
                            {{$t('words.article')}}s
                        </span>
                    </button>

                </div>
                <div class="Main">
                    <div class="Name-Img-control">
                        <div class="form-nome">
                            <input :disabled="singleSupplier.state == 0" type="text" v-model="singleSupplier.name" :placeholder="$t('words.name')"/>
                        </div>

                        <div class="form-image">
                            <div class="Arquivado" v-if="singleSupplier.state == 0">
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
                                    <FontAwesomeIcon @click="RemoveImage" icon="fa-solid fa-trash"/>
                                </span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="info-basic">
                        <div class="form-content">
                            <div class="form-Control">
                                <label for="nif">{{$t('phrases.nifCompany')}}: </label>
                                <input id="nif" :disabled="singleSupplier.state == 0" type="text" v-model="singleSupplier.nif" placeholder="NIF"/>
                            </div>

                            <div class="form-Control">
                                <label for="phone">{{$t('words.phone')}}: </label>
                                <input id="phone" :disabled="singleSupplier.state == 0" type="text" v-model="singleSupplier.phone" :placeholder="$t('words.phone')"/>
                            </div>

                            <div class="form-Control">
                                <label for="email">E-mail: </label>
                                <input id="email" :disabled="singleSupplier.state == 0" type="text" v-model="singleSupplier.email" placeholder="Email"/>
                            </div>

                        </div>

                        <div class="form-content">
                            <div class="form-Control">
                                <label for="country">{{$t('words.country')}}: </label>
                                <input id="country" :disabled="singleSupplier.state == 0" type="text" v-model="singleSupplier.country" :placeholder="$t('words.country')"/>
                            </div>

                            <div class="form-Control">
                                <label for="city">{{$t('words.city')}}: </label>
                                <input id="city" :disabled="singleSupplier.state == 0" type="text" v-model="singleSupplier.city" :placeholder="$t('words.city')"/>
                            </div>

                            <div class="form-Control">
                                <label for="sede">{{$t('words.neighborhood')}}: </label>
                                <input class="sede" :disabled="singleSupplier.state == 0" type="text" v-model="singleSupplier.sede" :placeholder="$t('words.neighborhood')"/>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="Footer">
                    em processo...
                </div> -->
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import Confirmation from '@/components/confirmation/index.vue'
import { onMounted, ref, reactive, watch , computed} from "vue";
import { useStore } from "vuex";
import Confirm from '@/components/confirmation/confirm.vue'
import ProdutosFornecedor from './ProdutosFornecedor.vue'
import Toast from 'primevue/toast'
import { useToast } from "primevue/usetoast";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import axios from 'axios';

import {useUploadImage} from '@/composable/public/UploadImage'
import { getImages } from '@/composable/public/getImages';

const PretedFornecedor = ref(null)

const emits = defineEmits(['voltar'])

const toast = useToast()

const EnviarDados = ref([])

const SmsConfirm = ref()

const confirm = ref(false)

const props = defineProps({
    Guardar: Boolean,
    supplier: Object
});
const store = useStore()
const singleSupplier = ref([])
const produtos = ((event)=>{
    PretedFornecedor.value = event
})

const PretendFornecedor = ref({
    products: [],
    orders: [],
})
const element = reactive({
    img: "/produtos/image/" + props.supplier.image,
});

const {getImage,RemoveImage} = getImages(element);

const Arquivar = (()=>{
    singleSupplier.value.state = 0;
})

const Apagar = (()=>{
    confirm.value = true
    SmsConfirm.value = 'apagar'
})



const OnMounted = onMounted(async ()=>{
    await getImage()
    await axios.get(`/suppliers/relations/${props.supplier.id}`)
    .then((Response) => {
        singleSupplier.value = Response.data
        PretendFornecedor.value = Response.data
        PretedFornecedor.value = null
    }).catch((err) => {
        console.log(err);
    });
})
const {createImg,onFileChange,object} = useUploadImage(singleSupplier.value,element)
const Desarquivar = (()=>{
    singleSupplier.value.state = 1;
})

const GuardarFornecedor = (()=>{
    axios.post('/InserirCliente',{...singleSupplier.value})
    .then((Response) => {
        store.state.GuardarCliente = false
        toast.add({
            severity: Response.data.tipo,
            summary: 'menssagem',
            detail: Response.data.message,
            life: 5000
        })
    }).catch((err) => {
        console.log(err);
    });
})

const saveSupplier = (()=>{
    singleSupplier.value.images = !object.imagem ? null :  object.imagem
    axios.post(`/supplier/update/${singleSupplier.value.id}`,{...singleSupplier.value})
    .then((Response) => {
        store.state.GuardarCliente = false
        message(Response.data.message,Response.data.type)
    }).catch((err) => {
        console.log(err);
    });
})

const Confirme = ((event , outro)=>{
    axios.post(`/deleteSupplier/${singleSupplier.value.id}`)
    .then((Response) => {
        confirm.value = false
        message(Response.data.message,Response.data.type)
        if (Response.data.type =='success') {
            setTimeout(()=>{
                emits('voltar')
            },5000)
        }
    }).catch((err) => {
        console.log(err);
    });
})

const message = ((message,type)=>{
    toast.add({
        severity: type,
        summary: 'menssagem',
        detail: message,
        life: 5000
    })
})

const guardar = computed(()=>{
    return store.state.GuardarCliente
})

watch(guardar,(novo)=>{
    saveSupplier()
})

</script>

<style scoped lang="scss">
.principal{
    height:auto;
    .Container{
        height: auto;
        justify-content: center;
        align-items: center;
        .Form{
            background-color: #f9f9f9f9;
            width: 100%;
            height: 100%;
            .form-container{
                height: auto;
            }
            @include formulary;
        }
    }
}
</style>
