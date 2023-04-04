<template>
<div class="criarClientes">
    <Toast/>
    <Confirmation v-if="confirm" :SmsConfirm="SmsConfirm" @Confirme="Confirme"/>
    <div class="formulario">
        <div class="formulario-header">
            <div class="card-header   Action dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
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
                    <strong>{{pretendClient.orders.length}}</strong>
                    Pedidos
                </span>
            </div>
            <div class="card-header">
                <font-awesome-icon icon="fa-solid fa-money-check-dollar" />
                <span>
                    <strong>{{pretendClient.payments.length}}</strong>
                    Pagamentos
                </span>
            </div>

        </div>

        <div class="formulario-content">
            <div class="imagem">
                <div class="nomeCliente">
                    <input :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.surname" placeholder="digita o nome do cliente"/>
                </div>

                <div class="img">
                       <div class="Arquivado" v-if="DadosCliente.state == 0">
                            <h2>ARQUIVADO</h2>
                        </div>
                        <div v-else class="icones">
                            <label for="imagem" id="edit_img">
                                <input type="file" @change="onFileChange" ref="fileInput" id="imagem" class="position-absolute d-none"/>
                                <i class="fa fa-pencil-square-o EditarImagen" aria-hidden="true" ></i>
                            </label>
                            <span @click="DeletarImagem" id="icone_delete_img">
                                <i class="fa fa-ban" aria-hidden="true"></i>
                            </span>
                        </div>
                    <div class="imagem-cliente">
                        <img :src="element.image"/>
                    </div>
                </div>

            </div>

            <div class="formulario-insert">
                <div class="Formulario-one">
                    <div class="dados">

                        <div class="dados-one">
                            <div>Nome</div>
                            <div>Telefone</div>
                            <div>Email</div>
                        </div>

                        <div class="dados-two">
                            <input :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.whatssap" placeholder="Whatssap"/>
                            <input :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.phone" placeholder="Telefone"/>
                            <input :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.email" placeholder="Email"/>
                        </div>
                    </div>

                    <div class="endereco">
                        <div class="EnderecoTitl">
                            <div>Nacionalidade</div>
                            <div>cidade</div>
                            <div>Rua</div>
                        </div>

                        <div class="EnderecoContent">
                            <input :disabled="DadosCliente.state == 0" type="text" @click="SelectPais" v-model="DadosCliente.pais" placeholder="Pais"/>
                            <input :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.cidade" placeholder="Cidade"/>
                            <input :disabled="DadosCliente.state == 0" type="text" v-model="DadosCliente.rua" placeholder="Rua"/>
                        </div>
                    </div>
                </div>

                <div>
                    em processo...
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

const emits = defineEmits(['voltar'])
const toaste = useToast()
const SmsConfirm = ref()
const confirm = ref(false)
const props = defineProps({
    Guardar: Boolean,
    Dados: Object
});
const pretendClient = ref({
    orders: [],
    payments: []
})

const element = reactive({
  image: "clientes/image/registro-sem-imagen.png",
});

const Arquivar = (()=>{
    DadosCliente.value.state = 0;
})

const Apagar = (()=>{
    confirm.value = true
    SmsConfirm.value = 'apagar'
})

const store = useStore()

const DadosCliente = ref({
  apelido: null,
  whatssap: null,
  image: null,
  telefone: null,
  email: null,
  pais: null,
  cidade: null,
  rua: null,
  id: null,
  state: 1,
});

onMounted(()=>{
    if (props.Dados != null) {
        axios.get(`/getPretendOrders/${props.Dados.id}`)
        .then((response) => {
            pretendClient.value = response.data
            console.log(response.data);
        }).catch((err) => {
            console.log(err);
        });
        DadosCliente.value = props.Dados
        element.imagen = '/Clientes/image/'+props.Dados.image
    }
})

const Desarquivar = (()=>{
    DadosCliente.value.state = 1;
})

const GuardarCliente = (()=>{
    axios.post('/InserirCliente',{dados:DadosCliente.value})
    .then((Response) => {
        store.state.GuardarCliente = false
        toaste.add({
            severity: Response.data.tipo,
            summary: 'menssagem',
            detail: Response.data.message,
            life: 5000
        })

    }).catch((err) => {
        console.log(err);
    });
})

const Confirme = ((event , outro)=>{
    if (outro == 'confirmou') {
        axios.post('/UpdatedCliente',{id: DadosCliente.value.id,tipo: event})
        .then((Response) => {
            confirm.value = false
            toaste.add({
                severity: Response.data.tipo,
                summary: 'menssagem',
                detail: Response.data.message,
                life: 5000
            })
            if (Response.data.tipo =='success') {
                setTimeout(()=>{
                emits('voltar')
            },5000)
            }
        }).catch((err) => {
            console.log(err);
        });
    } else {
        confirm.value = false
    }
})

const DeletarImagem = (()=>{
    element.imagen = 'clientes/image/registro-sem-imagen.png'
    DadosCliente.value.image = 'registro-sem-imagen.png'
})

const guardar = computed(()=>{
    return store.state.GuardarCliente
})

watch(guardar,(novo)=>{
    GuardarCliente()
})

//===========================================
// Preview da imagem do cliente
//============================================

const onFileChange = (e) => {
  var files = e.target.files || e.dataTransfer.files;
  // Verificar o formato da imagem
  let arquivo = files[0].name;
  let extension = arquivo.indexOf(".") < 1 ? "" : arquivo.split(".").pop();
  if (extension == "") {
    return false;
  } else {
    // verificar se este formato existe no Array
    let formatos_permitidos = [
      "jpg",
      "png",
      "gif",
      "jpeg",
      "JPG",
      "PNG",
      "GIF",
      "JPEG",
    ];
    let resultado = formatos_permitidos.includes(extension);
    if (resultado == false) {
      return false;
    } else {
      // Tamanho da foto em MB
      var tamanho_maximo = 2242880;
      var fsizet = 0;
      for (var i = 0; i <= e.target.files.length - 1; i++) {
        // tamanho do arquivo
        var fsize = e.target.files.item(i).size;
        // total calculado
        fsizet = fsizet + fsize;
      }
      if (fsizet > tamanho_maximo) {
        console.log("tamanho de imagem muito grande");
      } else {
        createImg(files[0]);
      }
    }
  }
};

const createImg = (file) => {
  var imagem = new Image();
  var reader = new FileReader();

  reader.onload = (e) => {
    element.image = e.target.result;
    DadosCliente.value.image = element.image
  };
  reader.readAsDataURL(file);
};
</script>

<style scoped lang="scss">
@import "../../../assets/Clientes/css/NovoCliente";
</style>
