<template>
  <div class="principal">
    <div class="Header">
        <div class="Header-left">
            <h2>Nova caixa</h2>
            <span>
                <button @click="savePoint">Guardar</button>
                <button class="botao_descartar" @click="$emit('close')">Fechar</button>
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
                            <label for="userRelation">Usuario relacionado:</label>
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
                    <button>Senha</button>
                </div>
            </div>
            <div class="Footer">
                <div class="form-content">
                    <div class="form-Control">
                        <label for="password">Senha:</label>
                        <input type="password" v-model="pointSale.password" id="password" placeholder="Digita a sua senha">
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { computed, onMounted, ref } from "vue";
import { useStore } from "vuex";

const store = useStore()
const stateDrop = ref(null)
const pointSale = ref([])
const emits = defineEmits(['message','close'])

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

async function savePoint() {
    axios.post(`caixa/savePoint${pointSale.value.id ? '/' +pointSale.value.id : ''}`,{...pointSale.value})
    .then((response) => {
        emits('message',response.data.type,response.data.message)
        emits('close')
    }).catch((err) => {
        console.log(err);
        emits('message','warning','Aconteceu um erro ao salvar os dados')
    });
}

</script>

<style lang="scss" scoped>
@include components;
.Container{
    @include formulary;
}
</style>
