<template>
  <Transition name="bounce">
    <NewCategory
      v-if="categorys.StateModal"
      @FecharNewCateg="OnMounted"
      :category="categorys.SingleCateg"
    />
  </Transition>
  <Muvementos
    :product="produto.produtos"
    v-if="$store.state.pos.EstadoMuvemento.estado"
  />
  <div v-else class="principal">
    <div class="Header">
      <div class="Header-left">
        <strong>Novo produto</strong>
        <div class="guardar_descartar">
          <button
            @click="submit"
            v-if="StateEditProd && produto.produtos.state != 'active'"
            class=""
          >
            Guardar
            <i v-if="loading == 1" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
          </button>
          <button type="button" class="botao_descartar"
            v-if="!StateEditProd && produto.produtos.estado != 1"
            @click="StateEditProd = true"
          >
            Editar
          </button>
          <button type="button" class="botao_descartar"
            v-if="StateEditProd && produto.produtos.estado != 1"
            @click="StateEditProd = false"
          >
            Descartar
          </button>
          <button v-if="!StateEditProd" type="button" @click="$emit('descartar')" class="mx-1 botao_descartar">
            Fechar
          </button>
        </div>
      </div>
      <div class="Header-right">
        <Confirm
            :product="produto.produtos"
            @Voltar="$emit('descartar')"
            @produto="OnMounted"
            class="Header-right"
        />
      </div>
    </div>
    <div class="Formulario">
      <form class="FormNewProd">
        <div class="guard_descart">
          <div  v-if="user.nivel == 'admin'" class="p-1 border-bottom">
            <div class="guardar_descartar" v-if="produto.produtos.estado != 1">
              <div v-for="item in Entradasaida.tipo" :key="item.id" @click="EntradaSaidaProd(item)">{{item.name+" de stock"}}</div>
            </div>
          </div>
        </div>
        <EntradaSaida
          :dados="Entradasaida"
          v-if="Entradasaida.estado"
          @Confirmar="OnMounted"
          @fechar="Entradasaida.estado = false"
        />
        <div class="form-container">
            <div class="Headers">
                <div class="drop dropdown-toggle" @click="stateDrop = stateDrop == 'mov' ? '' : 'mov'"><i class="fa fa-bars"></i></div>
                <div v-if="user.nivel == 'admin'" class="movements" :class="stateDrop == 'mov' ? 'active' : ''">
                    <div v-for="item in produto.type_movements"
                        :key="item.id" class="BotaoMuvementos">
                        <div @click="muvementos(item)" class="view_muvementos">
                            <i :class="item.icon"></i>
                            <div class="w-100">
                                <div>{{ item.count + ",00Un(s)" }}</div>
                                <strong class="TipoMuv">{{ item.name }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="BotaoMuvementos">
                        <div class="view_muvementos">
                            <i class="fa fa-bar-chart"></i>
                            <div class="w-100">
                                <div>{{produto.produtos.stock_sum_quantity + ",00Un(s)" }}</div>
                                <strong class="TipoMuv">{{ "Stock real" }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Main">
                <div class="Name-Img-control">
                    <div class="form-nome">
                        <input type="text" :disabled="produto.produtos.estado === 1 || !StateEditProd" v-model="produto.produtos.nome" placeholder="Digite nome do produto">
                    </div>
                    <div class="form-image">
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
                <div class="info-basic">
                    <div class="form-content">
                        <div class="form-Control">
                            <label for="category">Categoria:</label>
                            <button @click="stateDrop = 'category'" type="button" id="category"
                            :disabled="produto.produtos.estado === 1 || !StateEditProd">
                                {{produto.produtos.category != null ? produto.produtos.category.name : 'Seleciona a categoria do produto'}}
                            </button>
                            <div v-if="stateDrop == 'category'" class="drop">
                                <span v-for="item in categorys.liste" :key="item.id" @click="AddCategoryObject(item)">{{item.name}}</span>
                                <button type="button" @click="newCategory(produto.produtos.id)">Criar nova categoria</button>
                            </div>
                        </div>
                        <div class="form-Control">
                            <label for="productType">Tipo de artigo:</label>
                            <button @click="stateDrop = 'type'" type="button" id="productType"
                            :disabled="produto.produtos.estado === 1 || !StateEditProd">
                                {{produto.produtos.product_type != null ? produto.produtos.product_type.name : 'Seleciona o tipo de artigo'}}
                            </button>
                            <div v-if="stateDrop == 'type'" class="drop">
                                <span v-for="item in productType" :key="item.id" @click="addTypeProduct(item)">{{item.name}}</span>
                            </div>
                        </div>
                        <div class="form-Control">
                            <label for="bare">Codego de barro:</label>
                            <input type="text" v-model="produto.produtos.codego" :disabled="produto.produtos.estado === 1 || !StateEditProd" id="bare" placeholder="bare code">
                        </div>
                    </div>
                    <div class="form-content">

                    </div>
                </div>
            </div>
            <div class="ContainerFooter">
                <InfoProd v-if="user.nivel == 'admin'" @prices="Precos" :produto="produto" />
            </div>
        </div>
      </form>
    </div>
  </div>
  <Toast/>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { useForm } from "@inertiajs/inertia";
import { onMounted, reactive, ref } from "@vue/runtime-core";
import { useStore } from "vuex";
import EntradaSaida from "@/Components/products/entry.vue";
import Muvementos from "./movements.vue";
import InfoProd from "./InfoProduct.vue";
import Progress from "@/components/confirmation/progress.vue";
import Confirm from "../confirmation/confirm.vue";
import NewCategory from "./CategoryProduct.vue";
import {useUploadImage} from '@/composable/public/UploadImage'
import {getImages} from '@/composable/public/getImages'
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";

const emits = defineEmits(["descartar", "saved"]);
const store = useStore();
const toast = useToast()
const StateEditProd = ref(false);
const props = defineProps({
  product: Object,
});
const loading = ref(0)
const user = ref([])
const stateDrop = ref(null)
const productType = ref([]);

const categorys = reactive({
  liste: [],
  SingleCateg: {
    idProduct: null,
    category: null,
  },
  StateModal: false,
});

const element = reactive({
  img: "/produtos/image/" + props.product.image,
});

const image = ref();
const FormetDineiro = new Intl.NumberFormat("pt-AO", {
  style: "currency",
  currency: "AOA",
});

const produto = reactive({
  produtos: props.product,
  imagem: null,
  tipo: "precos",
  type_movements:[]
});

const Entradasaida = ref({
  estado: null,
  tipo: [],
  product: produto.produtos,
  store: []
});

const {createImg,onFileChange} = useUploadImage(produto, element);
const {getImage,RemoveImage} = getImages(element);


const EntradaSaidaProd = (event) => {
  Entradasaida.value.store = event;
  Entradasaida.value.product = produto.produtos;
  Entradasaida.value.estado = true;
};

const muvementos = (event) => {
  store.state.pos.EstadoMuvemento.estado = true;
  store.state.pos.EstadoMuvemento.tipo = event;
};

const newCategory = (idProduct) => {
  categorys.StateModal = true;
  categorys.SingleCateg.idProduct = idProduct;
};

const AddCategoryObject = (event) => {
    produto.produtos.category = event
    produto.produtos.category_product_id = event.id;
    stateDrop.value = null
};

const OnMounted = onMounted(async () => {
   await getImage();
  store.state.pos.StateProgress = true;
  await axios
    .get(`/products/${props.product.id}`)
    .then((Response) => {
      user.value = Response.data.user
      produto.produtos = Response.data.product;
      productType.value = Response.data.product_type;
      produto.type_movements = Response.data.type_movements
      store.state.pos.StateProgress = false;
      categorys.StateModal = false;
      StateEditProd.value = true;
      calcMovement();
      categorys.liste = Response.data.categorys;
    })
    .catch((err) => {
      emits("descartar");
      console.log(err);
      categorys.StateModal = false;
      StateEditProd.value = true;
      store.state.pos.StateProgress = false;
    }).finally(()=>{
        categorys.StateModal = false;
        StateEditProd.value = true;
        store.state.pos.StateProgress = false;
    });
});

const calcMovement = () => {
  if (produto.type_movements == null) return;
  Entradasaida.value.tipo = [];
  produto.type_movements.forEach((element) => {
    element.count = 0;
    if (element.name == "Entrada" || element.name == "Saida") {
        Entradasaida.value.tipo.push(element)
    }
    if (element.movements) {
        element.movements.forEach((item) => {
            Number((element.count += item.quantity));
        });
    }
  });
};

const addTypeProduct = (type) => {
  produto.produtos.product_type = type
  produto.produtos.product_type_id = type.id;
  stateDrop.value = null
};

const Precos = (tipo, valor) => {
  produto.produtos[tipo] = valor;
};

const submit = async() => {
  loading.value = true;
  if (produto.imagem != null) {
    produto.imagem = element.img;
  }
    await axios
    .post(`/update/${produto.produtos.id}`, produto)
    .then((Response) => {
        message(Response.data.message,Response.data.type)
        StateEditProd.value = false;
        emits("saved", Response.data.data);
    })
    .catch((err) => {
      console.log(err);
    }).finally(()=>{
        loading.value = false;
    });
};

const message = ((message,type)=>{
    toast.add({
        severity: type,
        summary: 'Informação',
        detail: message,
        life:5000,
    })
})
</script>

<style lang="scss" scoped>
@import "../../../assets/produtos/css/produto";
</style>
