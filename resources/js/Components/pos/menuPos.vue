<template>
  <div class="ManuPosGeral">
    <Index
      v-if="!$store.state.pos.Controlo.state"
      :session="props.session"
      @message="messages"
      @login="teste"
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

    <section id="headerPos">
        <button type="button" class="header-One">
            <li>
                <strong> SISGESC </strong>
                <i class="fa fa-chevron-down openMenu" aria-hidden="true"></i>
            </li>
            <div class="menu">
                <div class="EntradaSaida" @click="entradaSaida = true">
                    <cash />
                    <span>Entrada & Saida & gastos</span>
                </div>
                <div @click="ListePedidos = true,showProducts = !showProducts" id="pos">
                    <i class="fa fa-ticket" aria-hidden="true"></i>
                    <strong>{{ Pedido.number }}</strong>
                    <span>Pedido</span>
                </div>
            </div>
        </button>
      <div class="header-two">
        <Pesquisar />
        <button class="user">
            <img :src="image.img"/>
            <div class="closePos">
                <span>{{ $store.state.publico.user.surname }}</span>
                <div @click="close">Sair <i class="fa fa-sign-out"></i></div>
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
                  :class="`${IdAlterar == Pedido.id && 'last-element'} listaPedido`"
                  v-for="(Pedido, index) in Pedido.items"
                  :key="index"
                  @click="ClicarLinha(Pedido.id, 'linha')"
                >
                  <div class="w-50">{{ Pedido.nome }}</div>
                  <div class="mr-3 w-auto">
                    {{ Pedido.quantidade + "Un(s)" }}
                  </div>
                  <div>{{ FormatarDineiro.format(Pedido.preco_pedido) }}</div>
                  <div class="totalEncomeda">
                    {{ FormatarDineiro.format(Pedido.total) }}
                  </div>
                </li>
                <div v-if="Pedido.items.length > 0">
                    <strong class="mr-2">Total:</strong>
                    <span>{{ FormatarDineiro.format(Pedido.total) }}</span>
                </div>
            </div>
          </div>

          <div class="esquerdaBaixo">
            <Eventos
                :idSession="session.id"
                :user="props.user"
                @message="messages"
                @invoice="print"
                @payment="makePayment"
                @cliente="client"
                @Alterar="updateOrder"
                @tipo="tipo"
                @showProds="showProducts = !showProducts"
                @Remover="Remover"
            />
          </div>
      </div>
      <div class="Posdireita" :class="showProducts ? 'showProducts' : ''">
        <ListePedido
          @close="ListePedidos = false"
          @AlterarPedido="AlterarPedido"
          @NovoPedido="NovoPedido"
          :session="session.id"
          v-if="ListePedidos == true"
        />
        <div v-else class="w-100 h-100">
          <div class="direitaCima">
            <i @click="showProducts = false" class="fa fa-close"></i>
          </div>
          <div v-if="!fatura" class="direitaBaixo">
            <Produtos @AddProds="AddProds" @message="messages" />
          </div>
        </div>
      </div>
      <Pagamento
        :method="method"
        v-if="Form_Pagamento == true"
        @message="messages"
        @closePaymentForm="Form_Pagamento = false"
        @fatura="print"
        :dados="Pedido"
      />
    </section>
  </div>
</template>
<script setup>
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
import { onMounted, reactive, ref } from "@vue/runtime-core";
import { useToast } from "primevue/usetoast";
import Pesquisar from "./pesquisar.vue";
import { getImages } from "@/composable/public/getImages";

const store = useStore();
const ListePedidos = ref(false);
const FormatarDineiro = Intl.NumberFormat("PT-AO", {
  style: "currency",
  currency: "AOA",
});
const image = reactive({
    img:'/login/image/'+store.state.publico.user.surname
})

const showProducts = ref(false)
const {getImage} = getImages(image);
const toast = useToast();
const pos = ref("Pos");
const entradaSaida = ref(false);
const cliente = ref("");
const IdAlterar = ref();
const dadosFatura = ref();
const fatura = ref(false);
const numeros = ref("");
const TipoAlteration = ref("quantidade");
const linha = ref(null);
const Form_Pagamento = ref(false)

const props = defineProps({
  session: Object,
  user:Object
});

const user = ref(props.user);
const Pedido = ref({
  items: [],
  total: 0,
  state: "Cotação",
  user: [],
  cliente: null,
  number: null,
  session: null,
});
const close = ()=>{
    router.get('/PDV/Home')
}
const Encomendas = ref([]);

const method = ref();

const IdEncomenda = ref();

const teste = (event) => {
  store.commit("pos/CloseCash", event);
};

const print = (event) => {
    dadosFatura.value = event;
    fatura.value = true;
    Form_Pagamento.value = false;
    Pedido.value.number = null;
    Pedido.value.items = [];
    Pedido.value.total = null;
    store.state.pos.ClientePos = null;
};

const closePrint = (()=>{
    fatura.value = false;
    return OnMounted();
})

const InputFocus = () => {
  store.state.pos.PesquisarProduto = "";
};

const AlterarPedido = (event,edit) => {
    localStorage.setItem("NumeroPedidos" + Pedido.value.session, event);
    ListePedidos.value = false;
    InputFocus();
    getStore(edit);
};

const NovoPedido = () => {
  //A verificar se o carrinho interior tem alguns items
  if (Pedido.value.items.length <= 0) {
    return messages(
      "info",
      "Atenção não e possivel crear duas pedidos vazios"
    );
  } else {
    Pedido.value.number = Encomendas.value.length;
    Pedido.value.items = [];
    store.state.pos.ClientePos = null;
    Pedido.value.total = 0;
    Pedido.value.cliente = null;
    localStorage.setItem(
      "NumeroPedidos" + Pedido.value.session,
      Pedido.value.number
    );
    Encomendas.value.push(Pedido.value);
    localStorage.setItem(
      "Encomendas" + Pedido.value.session,
      JSON.stringify(Encomendas.value)
    );
    ListePedidos.value = false;
  }
};

const client = (event) => {
  Pedido.value.cliente = event;
  Encomendas.value[Pedido.value.number] = Pedido.value;
  localStorage.setItem(
    "Encomendas" + Pedido.value.session,
    JSON.stringify(Encomendas.value)
  );
};

const tipo = (event) => {
  numeros.value = "";
  TipoAlteration.value = event;
};

const OnMounted = onMounted(async () => {
    await getImage();
    await axios.get("/PDV/menuPos").then((Response) => {
        method.value = Response.data.methods;
        Pedido.value.user = store.state.publico.user;
    });
    localStorage.setItem("session", props.session.id);
    Pedido.value.session = props.session.id;
    getStore();
    InputFocus();
});

const getStore = (edit) => {
  if (JSON.parse(localStorage.getItem("Encomendas" + Pedido.value.session))) {
    Encomendas.value = JSON.parse(
      localStorage.getItem("Encomendas" + Pedido.value.session)
    );
    Pedido.value.number = localStorage.getItem(
      "NumeroPedidos" + Pedido.value.session
    );
    //A verificar se esta encomenda esta paga
    return VerificarCarrinho(Encomendas.value[Pedido.value.number],edit);
  } else {
    Pedido.value.cliente = null;
    Pedido.value.number = 0;
    Encomendas.value.push(Pedido.value);
    Pedido.value.number = Encomendas.value.length - 1;
    localStorage.setItem(
      "NumeroPedidos" + Pedido.value.session,
      Pedido.value.number
    );
    localStorage.setItem(
      "Encomendas" + Pedido.value.session,
      JSON.stringify(Encomendas.value)
    );
  }
};

const data = () => {
  var objData = new Date(),
    ano = objData.getFullYear(),
    mes = objData.getMonth() + 1,
    numDias = new Date(ano, mes, 0).getDate();
  var dia = String(objData.getDate()).padStart("0");
  return dia + "-" + mes + "-" + ano;
};

const SetStore = () => {
  Pedido.value.total = CalcularTotal();
  Encomendas.value[Pedido.value.number] = Pedido.value;
  localStorage.setItem(
    "Encomendas" + Pedido.value.session,
    JSON.stringify(Encomendas.value)
  );
  getStore();
};

const VerificarCarrinho = (event,edit) => {
  if (event.state != "Pago"||edit) {

    if (event.items != []) {
      store.state.pos.ClientePos = event.cliente;
      if (edit) {
        Pedido.value.items = []
        event.items.forEach(item => {
            if (item.product) {
                item.product.preco_pedido = item.price_sold
                item.product.quantidade = item.quantity
                item.product.total = item.total
                Pedido.value.items.push(item.product)
            }else{
                item.preco_pedido = item.preco_pedido
                item.quantidade = item.quantidade
                item.total = item.total
                Pedido.value.items.push(item)
            }
        });
      }else{
        Pedido.value.items = event.items;
      }
      CalcularTotal();
    }
  } else {
    const testar = Encomendas.value.find((o) => o.state === "Cotação");
    if (testar) {
      localStorage.setItem(
        "NumeroPedidos" + Pedido.value.session,
        testar.number
      );
      Pedido.value.number = testar.number;
      Pedido.value.items = Encomendas.value[testar.number].items;
      store.state.pos.ClientePos = Encomendas.value[testar.number].cliente;
      CalcularTotal();
    } else {
      Pedido.value.number = Encomendas.value.length;
      localStorage.setItem(
        "NumeroPedidos" + Pedido.value.session,
        JSON.stringify(Pedido.value.number)
      );
      Pedido.value.number = localStorage.getItem(
        "NumeroPedidos" + Pedido.value.session
      );
      Pedido.value.cliente = store.state.pos.ClientePos;
      Encomendas.value.push(Pedido.value);
      localStorage.setItem(
        "Encomendas" + Pedido.value.session,
        JSON.stringify(Encomendas.value)
      );
      CalcularTotal();
    }
  }
};

const ClicarLinha = (event, prod) => {
  IdAlterar.value = event;
  numeros.value = "";
  linha.value = prod;
};

const messages = (tipo, message) => {
  toast.add({
    severity: tipo,
    summary: "Menssagem",
    detail: message,
    life: 5000,
  });
};

const AddProds = (produto) => {
  const existProduct = Pedido.value.items.find(
    (object) => object.id === produto.id
  );
  if (existProduct) {
    let listPrice = produto.list_price.filter((item)=>{
        return item.quantity <= existProduct.quantidade + 1
    })
    var preco = 0;
    if (listPrice.length>0) {
        listPrice = listPrice[listPrice.length -1];
        if (existProduct.quantidade + 1 >= listPrice.quantity) {
            preco = listPrice.price_discount;
        } else {
            preco = existProduct.preçovenda;
        }
    } else {
      preco = existProduct.preçovenda;
    }
    var quantidad = existProduct.quantidade + 1;
    if (existProduct.stock_sum_quantity >= quantidad) {

      existProduct.quantidade += 1;
      const total = preco * existProduct.quantidade;
      existProduct.total = total;
      existProduct.preco_pedido = preco;
    } else {
      return messages(
        "error",
        "Este produto ja nao ten quantidade suficiente em stock"
      );
    }
  } else {
    produto.total = produto.preçovenda;
    produto.quantidade = 1;
    produto.preco_pedido = produto.preçovenda;
    Pedido.value.items.push(produto);
    IdAlterar.value = produto.id;
  }
  showProducts.value = false
  CalcularTotal();
  SetStore();
}

const updateOrder = (numero) => {
  numeros.value = numeros.value + String(numero);
  const existProduct = Pedido.value.items.find((o) => o.id === IdAlterar.value);
  if (existProduct) {
    let listPrice = existProduct.list_price.filter((item)=>{
        return item.quantity <= numeros.value
    })
    var preco = 0;
    if (listPrice.length>0) {
      listPrice = listPrice[listPrice.length -1];
      if (numeros.value >= listPrice.quantity) {
        preco = listPrice.price_discount;
      } else {
        preco = existProduct.preçovenda;
      }
    } else {
      preco = existProduct.preçovenda;
    }
    if (TipoAlteration.value === "quantidade") {
      // A verificar se tem stock suficiente
      var quantidad = Number(numeros.value);
      if (existProduct.stock_sum_quantity >= quantidad) {
        existProduct.quantidade = quantidad;
        const total = preco * existProduct.quantidade;
        existProduct.total = total;
        existProduct.preco_pedido = preco;
        console.log(existProduct);
        SetStore();
      } else {
        console.log(existProduct);
        return messages(
          "error",
          "Este produto ja nao ten quantidade suficiente em stock"
        );
      }
    } else {
      if (user.value.config.length <= 0 || user.value.config.listPrice === "0") {
        return messages("info", "Usuario sem aceso");
      } else {
        const total = numeros.value * existProduct.quantidade;
        existProduct.total = total;
        existProduct.preco_pedido = numeros.value;
      }
    }
    SetStore();
    CalcularTotal();
  }
};

const Remover = () => {
  if (Pedido.value.items != null) {
    let newList = Pedido.value.items.filter(
      (item) => item.id !== IdAlterar.value
    );
    Pedido.value.items = newList;
  } else {
    Pedido.value.items.pop();
  }
  CalcularTotal();
  SetStore();
  let ultimo = Pedido.value.items[Pedido.value.items.length - 1];

  if (ultimo) {
    IdAlterar.value = ultimo.id;
  }
};

const CalcularTotal = () => {
  // aqui vai a lógica do total
  Pedido.value.total = 0;
  var ss = Pedido.value.items.filter((novo) => novo.total);
  ss.forEach((novo) => {
    Pedido.value.total += Number(novo.total);
  });
  return Pedido.value.total;
};

const makePayment = (event) => {
  // A verificar se existe alguns total para pagar
  if (Pedido.value.items.length <= 0) {
    Form_Pagamento.value = false;
    return messages(
      "info",
      "Não tem nehum item no carrinho por favor adicione"
    );
  } else {
    IdEncomenda.value = Pedido.value.number;
    Form_Pagamento.value = true;
  }
};
</script>
<style lang="sass" scoped>
@import '../../../assets/Pos/css/MenuPos'
</style>
