<template>
  <Progress v-if="$store.state.pos.StateProgress" />
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
          <button type="button" @click="$emit('descartar')" class="mx-1 botao_descartar">
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
        <div class="form_novo_prod">
          <div class="h-100">
            <div class="TopProd">
              <div class="ProdutoBarraCima">
                <div v-if="user.nivel == 'admin'" class="d-flex">
                  <div
                    v-for="item in produto.type_movements"
                    :key="item.id"
                    class="BotaoMuvementos"
                  >
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
                        <div>{{product.stock_sum_quantity + ",00Un(s)" }}</div>
                        <strong class="TipoMuv">{{ "Stock real" }}</strong>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
              <div class="FormContainer">
                <div class="input_nome_tipo">
                  <div>
                    <div>
                      <label for="nome_do_artigo">Nome do Artigo</label>
                    </div>
                    <div>
                      <input
                        type="text"
                        v-model="produto.produtos.nome"
                        class="nome_artigo_data"
                        :disabled="
                          produto.produtos.estado === 1 || !StateEditProd
                        "
                        id="nome_do_artigo"
                        placeholder="Digite nome de produto"
                      />
                    </div>
                  </div>
                  <div class="ContainerInfo">
                    <div class="Titulos">
                      <div class="w-100 text-sm p-1">
                        <strong>Tipo de artigo</strong>
                      </div>
                      <div class="w-100 text-sm p-1">
                        <strong>QR codigo de barra</strong>
                      </div>
                    </div>

                    <div class="InfoContainer">
                      <div class="TipoCategoria">
                        <div class="tipoartigo">
                          <select
                            @change="addTypeProduct"
                            class="tipo_artigo"
                            :disabled="
                              produto.produtos.estado === 1 || !StateEditProd
                            "
                          >
                            <option
                              v-if="produto.produtos.product_type_id == null"
                            >
                              Selecionar Categoria
                            </option>
                            <option
                              v-for="item in productType"
                              :key="item.id"
                              :value="item.name"
                              :selected="
                                produto.produtos.product_type_id == item.id
                              "
                            >
                              {{ item.name }}
                            </option>
                          </select>
                        </div>

                        <div class="DivCategoria">
                          <select
                            @change="AddCategoryObject"
                            :disabled="
                              produto.produtos.estado === 1 || !StateEditProd
                            "
                            class="Categoria"
                          >
                            <option
                              v-if="
                                produto.produtos.category_product_id == null
                              "
                            >
                              Selecionar Categoria
                            </option>
                            <option
                              v-for="item in categorys.liste"
                              :key="item.id"
                              :value="item.name"
                              :selected="
                                produto.produtos.category_product_id == item.id
                              "
                            >
                              {{ item.name }}
                            </option>
                          </select>
                          <button
                            class="AdicionarCategoria"
                            type="button"
                            @click.prevent="NewCategoria(produto.produtos.id)"
                          >
                            <i class="fa fa-plus"></i>Add Categoria
                          </button>
                        </div>
                      </div>
                      <div class="CodeBar">
                        <input
                          type="text"
                          placeholder="QR code bar"
                          :disabled="
                            produto.produtos.estado === 1 || !StateEditProd
                          "
                          v-model="produto.produtos.codego"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="image">
                    <div class="form-image">
                        <div>
                            <!-- <canvas v-if="element.stateCanvas" id="myCanvas" width="100" height="100"></canvas> -->
                            <img :src="element.img">
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
            </div>
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

const user = ref([])

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

const NewCategoria = (idProduct) => {
  categorys.StateModal = true;
  categorys.SingleCateg.idProduct = idProduct;
};

const AddCategoryObject = (event) => {
  let CategoriaFiltrada = categorys.liste.filter(
    (item) => item.name === event.target.value
  );
  produto.produtos.category_product_id = CategoriaFiltrada[0].id;
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
      calcMovement();
      categorys.liste = Response.data.categorys;
      store.state.pos.StateProgress = false;
      categorys.StateModal = false;
      StateEditProd.value = true;
    })
    .catch((err) => {
      emits("descartar");
      console.log(err);
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
  let types = productType.value.filter(
    (item) => item.name === type.target.value
  );
  produto.produtos.product_type_id = types[0].id;
};

const Precos = (tipo, valor) => {
  produto.produtos[tipo] = valor;
};

const submit = () => {
  store.state.pos.StateProgress = true;
  if (produto.imagem != null) {
    produto.imagem = element.img;
  }
  axios
    .post(`/update/${produto.produtos.id}`, produto)
    .then((Response) => {
        message(Response.data.message,Response.data.type)
      store.state.pos.StateProgress = false;
      StateEditProd.value = false;
      emits("saved", Response.data.data);
    })
    .catch((err) => {
      store.state.pos.StateProgress = false;
      console.log(err);
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
