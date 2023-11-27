<template>
  <OrderSingleUser
    @closeModal="modalSingle.state = false"
    v-if="modalSingle.state"
    :idCaixa="modalSingle.id"
  />
  <div class="principal">
    <div class="Header">
      <div class="Header-left">
        <div><h2>Ponto de venda</h2></div>
        <div v-if="user.nivel == 'admin'">
          <button @click="$emit('CriarCaixa')">Criar caixa</button>
        </div>
      </div>
      <div class="Header-right"></div>
    </div>
    <div class="Container">
        <div>
            <div v-for="item in Points" :key="item.id" class="cardCash">
                <div class="cashHeader">
                    <div>
                        <div>{{ item.name }}</div>
                    </div>
                    <div class="bars">
                        <div>
                            <i @click="OptionCaixas(item.id)" class="fa fa-bars"></i>
                        </div>
                        <div v-if="OptionCaixa == item.id" class="options">
                            <div @click="user.nivel == 'admin'
                                    ? showOrders(item.session[0].id) : $emit('message', 'info', 'Usuario sem acesso')">
                                Ordens
                            </div>
                            <div @click="$emit('sessao', item.id)">Sessões</div>
                            <div
                                @click="
                                user.nivel == 'admin'
                                    ? $emit('definicaoCaixa', item)
                                    : $emit('message', 'info', 'Usuario sem acesso')">Definições</div>
                        </div>
                    </div>
                </div>
                <div class="cash-container">
                    <div v-if="item.state != 'Aberto'">
                        <button @click="openControl(item.id)">
                        Abrir
                        </button>
                    </div>

                    <div v-if="item.state == 'Aberto'">
                        <button @click="submit(item.id)" type="submit">Continuar</button>
                    </div>
                    <div v-if="item.state == 'Aberto'">
                        <button
                            @click=" user.nivel == 'admin' ? showCash(item.session[0].id)
                                : $emit('message', 'info', 'Usuario sem acesso')" class="showCash" type="button">
                            Ver
                            <FontAwesomeIcon icon="fa fa-eye" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { computed, onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import OrderSingleUser from "./orderSingleUser.vue";
import { useStore } from "vuex";
import { router, useForm } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
const store = useStore();

const props = defineProps({
  dados: Object,
});
const OptionCaixa = ref();
const Points = ref([]);
const emits = defineEmits([
    'definicaoCaixa',
    'Caixa',
    'sessao',
    'CriarCaixa',
    'AbrirCaixa',
    'message'
]);

const modalSingle = ref({
  state: false,
  id: "",
});

const user = computed(()=> store.state.publico.user);
const form = useForm({
  id_da_caixa: null,
});

onMounted(() => {
    getCaixas();
});

const submit = (id) => {
    router.get(`Pos/${id}`, {
        onSuccess: (Response) => {
            emits("message", "info", Response.props.message);
        },
    })
}

const OptionCaixas = (event) => {
    if (OptionCaixa.value == event) {
        OptionCaixa.value = null;
    } else {
        OptionCaixa.value = event;
    }
};

const showOrders = (id) => {
  modalSingle.value.id = id;
  modalSingle.value.state = true;
};

const openControl = ((caixa)=>{
    const savedState = localStorage.getItem('vuex')
    localStorage.clear()

    if (savedState) {
        localStorage.setItem('vuex',savedState)
        router.post(`caixa/opiningControl`,{id:caixa})
    }

})

const showCash = (Cash) => {
    emits("AbrirCaixa", Cash);
};
const getCaixas = async() => {
    store.state.pos.StateProgress = true;
   await axios.get('caixa/buscar')
    .then((Response) => {
        Points.value = Response.data
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        store.state.pos.StateProgress = false;
    });
}
</script>

<style scoped lang="scss">
@import "../../../assets/PontoVenda/css/MenuPonto.scss";
</style>
