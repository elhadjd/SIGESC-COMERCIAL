<template>
  <Muvementos v-if="product.movementsProduct.state"/>
  <div class="principal">
    <div class="Header">
      <div class="Header-left">
        <strong>Novo produto</strong>
        <div class="guardar_descartar">
            <buttonsVue @closeForm="$emit('closeForm')" @saved="$emit('saved',product.data)"/>
        </div>
      </div>
      <div class="Header-right">
        <Confirm @Voltar="showProduct(false)"/>
      </div>
    </div>
    <div class="Formulario">
      <form class="FormNewProd">
        <div class="guard_descart">
          <div  v-if="user.nivel == 'admin'" class="p-1 border-bottom">
            <div class="bannerButtons" v-if="product.data.estado != 1">
                <MessagesToast v-if="news.state" @closeMessage="news.state = false" :message="news"/>
                <div>
                    <ButtonVue v-for="item in movementsStockProduct.TypesMovements" :className="''" :type="'button'" :key="item.id" @click="moveProductStock(item)">
                        {{item.name+" de stock"}}
                    </ButtonVue>
                </div>
                <ButtonVue :className="'publish'" @click="publishProductOnline" :type="'button'">
                    Divulgar
                    <FontAwesomeIcon v-if="stateDrop == 'shopOnline'" icon="fa-solid fa-spinner" shake />
                    <FontAwesomeIcon icon="fa-solid fa-shop" v-else beat-fade />
                    <FontAwesomeIcon icon="fa-solid fa-check" class="check" />
                </ButtonVue>
            </div>
          </div>
        </div>
        <MoveStock
          :data="movementsStockProduct"
          v-if="movementsStockProduct.stateShow"
          @closeModal="movementsStockProduct.stateShow = false"
        />
        <div class="form-container">
            <div class="Headers">
                <div class="drop dropdown-toggle" @click="stateDrop = stateDrop == 'mov' ? '' : 'mov'"><i class="fa fa-bars"></i></div>
                <div v-if="user.nivel == 'admin'" class="movements" :class="stateDrop == 'mov' ? 'active' : ''">
                    <div v-for="item in product.movementsProduct.data"
                        :key="item.id" class="BotaoMuvementos">
                        <div @click="ShowMovements(item)" class="view_muvementos">
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
                                <div>{{product.data.stock_sum_quantity + ",00Un(s)" }}</div>
                                <strong class="TipoMuv">{{ "Stock real" }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Main">
                <div class="Name-Img-control">
                    <div class="form-nome">
                        <input type="text" :disabled="product.data.estado === 1" v-model="product.data.nome" placeholder="Digite nome do produto">
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
                <bannerProductVue :listCategories="categories.listCategories" :itemTypeProps="itemType"/>
            </div>
            <div class="ContainerFooter">
                <InfoProd v-if="user.nivel == 'admin'"/>
            </div>
        </div>
      </form>
    </div>
    <!-- <Progress v-if="store.state.pos.StateProgress"/> -->
  </div>
  <Toast/>
</template>
<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { useForm } from "@inertiajs/inertia";
import { computed, onMounted, reactive, ref, watch } from "@vue/runtime-core";
import { useStore } from "vuex";
import MoveStock from "@/Components/products/product/entry.vue";
import Muvementos from "./movements.vue";
import InfoProd from "./InfoProduct.vue";
import Progress from "@/components/confirmation/progress.vue";
import Confirm from "../../confirmation/confirm.vue";
import {getImages} from '@/composable/public/getImages'
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import {useUploadImage} from '@/composable/public/UploadImage'
import {serviceMessage} from '../../../composable/public/messages'
import {ProductServices} from '../services/product/productServices';
import {ProductsServices} from '../services/productsServices';
import bannerProductVue from './bannerProduct.vue';
import ButtonVue from '@/ui/button.vue'
import buttonsVue from './buttons.vue'
import { BannerProductServices } from '../services/product/bannerProductServices';
import MessagesToast from '@/Layouts/news/messagesToast.vue';
const {createProduct,showProduct} = ProductsServices()
const store = useStore();
const toast = useToast()
const emits = defineEmits([ "saved",'closeForm']);
const product = computed(()=> store.getters['Product/product'])
const {
    loadProduct,
    element,
    movementsStockProduct,
    itemType,
    calcMovementProduct,
    stateDrop,
    moveProductStock,
    ShowMovements ,
    changePrices,
    publishProductOnline,
    news
} = ProductServices(emits)
const {
    categories
} = BannerProductServices()

watch(element,(prod,novo)=>{
    product.value.data.imagem = element.img
})
const user = ref([])
const {showMessage} = serviceMessage()
const {createImg,onFileChange} = useUploadImage(product.value.data, element);
const {getImage,RemoveImage} = getImages(element);
const OnMounted = onMounted(async () => {
   await getImage();
  store.state.pos.StateProgress = true;
  await axios
    .get(`/products/${product.value.data.id}`)
    .then((Response) => {
        user.value = Response.data.user
        loadProduct(Response.data.product)
        itemType.value = Response.data.product_type;
        calcMovementProduct(Response.data.type_movements);
        categories.listCategories = Response.data.categorys;
    })
    .catch((err) => {
      showProduct(false);
      console.log(err);
      categories.StateModal = false;
    }).finally(()=>{
        categories.StateModal = false;
        store.state.pos.StateProgress = false;
    });
});

</script>

<style lang="scss" scoped>
@import "../../../../assets/produtos/css/produto";
</style>
