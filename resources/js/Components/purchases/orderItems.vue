<template>
   <div class="h-100 w-100">
      <Transition name="bounce">
         <div>
            <Addbox @message="message" @Close="caixa.state = false" @Confirmou="BoxConfirm" v-if="caixa.state" :product="caixa.artigo"/>
            <div class="NewProduct" v-if="ModalProduct.state">
               <div class="Modal">
                  <Product :product="ModalProduct.produto" @Guardar="SelectProduct"/>
               </div>
            </div>
         </div>
      </Transition>

      <div class="Container">
         <div class="d-flex TitlsPedido">
            <div class="text-start">Produto</div>
            <div>Quantidade</div>
            <div>Preço</div>
            <div>Desconto</div>
            <div>Iva</div>
            <div>Gastos</div>
            <div>Preço final</div>
            <div>Total</div>
            <div v-if="order.state =='Cotação'">Ação</div>
         </div>
         <div class="w-100">
            <div class="ListaPedidos" v-for="item in order.items" :key="item.id">
               <input type="text" :value="item.product?.nome" class="nameItem" readonly/>

               <input type="text"
                  v-model="item.quantity"
                  @click="SelectText(item.id,'quantidade')"
                  @keyup.enter="UpdateItem(item)"
                  :class="'quantidade'+item.id"
                  id="inputRef" ref="inputRef"
                  :disabled="order.state != 'Cotação'"
                  placeholder="Quantidade"
                />

               <input placeholder="Preço" @click="SelectText(item.id,'custo')"
                  @keyup.enter="UpdateItem(item)" v-model="item.priceCost"
                  :class="'custo'+item.id"
                  id="inputRef" ref="inputRef"
                  :disabled="order.state != 'Cotação'"
                  type="text"
                />
               <input placeholder="Desconto" v-model="item.discount"
                  @click="SelectText(item.id,'desconto')"
                  :class="'desconto'+item.id"
                  id="inputRef" ref="inputRef"
                  :disabled="order.state != 'Cotação'"
                  @keyup.enter="UpdateItem(item)" type="text"
                  />
               <input placeholder="Iva" v-model="item.tax"
                  :class="'iva'+item.id"
                  @click="SelectText(item.id,'iva')"
                  :disabled="order.state != 'Cotação'"
                  @keyup.enter="UpdateItem(item)" type="text"/>

               <input placeholder="Gastos" v-model="item.spent"
                  :class="'gasto'+item.id"
                  @click="SelectText(item.id,'gasto')"
                  :disabled="order.state != 'Cotação'"
                  @keyup.enter="UpdateItem(item)" type="text"
                />

               <input type="text" :value="formatMoney(item.finalPrice)" required disabled/>

               <input type="text" :value="formatMoney(item.totalItem)" required disabled/>

               <div v-if="order.state =='Cotação'">
                  <Caixa class="icone" @click="Addcaixa(item)"/>
                  <i @click="DeleteItem(item.id)" class="fa fa-trash"></i>
               </div>
            </div>
         </div>
      </div>
      <div class="ResultadoTotal border-top bg-white d-flex">
         <div class="w-50">
            <div v-if="order.state === 'Cotação'">
               <input
                  @click="getProducts('')"
                  id="Produto"
                  v-model="produto.nome"
                  placeholder="Selectionar produto"
                  @keyup="Pesquisar"
                  class="w-75 p-1 border-bottom"
                />
               <div v-if="produto.state" class=" text-secondary rounded-bottom bg-white ListaProdutos overflow-auto w-75 border border-top-0">
                  <div
                    v-for="produto in produto.lista.slice(0,30)"
                    @click="SelectProduct(produto)"
                    :key="produto.id"
                    id="ListaProdutos"
                    class="d-flex"
                >
                    {{produto.nome}}
                     <div v-if="produto.stock_sum_quantity <= 0"  class="StockEsgatado">
                        <i class="fa fa-info-circle text-danger" aria-hidden="true"></i>
                     </div>
                  </div>
                  <div class="BotoesCriar">
                     <button @click="NewProduct">Criar {{produto.nome}}</button>
                     <button @click="CreateUpdateProduct">Criar e editar {{produto.nome}}</button>
                  </div>
               </div>
            </div>
         </div>
         <div class="w-50">
            <div class="d-flex">
               <div class="TitlTotals">
                  <div>Total da mercadoria :</div>
                  <div>Total do disconto :</div>
                  <div>Total da iva :</div>
                  <div>Total de Gastos :</div>
               </div>
               <div class="Totals">
                  <div>{{formatMoney(order.totalMerchandise)}}</div>
                  <div>{{formatMoney(order.totalDiscount)}}</div>
                  <div>{{formatMoney(order.totalTax)}}</div>
                  <div>{{formatMoney(order.TotalSpending)}}</div>
               </div>
            </div>
            <div class="w-100 mt-3 d-flex border-top">
               <div class="TitlTotals">Total da fatura :</div>
               <div class="Totals">
                  {{formatMoney(order.total)}}
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import Addbox from './AddBox.vue'
import { computed, onMounted,reactive,ref,watch} from '@vue/runtime-core';
import axios from 'axios';
import Product from '@/components/products/product.vue'
import { useStore } from 'vuex';
import Caixa from 'vue-material-design-icons/PackageVariantClosedPlus.vue'
import { useCurrencyInput } from 'vue-currency-input';
import useEventsBus from '@/Eventbus';
const {bus} = useEventsBus();
const formatDinheiro = Intl.NumberFormat('PT-AO', {
   style: 'currency',
   currency: 'AOA'
});
const props = defineProps({
   id_order:{
      type: Number,
      required:true
   }
})
const {inputRef} = useCurrencyInput({
   currency: 'AOA'
})
const store = useStore()
const emits = defineEmits(['message', 'progress', 'encomenda'])
const order = ref([])
const ModalProduct = ref({
   state: false,
   produto: Object
})
const SelectText = ((id, tipo) => {
   var classe = tipo + id
   document.querySelector('.' + classe).select();
})
const caixa = reactive({
   artigo: null,
   gasto: true,
   state: false
})
const Addcaixa = ((artigo) => {
   caixa.artigo = artigo
   caixa.state = true
})
const produto = ref({
   nome: null,
   state: false,
   lista: [],
   store: []
});
const message = ((message, tipo) => {
   emits('message', message, tipo)
})
const OnMounted = onMounted(() => {
    requestGet(`getPurchases/${props.id_order}`)
})


const NewProduct = (() => {
   if (produto.value.nome != '' && produto.value.nome != null) {
      requestPost(`/new_product/${produto.value.nome}`)
   } else {
      emits('message', 'Não pode adicionar um produto com nome vazio ', 'info')
   }
})

const getProducts = (order) => {
   emits('progress')
   axios.get('/products').then((response) => {
      produto.value.lista = response.data
      produto.value.store = response.data
      produto.value.state = true
   }).finally(()=>{
      emits('progress')
   })
}
const SelectProduct = (event) => {
   const Verificar = order.value.items.find(item => item.produtos_id === event.id)
   if (!Verificar) {
      produto.value.state = false
      event.idOrdem = order.value.id
      requestPost(`AddItemPuchase/${props.id_order}`,event)
   } else {
      message('Este produto ja encontra na encomenda ', 'info')
   }
}

const requestGet = (rota) => {
   ModalProduct.value.state = false
   axios.get(`${rota}`).then((Response) => {
      order.value = Response.data
      if (Response.data.message) {
         message(Response.data.message, Response.data.type)
      } else {
         emits('encomenda', order.value);
      }
   })
}

const requestPost  = (route, param = null) => {
    ModalProduct.value.state = false
   axios.post(`${route}`, {...param}).then((Response) => {
      order.value = Response.data

      if (Response.data.message) {
         message(Response.data.message, Response.data.type)
      } else {
         emits('encomenda', order.value);
      }
   })
}

const TotalMercadoria = (() => {
   return Number(order.value.total) + Number(order.value.totalDiscount) -
      Number(order.value.totalTax) -
      Number(order.value.TotalSpending)
})

const UpdateItem = ((Item) => {
   return requestPost(`UpdateItems/${Item.id}`, Item);
})

const DeleteItem = ((id) => {
   axios.delete(`deleteItem/${order.value.id}/${id}`).then((response)=>{
    if (response.data.message) return emits('message',response.data.message,response.data.type)
      order.value = response.data
   })
})

const Pesquisar = ((event) => {
   const filtrar = produto.value.store.filter(object => {
      return String(object.nome).toLowerCase().includes(event.target.value.toLowerCase(), 0)
   })
   if (filtrar.length != 0) {
      produto.value.lista = filtrar
   } else {
      produto.value.lista = []
   }
})

const BoxConfirm = ((item) => {
    console.log(order.value);
   const Rows = order.value.items.filter(item => item.id === item.id)

   Rows[0].quantity = item.QuatidadeFinal
   Rows[0].custo = item.PrecoUnidade
   Rows[0].spent = item.gasto
   caixa.state = false
   return UpdateItem(Rows[0])
})

const CreateUpdateProduct = (() => {
   axios.post(`/new_product/${produto.value.nome}`)
      .then((Response) => {
         ModalProduct.value.produto = Response.data.produto
         ModalProduct.value.state = true
      }).catch((err) => {
         console.log(err);
      });
})
</script>

<style scoped lang="scss">
@import "../../../assets/faturacao/css/FaturacaoPedido.scss";
.NewProduct{
    @include modal;
    .Modal{
        width: 90% !important;
        height: 90% !important;
    }
}
</style>
