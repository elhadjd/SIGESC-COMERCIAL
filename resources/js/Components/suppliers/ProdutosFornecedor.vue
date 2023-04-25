<template>
<Confirmation v-if="Confirm.estado" @Confirme="EmitConfirme" @descartou="Confirm.estado = false" :SmsConfirm="Confirm.sms"/>
<div class="Principal">
    <div class="Header">
        <div class="HeaderLeft">
            <header>Pesquisar produtos</header>
            <span class="p-input-icon-right">
                <i class="pi pi-search" />
                <input type="text" @keyup="Pesquisar" v-model="form.nome" placeholder="Pesquisar...">
            </span>
        </div>
        <div class="Muvementos">
            <header>Mudar preço</header>
            <div class="InputMudarPreco">
                <input type="text" v-model="percentagem" placeholder="Informe percentagem para mudar preços">
                <button @click="ConfirmarMudar">Confirmar</button>
            </div>
        </div>
    </div>
    <div class="Container">
        <label @click="MostrarProd(item)" v-for="item in ListProdust" :key="item.id" class="mx-1 mb-2 bloco_artigo" id="bloco_artigo">
            <div class="d-flex">
                <div id="div_da_imagem"><img :src="'/produtos/image/'+item.image" class="rounded float-right"></div>
                <div class="div_preco_qtd text-secondary">
                    <div class="NomeProduto"><strong>{{item.nome}}</strong></div>
                    <div class="d-flex">
                        <div class="preco_qtd">
                            <div><strong>Preço :</strong> {{FormetDineiro.format(item.preçovenda)}} </div>
                            <div><strong>Stock :</strong> {{item.stock_sum_quantity+',00Un(s)'}}</div>
                        </div>
                    </div>
                </div>
                <div><i :class="props.IdFornecedor == item.fornecedore_id ? 'fa fa-check text-warning' : 'fa fa-plus text-warning '"></i></div>
            </div>
        </label>
    </div>
    <div class="Footer">
        <button @click="$emit('Fechar')">Fechar</button>
    </div>
</div>
</template>

<script setup>
import Confirmation from '../confirmation/index.vue'
import { onMounted, ref,reactive } from "@vue/runtime-core";
import axios from "axios";
import Produtos from '../products/products.vue'
const StringPesquisa = ref()

const FormetDineiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})

const ListProdust = ref([])
const emits = defineEmits('Fechar');

const ArtigosGuardados = ref()

const percentagem = ref()

const Confirm = ref({
    estado: false,
    sms: null,
})

const form = reactive({
    colun1: 'nome',
    colun2: 'preçovenda',
    table: 'produtos',
    nome: null,
    estado: 0
})

const ConfirmarMudar = (()=>{
    if (percentagem != null || percentagem != '') {
        axios.post(`/MudaPrecos/${props.IdFornecedor}`,{percentagem: percentagem.value})
        .then((Response) => {
            ListProdust.value = Response.data.produtos
        }).catch((err) => {
            console.log(err);
        });
    } else {

    }
})

const produto = ref()

const props = defineProps({IdFornecedor: Number})

const EmitConfirme = (()=>{
    return EnviarProduto(produto.value)
})

const OnMounted = onMounted(()=>{
    axios.get(`/Stock/getProductAgroup/supplier/${props.IdFornecedor}`)
    .then((Response) => {
        ListProdust.value = Response.data.products
    }).catch((err) => {
        console.log(err);
    });
})

const MostrarProd = (async (event)=>{
    produto.value = event
    if (event.fornecedores_id != null) {
        if (event.fornecedores_id == props.IdFornecedor) {
            Confirm.value.sms = 'desativar este produto deste fornecedor';
            Confirm.value.estado = true
        }else{
            Confirm.value.sms = 'Mudar de fornecedor deste produto';
            Confirm.value.estado = true
        }
    } else {
        return EnviarProduto(event)
    }
})

const EnviarProduto = ((event)=>{
    axios.post(`AddProdutoFornecedor/${event.id}/${props.IdFornecedor}`)
    .then((Response) => {
        ListProdust.value = Response.data.products
        Confirm.value.estado = false
    }).catch((err) => {
        console.log(err);
    });
})

const Pesquisar = (()=>{
    // Verificar se o usuario digitiou 2 letras
    if (form.nome.length ==2) {
        axios.post('/PesquisarProduto',form).
        then((Response)=>{
            ListProdust.value  = null;
            ListProdust.value   = Response.data.resultado;
            ArtigosGuardados.value = Response.data.resultado
        })
    } else if((form.nome.length>=3)&&(ArtigosGuardados.value != null )) {
        var test = ArtigosGuardados.value
        // a filtra a veriavel resultado
        const teste = test.filter(objet=>{
            // a verificar o tipo de resultado para exebir
            if (Number(form.nome)) {
                return objet.precoVenda.includes(form.nome);
            } else {
                return objet.nome.includes(form.nome);
            }
        })
        ListProdust.value  = teste;
    } else if(form.nome.length ==0) {
        axios.get('/ProdutosMod')
        .then((Response) => {
        ListProdust.value = Response.data.produtoss.data;
        }).catch((err) => {
            console.log(err);
        });
    }else{
        return false
    }
})

</script>

<style scoped lang="scss">
@import '../../../assets/fornecedor/scss/produtos';
</style>
