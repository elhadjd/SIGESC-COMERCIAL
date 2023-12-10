<template>
  <div class="principal">
    <ConfirmVue @descartou="SmsConfirm.state = false" :SmsConfirm="SmsConfirm.sms" @Confirme="deleteCash" v-if="SmsConfirm.state"/>
    <div class="Header">
        <div class="Header-left">
            <h2>{{`${$t('words.save')} ${$t('words.box')}`}} caixa</h2>
            <span>
                <button @click="savePoint">{{$t('words.save')}}</button>
                <button class="botao_descartar" @click="$emit('close')">{{$t('words.close')}}</button>
                <buttonVue :class="'botao_descartar'" @click="SmsConfirm.state = true">
                    {{$t('words.delete')}}
                    <font-awesome-icon icon="fa-solid fa-xmark" style="color: #881111;" />
                </buttonVue>
            </span>
        </div>
    </div>
    <div class="Container">
        <div class="form-container">
            <div class="Main">
                <div class="Name-Img-control">
                    <div class="form-nome">
                        <input type="text" v-model="pointSale.name" placeholder="Shop" id="name">
                    </div>
                </div>
                <div class="info-basic">
                    <div class="form-content">
                        <div class="form-Control">
                            <label for="userRelation">{{`${$t('words.user')} ${$t('words.related')}`}}:</label>
                            <button @click="openDrop('users')" id="userRelation">{{!pointSale.user ? 'Seleciona usuario' : pointSale.user.name}}</button>
                            <div v-if="stateDrop == 'users'" class="drop">
                                <span v-for="user in company.users" :key="user.id" @click="selectUser(user)">{{user.name}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-content">
                        <div class="form-Control">

                        </div>
                    </div>
                </div>
                <div class="buttons">
                    <button>{{$t('login.password')}}</button>
                </div>
            </div>
            <div class="Footer">
                <div class="form-content">
                    <div class="form-Control">
                        <label for="password">{{$t('login.password')}}:</label>
                        <input type="password" v-model="pointSale.password" id="password" :placeholder="$t('login.password')">
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import ConfirmVue from '@/Components/confirmation/index.vue'
import axios from "axios";
import buttonVue from '@/ui/button.vue'
import { computed, onMounted, ref } from "vue";
import { useStore } from "vuex";
import { serviceMessage } from '@/composable/public/messages';

const store = useStore()
const stateDrop = ref(null)
const pointSale = ref([])
const emits = defineEmits(['message','close'])
const {showMessage} = serviceMessage()
const SmsConfirm = ref({
    sms: 'apagar esta caixa',
    state: false
})

const props = defineProps({
    point: Object
})

const company = computed(()=>store.getters['publico/public'].company)

onMounted(()=>{
    if (props.point) {
        pointSale.value = props.point
        pointSale.value.password = null
    }
})

const openDrop = ((e)=>{
    if (stateDrop.value == e) return stateDrop.value = null
    stateDrop.value = e
})

const selectUser = ((user)=>{
    pointSale.value.user = user
    return stateDrop.value = null
})

async function deleteCash() {
    await axios.delete(`caixa/deleteCash/${pointSale.value.id}`)
    .then((response) => {
        if(response.data.message) return showMessage(response.data.message,response.data.type)
        emits('close')
        console.log(response.data);
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        SmsConfirm.value.state = false
    });
}

async function savePoint() {
    store.state.pos.StateProgress = true;
    axios.post(`caixa/savePoint${pointSale.value.id ? '/' +pointSale.value.id : ''}`,{...pointSale.value})
    .then((response) => {
        emits('message',response.data.type,response.data.message)
        emits('close')
    }).catch((err) => {
        console.log(err);
        emits('message','warning','Aconteceu um erro ao salvar os dados')
    }).finally(()=>{
        store.state.pos.StateProgress = false;
    });
}

</script>

<style lang="scss" scoped>
@include components;
.Container{
    @include formulary;
}
</style>
