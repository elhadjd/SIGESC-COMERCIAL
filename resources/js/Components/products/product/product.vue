<template>
  <Muvementos v-if="product.movementsProduct.state"/>
  <div class="principal" v-else>
    <div class="Header">
      <div class="Header-left">
        <strong>{{$t('words.new') + ' ' + $t('words.article')}} </strong>
        <div class="guardar_descartar">
            <buttonsVue @closeForm="$emit('closeForm')" @saved="$emit('saved',product.data)"/>
        </div>
      </div>
      <div class="Header-right">
        <Confirm @close="$emit('closeForm'),showProduct(false)"/>
      </div>
    </div>
    <div class="Formulario">
      <form class="FormNewProd">
        <div class="guard_descart">
          <div  v-if="user.roles?.name == 'Admin'" class="p-1 border-bottom">
            <div class="bannerButtons" v-if="product.data.estado != 1">
                <MessagesToast v-if="news.state" @closeMessage="news.state = false" :message="news"/>
                <div>
                    <ButtonVue v-for="item in movementsStockProduct.TypesMovements" :className="''" :type="'button'" :key="item.id" @click="moveProductStock(item)">
                        {{item.movement_translate[0].translate + ' ' + $t('words.of') + ' stock'}}
                    </ButtonVue>
                </div>
                <div class="flex flex-row space-x-2">
                    <descriptionVue/>
                    <publishProductVue/>
                </div>
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
                <div v-if="user.roles?.name == 'Admin'" class="movements" :class="stateDrop == 'mov' ? 'active' : ''">
                    <div v-for="item in product.movementsProduct.data"
                        :key="item.id" class="BotaoMuvementos">
                        <div @click="ShowMovements(item)" class="view_muvementos">
                            <i :class="item.icon"></i>
                            <div class="w-100">
                                <div>{{ item.count + ",00Un(s)" }}</div>
                                <strong class="TipoMuv">{{ item.movement_translate[0].translate }}</strong>
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
                        <input type="text" :disabled="product.data.estado === 1"  v-model="product.data.nome" :placeholder="$t('phrases.typeProductName')">
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
                <InfoProd v-if="user.roles?.name == 'Admin'"/>
            </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
import descriptionVue from './description.vue'
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
import {useUploadImage} from '@/composable/public/UploadImage'
import {serviceMessage} from '../../../composable/public/messages'
import {ProductServices} from '../services/product/productServices';
import {ProductsServices} from '../services/productsServices';
import bannerProductVue from './bannerProduct.vue';
import ButtonVue from '@/ui/button.vue'
import buttonsVue from './buttons.vue'
import { BannerProductServices } from '../services/product/bannerProductServices';
import MessagesToast from '@/Layouts/news/messagesToast.vue';
import publishProductVue from './publishProduct.vue';
const {createProduct,showProduct} = ProductsServices()
const store = useStore();
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
    news
} = ProductServices(emits)
const {
    categories
} = BannerProductServices()

const testP = (()=>{
    console.log(0);
})

watch(element,(prod,novo)=>{
    product.value.data.imagem = element.img
})
const user = computed(()=>store.getters['publico/public'].user)

const {showMessage} = serviceMessage()
const {createImg,onFileChange} = useUploadImage(product.value.data, element);
const {getImage,RemoveImage} = getImages(element);
const OnMounted = onMounted(async () => {
   await getImage();
  store.state.pos.StateProgress = true;
  await axios
    .get(`/products/${product.value.data.id}`)
    .then((Response) => {
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
