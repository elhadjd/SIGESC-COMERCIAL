<template>
   <!-- <Addbox :artigo="AddBox.artigo" @Close="AddBox.state = false" @Confirmou="BoxConfirm" v-if="AddBox.state"/> -->
   <div class="h-100 w-100">
      <div class="Container">
         <div class="d-flex TitlsPedido">
            <div class="text-start">Produto</div>
            <div>Quantidade</div>
            <div>Preço</div>
            <div>Desconto</div>
            <div>Total</div>
            <div>Ação</div>
         </div>
         <div class="w-100">
            <div class="ListaPedidos" 
                v-for="item in Invoice.items" 
                :key="item.id"
            >
               <input type="text" class="text-start nameItem" readonly :value="item.product.nome">
               <input @keyup.enter="UpdateItem(item)" @click="Alteracao(item.id, 'quantidade')"
                  v-model="item.quantity"
                  type="text"
                  :disabled="Invoice.state !='Cotação'"
                  placeholder="Quantidade"
                  :class="'quantidade'+item.id"/>
               <input
                  placeholder="Preço"
                  type="text"
                  :disabled="Invoice.state !='Cotação'"
                  v-model="item.PriceSold"
                  @keyup.enter="UpdateItem(item)"
                  @click="Alteracao(item.id, 'PrecoVenda')"
                  :class="'PrecoVenda'+item.id"/>
               <input 
                  @keyup.enter="UpdateItem(item)" 
                  @click="Alteracao(item.id, 'Desconto')"
                  v-model="item.discount"
                  :class="'Desconto'+item.id"
                  :disabled="Invoice.state !='Cotação'"
                  placeholder="Desconto" type="text" />
               <input
                  placeholder="Total"
                  :value="formatMoney(item.TotalSold)"
                  type="text" required disabled />
               <div>
                  <Caixa  
                  v-if="Invoice.state =='Cotação'" 
                  class="icone" 
                  @click="Addcaixa(item)
                  "/>
                  <i @click="DeleteProduct(item.id)"  v-if="Invoice.state =='Cotação'" class="fa fa-trash"></i>
               </div>
            </div>
         </div>
      </div>
      <div class="ResultadoTotal border-top bg-white d-flex">
         <div class="w-50">
            <div v-if="Invoice.state === 'Cotação'">
               <input @click="ShowProducts" @keyup="SearchProduct" id="Produto" placeholder="Selectionar produto" class="w-75 p-1 border-bottom" />
               <div v-show="Produtos.ShowProd" class=" text-secondary rounded-bottom
                  bg-white ListaProdutos overflow-auto w-75 border border-top-0 ">
                  <div v-for="produto in Produtos.ListProducts" @click="AddProduct(produto)" :key="produto.id"
                     id="ListaProdutos" class="d-flex" >
                     {{ produto.nome }}
                     <div v-if="produto.stock_sum_quantity <= 0" class="StockEsgatado">
                        <i class="fa fa-info-circle text-danger" aria-hidden="true"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="w-50">
            <div class=" d-flex">
               <div class="TitlTotals">
                  <div>Total da mercadoria : </div>
                  <div>Total do disconto : </div>
               </div>
               <div class="Totals">
                  <div>{{formatMoney(Invoice.TotalMerchandise)}} </div>
                  <div>{{formatMoney(Invoice.discount)}}</div>
               </div>
            </div>
            <div class="w-100 mt-3 d-flex border-top">
               <div class="TitlTotals">Total da fatura : </div>
               <div class="Totals">{{formatMoney(Invoice.TotalInvoice)}}</div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
// import Addbox from '../Compras/AddBox.vue'
import { onMounted ,reactive,ref, watch} from "@vue/runtime-core"
import axios from "axios"
import Caixa from 'vue-material-design-icons/PackageVariantClosedPlus.vue'
import useEventsBus from '@/Eventbus'

const {bus} = useEventsBus()

const FrmatDinheiro = Intl.NumberFormat('PT-AO',{
    style: 'currency', currency: 'AOA'
})

const AddBox = ref({
    state: false,
    gasto: false,
    artigo: []
})
const props = defineProps({
    Invoice:{
        default: {}
    }
})
const emits = defineEmits(['message'])

const Produtos = ref({
    ShowProd: false,
    ListProducts: [],
    StoreProducts: []
})

const Invoice = ref([])

const OnMounted = onMounted(async ()=>{
    Invoice.value = props.Invoice
    return getItems(`getItems/${props.Invoice.id}`)
})

const ShowProducts = (()=>{
    axios.get("/products").then((Response) => {
        Produtos.value.ShowProd = true
        Produtos.value.ListProducts = Response.data;
        Produtos.value.StoreProducts = Response.data;
    });
})

const getItems = (async (rota)=>{
    await axios.get(rota)
    .then((Response) => {
        Invoice.value = Response.data
    })
})

const SearchProduct = ((event)=>{
    const FilterSearch = Produtos.value.StoreProducts.filter(object=>{
        return object.nome.toLowerCase().includes(event.target.value.toLowerCase())
    })
    if (FilterSearch.length > 0) {
        Produtos.value.ListProducts = FilterSearch
    } else {
        emits('message','Nenhum produto encontrado com este nome','info')
    }
})

const AddProduct = ((product)=>{
    if (Number(product.preçovenda) < Number(product.preçocust)) {
        emits('message','O preço deste produto é invalido','info')
    } else {
        if (Number(product.stock_sum_quantity)<=0) {
            emits('message','A quantidade deste produto não e suficiente ','info')
        } else {
            axios.post(`AddItem/${product.id}/${Invoice.value.id}`)
            .then((Response) => {
                if (Response.data.message) {
                    emits('message',Response.data.message,'info')
                } else {
                    Invoice.value = Response.data
                }
            }).catch((err) => {
                console.log(err);
            });
        }
    }
})

const DeleteProduct = ((item)=>{
    if (Invoice.value.state == 'Cotação') {
        axios.delete(`deleteItem/${Invoice.value.id}/${item}`)
        .then((Response) => {
            Invoice.value = Response.data
        }).catch((err) => {
            console.log(err);
        });
    }
})

const UpdateItem = ((Rows)=>{
    if (Invoice.value.state == 'Cotação') {
        if (Number(Rows.quantidade) > Number(Rows.stock_sum_quantity)) return emits('message',`Atenção a quantidade fornecida não é suficiente`,'info')
        if (Rows.PrecoCusto >= Number(Rows.PrecoVenda)) emits('message',`Atenção o preço fornecido não e valido`,'info')
        axios.post(`/UpdateRows/${Invoice.value.id}`,{dados:Rows})
        .then((Response) => {
            Invoice.value = Response.data
        }).catch((err) => {
            console.log(err);
        });
    }
})

const Addcaixa = ((artigo)=>{
    AddBox.value.artigo = artigo
    AddBox.value.state = true
})

const BoxConfirm = ((dados)=>{
    const Rows = Invoice.value.ItemInvoice.filter(item=> item.id === dados.id)
    Rows[0].quantidade = dados.QuatidadeFinal
    Rows[0].PrecoVenda = dados.PrecoUnidade
    AddBox.value.state = false
    return UpdateItem(Rows[0])
})

 const Alteracao = (id, tipo)=> {
    if (Invoice.value.state === 'Cotação') {
        var classe = tipo + id
        document.querySelector('.' + classe).select();
    } else {
        emits('message','Atenção esta orden não pode ser alterado por estar confirmado clica em editar para poder fazer alteração','info')
        return false;
    }
}

watch(()=>bus.value.get('Invoice'), (Invoice)=>{
    Invoice.value = Invoice[0]
})
</script>

<style scoped lang="scss">
@import "../../../assets/faturacao/css/FaturacaoPedido.scss";
</style>