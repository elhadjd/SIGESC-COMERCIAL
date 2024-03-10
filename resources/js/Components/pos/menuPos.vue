<template>
<Index
    v-if="!$store.state.pos.Controlo.state"
    :session="props.session"
    @message="messages"
    @login="showLogin"
/>
<Toast />
<Transition>
    <EntradaSaida
    v-if="entradaSaida"
    @message="messages"
    @fechar="entradaSaida = false"
    />
</Transition>
<div v-if="fatura" class="fatura">
    <Fatura :dadosFatura="dadosFatura" @closePrint="closePrint"/>
</div>
  <div v-else class="ManuPosGeral">
    <ProgressVue v-if="progress"/>
    <section id="headerPos" class="z-10">
        <button type="button" class="header-One z-20">
            <li>
                <strong> SIGESC </strong>
                <i class="fa fa-chevron-down openMenu" aria-hidden="true"></i>
            </li>
            <div class="menu">
                <div class="EntradaSaida" @click="entradaSaida = true">
                    <cash />
                    <span>{{`${$t('words.entry')}&${$t('words.output')}&${$t('words.expenses')}`}}</span>
                </div>
                <div @click="listOrdersState = true,showProducts = !showProducts" id="pos">
                    <i class="fa fa-ticket" aria-hidden="true"></i>
                    <strong>{{ orderStore.order.number }}</strong>
                    <span>{{$t('words.order')}}</span>
                </div>
            </div>
        </button>
      <div class="header-two">
        <Pesquisar />
        <button class="user">
            <img :src="image.img"/>
            <div class="closePos">
                <span class="truncate w-40">{{ userStor.surname }}</span>
                <div @click="close">{{$t('words.goOut')}} <i class="fa fa-sign-out"></i></div>
            </div>
        </button>
      </div>
    </section>

    <section class="MenuPos user-select-none">
      <div class="cartShop">
        <shopping :size="200" />
      </div>
      <div class="Posesquerda">
          <div class="esquerdaCima">
            <div class="Carrinho">

                <li
                  :class="`${orderShow.lastItem == item.id && 'last-element'} listaPedido`"
                  v-for="(item, index) in orderShow.items"
                  :key="index"
                  @click="ClickOnRowItem(item.id, 'linha')"
                >
                  <div class="w-50">{{ item.nome }}</div>
                  <div class="mr-3 w-auto">
                    {{ item.quantidade + "Un(s)" }}
                  </div>
                  <div>{{ formatMoney(item.preco_pedido) }}</div>
                  <div class="totalEncomeda">
                    {{ formatMoney(item.total) }}
                  </div>
                </li>
                <div v-if="orderShow.items.length > 0">
                    <strong class="mr-2">Total:</strong>
                    <span>{{ formatMoney(orderShow.total) }}</span>
                </div>
            </div>
          </div>

          <div class="esquerdaBaixo">
            <Eventos
                :idSession="session.id"
                :user="props.user"
                @invoice="printRecipt"
                @showProds="showProducts = !showProducts"
            />
          </div>
      </div>
      <div class="Posdireita" :class="showProducts ? 'showProducts' : ''">
        <ListePedido
          @close="listOrdersState = false"
          @AlterarPedido="AlterOrder($event),listOrdersState = false"
          @NovoPedido="setNewOrder"
          :session="session.id"
          v-if="listOrdersState == true"
        />
          <div v-if="!fatura && !listOrdersState" class="direitaBaixo">
            <Produtos @AddProds="($event)=>AddProd($event, session.id)" :categories="categories" @message="messages" />
          </div>
      </div>
      <Pagamento
        v-if="orderStore.order.formPayment"
        @closePaymentForm="orderStore.order.formPayment = false"
        @sendInvoice="printRecipt"
      />

    </section>
  </div>
</template>
<script setup>
import ProgressVue from '@/Components/confirmation/progress.vue'
import Index from "./LogIn/index.vue";
import cash from "vue-material-design-icons/CashMultiple.vue";
import shopping from "vue-material-design-icons/Shopping.vue";
import { mapMutations, mapState, useStore } from "vuex";
import Fatura from "./fatura.vue";
import Pagamento from "./pagamento.vue";
import Produtos from "./produtos.vue";
import ListePedido from "./ListePedidos.vue";
import Toast from "primevue/toast";
import { Link,router } from "@inertiajs/vue3";
import EntradaSaida from "./SaidaEntrada.vue";
import Eventos from "./eventos.vue";
import "./inatividade";
import { onMounted, reactive,computed, ref, watch } from "@vue/runtime-core";
import { useToast } from "primevue/usetoast";
import Pesquisar from "./pesquisar.vue";
import { getImages } from "@/composable/public/getImages";
import {AlterItemsInTheOrder} from '@/Components/pos/services/alterItemsInTheOrder'
import { serviceMessage } from "@/composable/public/messages"
import {OrdersServices} from "@/Components/pos/services/ordersServices"
import {storeOrderServices} from "@/Components/pos/services/storeOrderServices"
import {AddProductAtCart} from '@/Components/pos/services/addProductService'
const props = defineProps({
  session: Object,
  user:Object
});
const {showMessage} = serviceMessage()
const {
    checkOrders,
    progress,
    positionAlter,
    userStor,
    orders,
    showProducts,
    listOrdersState,
    setNewOrder,
    sumOrder,
    ClickOnRowItem
} = OrdersServices()
const {AddProd} = AddProductAtCart()
const {
    AlterOrder,
    getOrderAtStore,
    setStoreOrder
} = storeOrderServices()

const {
        alterItems
    } = AlterItemsInTheOrder()
const store = useStore();
const orderShow = computed(()=>store.getters['pos/public'].order)
let orderStore = store.state.pos
const image = reactive({
    img:'/login/image/'+userStor.image
})
const {getImage} = getImages(image);
const toast = useToast();
const entradaSaida = ref(false);
const cliente = ref("");
const dadosFatura = ref();
const fatura = ref(false);
const linha = ref(null);

const close = ()=>{
    router.get('/PDV/Home')
}
const categories = ref([])

const showLogin = (event) => {
  store.commit("pos/CloseCash", event);
};

const printRecipt = (event) => {
    dadosFatura.value = event;
        fatura.value = true;
    if(!fatura.value){
        
    }
    
};

const closePrint = (()=>{
    orderStore.order.formPayment = false;
    orderStore.order.number = '0';
    orderStore.order.items = [];
    orderStore.order.state = 'Cotação'
    orderStore.order.total = 0;
    fatura.value = false;
    return OnMounted();
})

const InputFocus = () => {
  store.state.pos.PesquisarProduto = "";
};

const OnMounted = onMounted(async () => {
    await getImage();
    await axios.get(`/PDV/menuPos/${localStorage.getItem('locale') || 'en'}`)
    .then((Response) => {
        store.commit('pos/setPaymentMethods',Response.data.methods)
        categories.value = Response.data.categories
        orderStore.order.user = userStor.value;
        orderStore.order.session = props.session.id;
        getOrderAtStore(orderStore.order);
    }).finally(()=>{

    });
    localStorage.setItem("session", props.session.id);
    orders.value = JSON.parse(localStorage.getItem('orders'+props.session.id))
    InputFocus();
    if(orders.value.length == 1){
        await checkOrders(orders.value,props.session.id)
    }
});

const data = () => {
  var objData = new Date(),
    ano = objData.getFullYear(),
    mes = objData.getMonth() + 1,
    numDias = new Date(ano, mes, 0).getDate();
  var dia = String(objData.getDate()).padStart("0");
  return dia + "-" + mes + "-" + ano;
};

const messages = (type, message) => {
  showMessage(message,type)
};
</script>
<style lang="sass" scoped>
@import '../../../assets/Pos/css/MenuPos'
</style>
