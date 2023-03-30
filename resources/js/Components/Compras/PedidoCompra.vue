<template>
<div class="h-100 w-100">
    <Transition name="bounce">
        <div>
            <Addbox @message="message" @Close="caixa.estado = false" @Confirmou="BoxConfirm" v-if="caixa.estado" :artigo="caixa.artigo"/>
            <div class="NewProduct" v-if="ModalProduct.estado">
                <div class="Modal">
                    <Product :produto="ModalProduct.produto" @Guardar="SelecionarProduto"/>
                </div>
            </div>
        </div>
    </Transition>

    <div class="Container">
        <div class="d-flex TitlsPedido">
            <div class="w-25" style="text-align: left">Produto</div>
            <div>Quantidade</div>
            <div>Preço</div>
            <div>Desconto</div>
            <div>Iva</div>
            <div>Gastos</div>
            <div>Preço final</div>
            <div>Total</div>
            <div v-if="dados.estado =='Cotação'">Ação</div>
        </div>
        <div class="w-100">
            <div class="ListaPedidos" v-for="artigos in Encomenda.lista" :key="artigos.id">

                <input type="text" v-model="artigos.nome" class="w-25 text-start" readonly/>

                <input type="text"
                v-model="artigos.quantidade"
                @click="SelectText(artigos.id,'quantidade')"
                @keyup.enter="UpdateItem(artigos)"
                :class="'quantidade'+artigos.id"
                id="inputRef" ref="inputRef"
                :disabled="dados.estado != 'Cotação'"
                placeholder="Quantidade"/>

                <input placeholder="Preço" @click="SelectText(artigos.id,'custo')"
                @keyup.enter="UpdateItem(artigos)" v-model="artigos.custo"
                :class="'custo'+artigos.id"
                id="inputRef" ref="inputRef"
                :disabled="dados.estado != 'Cotação'"
                type="text"/>

                <input placeholder="Desconto" v-model="artigos.desconto"
                @click="SelectText(artigos.id,'desconto')"
                :class="'desconto'+artigos.id"
                 id="inputRef" ref="inputRef"
                :disabled="dados.estado != 'Cotação'"
                @keyup.enter="UpdateItem(artigos)" type="text"/>

                <input placeholder="Iva" v-model="artigos.iva"
                :class="'iva'+artigos.id"
                @click="SelectText(artigos.id,'iva')"
                :disabled="dados.estado != 'Cotação'"
                @keyup.enter="UpdateItem(artigos)" type="text"/>

                <input placeholder="Gastos" v-model="artigos.gasto"
                :class="'gasto'+artigos.id"
                @click="SelectText(artigos.id,'gasto')"
                :disabled="dados.estado != 'Cotação'"
                @keyup.enter="UpdateItem(artigos)" type="text"/>

                <input type="text" :value="formatDinheiro.format(artigos.preco_final)" required disabled/>

                <input type="text" :value="artigos.total" required disabled/>
                <div v-if="dados.estado =='Cotação'">
                    <Caixa class="icone" @click="Addcaixa(artigos)"/>
                    <i @click="DeleteItem(artigos.id)" class="fa fa-trash"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="ResultadoTotal border-top bg-white d-flex">
        <div class="w-50">
            <div v-if="dados.estado =='Cotação'">
                <input @click="BuscarProdutos('')" id="Produto"
                v-model="produto.nome"
                placeholder="Selectionar produto" @keyup="Pesquisar"
                class="w-75 p-1 border-bottom" />
                <div v-if="produto.estado" class=" text-secondary rounded-bottom bg-white ListaProdutos overflow-auto w-75 border border-top-0">
                    <div @click="SelecionarProduto(produto)" v-for="produto in produto.lista.slice(0,30)" :key="produto.id" id="ListaProdutos" class="d-flex">
                        {{produto.nome}}
                        <div v-if="produto.qtd <= 0"  class="StockEsgatado">
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
                    <div>{{formatDinheiro.format(TotalMercadoria())}}</div>
                    <div>{{formatDinheiro.format(Encomenda.encomenda.total_desconto)}}</div>
                    <div>{{formatDinheiro.format(Encomenda.encomenda.total_iva)}}</div>
                    <div>{{formatDinheiro.format(Encomenda.encomenda.total_gastos)}}</div>
                </div>
            </div>

            <div class="w-100 mt-3 d-flex border-top">
                <div class="TitlTotals">Total da fatura :</div>
                <div class="Totals">
                    {{formatDinheiro.format(Encomenda.encomenda.total)}}
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
import Product from '@/components/produtos/produto.vue'
import { useStore } from 'vuex';
import Caixa from 'vue-material-design-icons/PackageVariantClosedPlus.vue'
import { useCurrencyInput } from 'vue-currency-input';

const formatDinheiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'});

const { inputRef } = useCurrencyInput({currency: 'AOA' })

const store = useStore()

const emits = defineEmits(['message','progress','encomenda'])

const dados = computed(()=>{
    return store.state.EncomendaCompra
});

const Encomenda = ref({
  lista: [],
  encomenda: dados.value,
})

const TotalMercadoria = (()=>{
    return Number(Encomenda.value.encomenda.total) + Number(Encomenda.value.encomenda.total_desconto)
- Number(Encomenda.value.encomenda.total_iva)
- Number(Encomenda.value.encomenda.total_gastos)
})

const ModalProduct = ref({
    estado: false,
    produto: Object
})

const SelectText = ((id,tipo)=>{
    inputRef.value
    var classe = tipo+id
    document.querySelector('.'+classe).select();
})

const caixa = reactive({
    artigo: null,
    gasto: true,
    estado: false
})

const Addcaixa = ((artigo)=>{
    caixa.artigo = artigo
    caixa.estado = true
})

const produto = ref({
    nome:null,
    estado:false,
    lista: [],
    StorProduct: []
});

const message = ((message,tipo)=>{
     emits('message',message,tipo)
})

const OnMounted = onMounted(()=>{
    return AddProduto(`Encomenda/${dados.value.id}`,'')
})

const NewProduct = (()=>{
    if (produto.value.nome != '' && produto.value.nome != null) {
        AddProduto(`NewProduct/${produto.value.nome}`,dados.value.id)
    } else {
        emits('message','Não pode adicionar um produto com nome vazio ','info')
    }
})

const BuscarProdutos = (dados) => {
    emits('progress')
    axios.post('BuscarProdutus',{nome:dados}).then((response)=>{
        produto.value.lista = response.data
        produto.value.StorProduct = response.data
        produto.value.estado = true
        emits('progress')
    })
}
const SelecionarProduto = (event) => {
    const Verificar = Encomenda.value.lista.find(object => object.id_produto === event.id)
    if (!Verificar) {
        produto.value.estado = false
        event.idOrdem = dados.value.id
        AddProduto('AdicionarItemCompra',event)
    } else {
        message('Este produto ja encontra na encomenda ','info')
    }
}

const AddProduto = (rota,dados) => {
    ModalProduct.value.estado = false
    axios.post(`/${rota}`,{dados:dados}).then((Response)=>{
        if (Response.data.message) {
            message(Response.data.message,Response.data.tipo)
        } else {
            Encomenda.value = Response.data
            emits('encomenda',Encomenda.value.encomenda);
        }
    })
}

const UpdateItem = ((Item)=>{
    let id = Item.id
    Item.nome = undefined
    Item.idOrdem = undefined
    Item.id = undefined
    return AddProduto(`UpdateItems/${id}`,Item);
})

const DeleteItem = ((id)=>{
    return AddProduto(`DeleteItem/${id}`,dados.value.id)
})

const Pesquisar = ((event)=>{
    const filtrar = produto.value.StorProduct.filter(object=>{
        return object.nome.toLowerCase().includes(event.target.value.toLowerCase(),0)
    })
    if (filtrar.length != 0) {
        produto.value.lista = filtrar
    }else{
        produto.value.lista = []
    }
})

const BoxConfirm = ((dados)=>{
    const Rows = Encomenda.value.lista.filter(item=> item.id === dados.id)
    Rows[0].quantidade = dados.QuatidadeFinal
    Rows[0].custo = dados.PrecoUnidade
    Rows[0].gasto = dados.gasto
    caixa.estado = false
    return UpdateItem(Rows[0])
})

const CreateUpdateProduct = (()=>{
    axios.get(`/novo_produto`)
    .then((Response) => {
        ModalProduct.value.produto = Response.data.produto
        ModalProduct.value.estado = true
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
