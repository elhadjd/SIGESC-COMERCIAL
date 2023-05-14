<template>
   <div class="all">
        <div v-if="loading == 'start'" class="loading">
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw edit-product"></i>
        </div>
      <div class="NewProduct" v-if="modalProduct.state">
         <div class="Modal">
            <product :product="modalProduct.product" @descartar="modalProduct.state = false" @saved="addItem"/>
         </div>
      </div>
      <Addbox
      :item="AddBox.product"
      @close="AddBox.state = false"
      @confirm="BoxConfirm"
      @BoxMessage="BoxMessage"
      v-if="AddBox.state"
      />
      <div class="Container">
         <div class="d-flex items">
            <div
               v-for="title,idx in general.form.inputs"
               :key="idx"
               :class="title.class"
               >
               {{title.label}}
            </div>
            <div v-if="order.state =='Cotação' || stateFormOrder == 'Cotação'">Ação</div>
         </div>
         <div class="list-items">
            <div
               v-for="item in orderItems.items"
               :key="item.id"
               class="items"
               >
               <div v-for="input,idx in general.form.inputs" :key="idx">
                  <input
                  v-if="input.type != 'select' && !input.disabled"
                  @keyup.enter="UpdateItem(item)"
                  @keyup="(e) => (item[input.name] = e.target.value)"
                  :value="item[input.name]"
                  @click="Alteracao(item.id,  input.name)"
                  :type="input.type"
                  :placeholder="input.label"
                  :class="input.class + item.id"
                  :disabled="input.disabled || stateFormOrder != 'Cotação'"
                  />
                  <span :class="input.product ? 'item ' :''" v-else-if="input.disabled">
                  <span :class="input.product ? input.class : input.class + item.id" >{{input.product ? item.product.nome : formatMoney(item[input.name])}}</span>
                  <i @click="editProduct(item.product)" :class="input.product ? 'fa fa-pencil-square-o edit-product' : ''" aria-hidden="true"></i>
                  </span>
                  <select :disabled="stateFormOrder != 'Cotação'" :class="input.class" @change="(e)=>{item.armagen_id = e.target.value,UpdateItem(item)}" v-else>
                     <option v-for="store in stores" :key="store.id" :value="store.id" :selected="item.armagen_id == store.id">{{store.name}}</option>
                  </select>
               </div>
               <div v-if="order.state =='Cotação' || stateFormOrder == 'Cotação'">
                  <Caixa
                     class="icone"
                     @click="Addcaixa(item)
                     "/>
                  <i v-if="loading == 'item'+item.id" class="fa fa-spinner fa-pulse fa-3x fa-fw edit-product"></i>
                  <i v-else @click="DeleteItem(item.id)" class="fa fa-trash"></i>
               </div>
            </div>
         </div>
      </div>
      <div class="ResultadoTotal border-top bg-white d-flex">
         <div class="w-50">
            <form-select
            v-if="stateFormOrder == 'Cotação'"
            @message="BoxMessage"
            @addItem="addItem"
            @newProduct="newProduct"
            :general="general"
            :order="order">
            <i v-if="loading == 'addProd'" class="fa fa-spinner fa-pulse fa-3x fa-fw edit-product"></i>
            </form-select>
         </div>
         <div class="totals">
            <div class=" d-flex">
               <div class="TitlTotals">
                  <div v-for="item,idx in general.form.totalOrder.totals" :key="idx">{{item.title}} : </div>
               </div>
               <div class="Totals">
                  <div v-for="item,idx in general.form.totalOrder.totals" :key="idx">{{formatMoney(orderItems[item.amount])}} </div>
               </div>
            </div>
            <div class="w-100 mt-3 d-flex border-top">
               <div class="TitlTotals">{{general.form.totalOrder.total.title}} : </div>
               <div class="Totals">{{formatMoney(orderItems[general.form.totalOrder.total.amount])}}</div>
            </div>
            <div class="total-items">
               <strong>Total de item na encomenda :</strong>
               <span>{{orderItems.items?.length+" Items"}}</span>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

import formSelect from './form-select.vue'
import axios from "axios";
import { onMounted, reactive, ref, watch } from "vue"
import Addbox from './Caixa/index.vue'
import Caixa from 'vue-material-design-icons/PackageVariantClosedPlus.vue'
import product from '@/Components/products/product.vue'
import useEventsBus from "@/Eventbus";
const AddBox = ref({
    state: false,
    spent: false,
    product: []
})

const stateFormOrder = ref(null)

const props = defineProps({
    general:Object,
    order:Object
})
const {bus} = useEventsBus()
const modalProduct = ref({
    product: [],
    state: false,
    type: null
})
const loading = ref(null)
const stores = ref([])

const emits = defineEmits(['message']);

const orderItems = ref([]);
watch(()=>bus.value.get('stateFormOrder'),(payload)=>{
    stateFormOrder.value = payload
})
onMounted(async()=>{
    await getOrder();
    await getStores()
})

const editProduct = ((product)=>{
    modalProduct.value.product = product
    modalProduct.value.state = true
    modalProduct.value.type = 'edit'
})

async function getStores() {
    axios.get('/getArmagens')
    .then((response) => {
        stores.value = response.data.armagens
    }).catch((err) => {
        console.log(err);
    });
}

async function getOrder() {
    loading.value = 'start'
     axios.get(`${props.general.routes.getItems.name}/${props.order.id}`)
    .then((response) => {
       orderItems.value = response.data
       stateFormOrder.value = response.data.state
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        loading.value = null
    });
}

async function addItem(param) {
    modalProduct.value.state = false
    if (modalProduct.value.type == 'edit') return modalProduct.value.type = null
    loading.value = 'addProd'
    axios.post(`${props.general.routes.addItems.name}/${param.id}/${props.order.id}`)
    .then((response)=>{
        if (response.data.message) return emits('message',response.data.message,response.data.type)
        orderItems.value = response.data
    }).catch((err)=>{
        console.log(err);
    }).finally(()=>{
        loading.value = null
    })
}

const UpdateItem = ((Rows)=>{
    if (orderItems.value.state == 'Cotação') {
        if (Number(Rows.quantity) > Number(Rows.stock_sum_quantity)) return emits('message',`Atenção a quantidade fornecida não é suficiente`,'info')
        if (Rows.PriceCost >= Number(Rows.PriceSold)) emits('message',`Atenção o preço fornecido não e valido`,'info')
        loading.value = 'item'+Rows.id
        axios.post(`${props.general.routes.updateOrder.name}/${Rows.id}`,{...Rows})
        .then((Response) => {
            if (Response.data.message) emits('message',Response.data.message,Response.data.type)
            if (Response.data.data) return orderItems.value = Response.data.data
            orderItems.value = Response.data
        }).catch((err) => {
            console.log(err);
        }).finally(()=>{
            loading.value = null
        });
    }
})

const DeleteItem = (id) => {
    loading.value = 'item'+id
    axios.delete(`${props.general.routes.deleteItem.name}/${props.order.id}/${id}`)
    .then((Response) => {
    if (Response.data.message) return emits('message',Response.data.message,Response.data.type)
        orderItems.value = Response.data
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        loading.value = null
    });
}

 const Alteracao = (id, type)=> {
    if (orderItems.value.state === 'Cotação') {
        var classe = type + id
        document.querySelector('.' + classe).select();
    } else {
        emits('message','Atenção esta orden não pode ser alterado por estar confirmado clica em editar para poder fazer alteração','info')
        return false;
    }
}

const Addcaixa = ((product)=>{
    AddBox.value.product = product
    AddBox.value.state = true
})

const BoxConfirm = ((payload)=>{
    const Rows = orderItems.value.items.find(item => item.id === payload.id)
    Rows.quantity = payload.quantityFinal
    Rows.PriceSold ? Rows.PriceSold = payload.PriceUnity : Rows.priceSold = payload.PriceUnity
    AddBox.value.state = false
    return UpdateItem(Rows)
})

const BoxMessage = (message,type) => {
    emits('message',message,type)
}

const newProduct = ((name,event)=>{
    axios.post(`/new_product/${name}`)
    .then((response) => {
        if (event == 'create') return addItem(response.data.product)
        modalProduct.value.product = response.data.product
        modalProduct.value.state = true
        modalProduct.value.type = 'create'
    }).catch((err) => {
        console.log(err);
    });
})

</script>

<style lang="scss" scoped>
.all{
    width: 100%;
    height: 100%;
}
@import "../../../assets/faturacao/css/FaturacaoPedido.scss";
</style>
