<template>
<Addpassword v-if="modal == 'password'" @fecharModal="modal = ''" @message="message" :id="Caixa.id"/>
  <div class="principal">
    <div class="Header">
        <div class="Header-left">
            <span>
                <h2>Nova caixa </h2>
            </span>
        </div>
    </div>
    <div class="Container">
        <form>
            <div class="Botoes">
                <button @click.prevent.stop="saveCash" class="guardar" type="submit">Guardar</button>
                <button class="descartar" @click="$emit('close')" type="button">Fechar</button>
            </div>
            <div class="FormCaixa">
                <div>
                    <div class="NomeCaixa">
                        <label for="CashName">Ponto de Venda</label>
                        <div>
                            <input type="text" v-model="Caixa.nome" name="CashName" id="CashName" placeholder="Nome de ponto de venda">
                            <button @click.prevent.stop="modal = 'password'" class="AddPassword">Adicionar senha</button>
                        </div>
                    </div>
                    <div class="FormConteiner">
                        <div class="form-control">
                            <label for="Users">Usuari relacionado</label>
                            <input type="text" @click="ShowDrop('Users')" id="Users" v-model="Caixa.userRelation.apelido" >
                            <div v-if="drop == 'Users'" class="drop">
                                <span @click="selectUser(user)" v-for="user in users" :key="user.id">{{user.apelido}}</span>
                            </div>
                        </div>
                        <div class="form-control">
                            <label for="infoCompany">Informação da empresa</label>
                            <input type="text" id="infoCompany" v-model="cashConfig.infoCompanyValue" placeholder="Como deseja imprimir a informaçoes da empressa" @click="ShowDrop('infoCompany')">
                            <div class="drop" v-if="drop == 'infoCompany'">
                                <span  @click="handelInputs('infoCompany','Imprimir informaçoes completa da empresa',true)">Imprimir informaçoes completa da empresa</span>
                                <span  @click="handelInputs('infoCompany','Imprimir Apenas o nome da empresa',false)">Imprimir Apenas o nome da empresa</span>
                            </div>
                        </div>
                        <div class="form-control">
                            <label for="currency">Moeda</label>
                            <div>
                                <input type="text" v-model="cashConfig.currency" id="currency" placeholder="Selectiona a moeda que esta caixa deseja usar" @click="ShowDrop('currency')">

                                <FontAwesomeIcon v-if="cashConfig.currency == ''" @click="newCurrency" icon="fa-solid fa-plus"/>
                                <FontAwesomeIcon @click="modal='currency'" v-else icon="fa-solid fa-edit"/>
                            </div>
                            <div class="drop" v-if="drop == 'currency'">
                                <span @click="handelInputs('currency',currency)" v-for="currency in currencys" :key="currency.id">{{currency.nome}}</span>
                            </div>
                        </div>
                        <div class="form-control">
                            <label for="priceList">Lista de preço</label>
                            <input type="text" v-model="cashConfig.listPriceValue" @click="ShowDrop('priceList')" id="priceList" placeholder="Permição sebre os preços">
                            <div class="drop"  v-if="drop == 'priceList'">
                                <span @click="handelInputs('listPrice','Permetir edição do preço',true)">Permetir edição do preço</span>
                                <span @click="handelInputs('listPrice','Não permetir edição do preço',false)">Não permetir edição do preço</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <NewCurrency v-if="modal == 'currency'" @close="modal = '',getCurrency()" @message="message" :currency="currency"/>
    </div>
  </div>
</template>

<script  setup>
import NewCurrency from './newCurrency.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { Inertia } from "@inertiajs/inertia"
import Addpassword from './AddPassword.vue'
import { useForm } from "@inertiajs/inertia";
import { onMounted, reactive ,ref , defineEmits,defineProps} from "@vue/runtime-core";
import axios from "axios";

const props = defineProps({
    DadosCaixa: Object
})
const cashConfig = ref({
    listPrice: "",
    listPriceValue: "",
    currency: "",
    infoCompany: "",
    infoCompanyValue: "",
})
const currency = ref({
    nome:null,
    pais: "",
    id: null
})
const currencys = ref([])
const users = ref()
const modal = ref("")
const drop = ref("")
const emits = defineEmits(['Guardar','message']);
const Caixa = useForm({
    nome: null,
    pagamento: null,
    id: null,
    userRelation: []
})


const selectUser = ((user)=>{
    Caixa.userRelation = user
    drop.value = ""
})

const newCurrency = (()=>{
    currency.value.nome = ""
    currency.value.pais = ""
    currency.value.id = ""
    modal.value = 'currency'
})

const message = ((tipo,message)=>{
    emits('message',tipo,message)
})

const handelInputs = ((type,value,boolean)=>{
    if (type == 'currency') {
        cashConfig.value[type] = value.nome
        currency.value = value
        return drop.value = ""
    }
    cashConfig.value[type] = boolean
    cashConfig.value[type+'Value'] = value
    return drop.value = ""
})

const OnMounted = onMounted(async ()=>{
    await axios.get('/users')
    .then((Response) => {
        users.value = Response.data
        if (props.DadosCaixa.length !=0) {
            Caixa.nome = props.DadosCaixa.nome
            Caixa.id = props.DadosCaixa.id
            filterUserRelation(props.DadosCaixa.id_user_relation)
        }
        getCashConfig()
        getCurrency()
    }).catch((err) => {
        console.log(err);
    });
})

const filterUserRelation = ((idUser)=>{
    const filter = users.value.filter((user)=>{
        return user.id == idUser
    })
    if (filter.length>0) {
        Caixa.userRelation = filter[0]
    }
})

const getCurrency = (()=>{
    axios.get('/getCurrency')
    .then((response) => {
        currencys.value = response.data
        const filterCurrency = currencys.value.filter((currency)=>{
            return currency.nome == cashConfig.value.currency
        })
        if (filterCurrency.length>0) {
            currency.value = filterCurrency[0]
        }
    }).catch((err) => {

    });
})

const getCashConfig = (()=>{
    axios.get(`/getCashConfig/${props.DadosCaixa.id}`)
    .then((response) => {
        cashConfig.value = response.data;
    }).catch((err) => {
        console.log(err);
    });
})

const ShowDrop = ((event)=>{
    if (event == drop.value) return drop.value = ""
    drop.value = event
})

const saveCash = (()=>{
    //A verificar se usuario digitou algun nome
    if (Caixa.nome != null) {
        console.log('Ol');
        if (Caixa.userRelation.apelido != null) {
            axios.post('/CriarCaixa',{Cash:Caixa,config: cashConfig.value})
            .then((Response) => {
                message('success','Informaçoes guardados com sucesso')
            }).catch((err) => {
                console.log(err);
            });

        }else{
            message('info','Por favor selectiona o usuario relationado')
        }
    }else{
        message('info','O campo nome não pode ficar vazio')
    }
})
</script>

<style scoped lang="scss">
@import '../../../../assets/PontoVenda/css/CashConfig/NovaCaixa';
</style>
