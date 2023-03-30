<template>
<Progress v-if="process"/>
<Pagamento :FormularioPagamentoCompra="FormularioPagamentoCompra"
@fechar="pagamento.estado = false" @encomenda="Encomenda" @message="message" v-if="pagamento.estado"/>
  <FaturaCompra v-if="StateFatura" @FecharModal="StateFatura = false"
  :encomenda="encomenda"/>
  <div class="Principal">
    <div class="Header">
      <h1>Compras</h1>
    </div>
    <div class="Container">
      <div class="Botoes">
        <button v-if="encomenda.estado == 'Cotação'" @click="Confirmar" class="botoes">Confirmar</button>
        <button v-if="encomenda.estado == 'Cotação'" @click="$emit('fechar')" class="botoes">Fechar</button>
        <button @click="pagamento.estado = true" v-if="encomenda.estado != 'Pago'" class="botoes">Adicionar pagamento</button>
        <button @click="Imprimir" v-if="encomenda.estado != 'Cotação'" class="botoes">Imprimir fatura</button>
      </div>
      <div class="Formulario">
        <div class="Form">
          <div class="HeaderFatura">
            <div class="NumeroOrdem">
              <h1 class="h1">Compra/00{{encomenda.id}}</h1>
            </div>
            <div class="Informacao">
                <div>
                    <div class="cliente">
                        <div>
                            <input :disabled="encomenda.estado !='Cotação'"
                            @click="BuscarListaFornecedores"
                            type="text" placeholder="Selecionar Fornecedor"
                            @keyup="PesquisarFornecedor"
                            v-model="encomenda.empresa"/>
                        </div>

                        <div v-if="fornecedor.estado" class="shadow ListCliente">
                            <div class="clienteRows" v-for="fornecedor in fornecedor.lista" :key="fornecedor.id"
                            @click="SelecionarFornecedor(fornecedor)">
                                <div>{{fornecedor.empresa}}</div>
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <DataEncomenda/>
                </div>
            </div>
          </div>
          <div class="FooterFatura">
            <PedidoCompra @encomenda="Encomenda" @message="message"
            @progress="process = !process"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import PedidoCompra from '../Compras/PedidoCompra.vue'
import DataEncomenda from '../Compras/DataEncomenda.vue'
import { computed, onMounted,reactive,ref, watch } from '@vue/runtime-core'
import Progress from '../confirmation/progress.vue'
import axios from 'axios'
import Pagamento from '@/components/pagamentos/index.vue'
import FaturaCompra from '@/components/Compras/FaturaCompra.vue'
import { useStore } from 'vuex'

const store = useStore()

const emits = defineEmits(['message'])

const process = ref(false)

const StateFatura = ref(false)

const fornecedor = reactive({
    placeholder:null,
    estado:false,
    lista:[],
    StoreFornecedor: []
});

const encomenda = ref(store.state.EncomendaCompra)

const Encomenda = (payload) => {
    axios.get(`Encomenda/${encomenda.value.id}`)
    .then((Response) => {
        encomenda.value = Response.data.encomenda
        // FormularioPagamentoCompra.encomenda = Response.data.encomenda
        // FormularioPagamentoCompra.valor = Response.data.encomenda.total
    }).catch((err) => {
        console.log(err);
    });
}

const FormularioPagamentoCompra = reactive({
    encomenda: encomenda.value,
    valor:encomenda.value.total,
    metodo:[],
    data:null,
    fornecedor:encomenda.value.empresa,
    caminho: 'AdicionarPagamentoCompra',
    CalcularTotalAPagar: 'PagamentosCompra'

});

const pagamento = reactive({
    estado:false
});

const Confirmar = (()=>{
    if (encomenda.value.id_fornecedor != null || encomenda.value.id_fornecedor != '') {
        process.value = true
        axios.post(`ConfirmarEncomenda/${encomenda.value.id}`)
        .then((Response) => {
            if (!Response.data.message) {
                encomenda.value = Response.data
            } else {
                emits('message',Response.data.message,Response.data.tipo)
            }
            process.value = false
        }).catch((err) => {
            process.value = false
            emits('message','Aconteçeu um erro no sistema por favor atualize seu navigador e tenta novamente','info')
            console.log(err);
        });
    } else {
        emits('message','Fornecedor não pode ficar vazio','info')
    }
})

const BuscarListaFornecedores = () => {
    axios.get('fornecedores?lista=toda').then((response)=>{
        fornecedor.lista = response.data
        fornecedor.StoreFornecedor = response.data
        fornecedor.estado = true
    });
}

const Imprimir = (()=>{
    StateFatura.value = true
})

const PesquisarFornecedor = ((event)=>{
    const Filtrar = fornecedor.StoreFornecedor.filter(object=>{
        return object.empresa.toLowerCase().includes (event.target.value.toLowerCase(),0)
    })
    if (Filtrar.length != 0) {
        fornecedor.lista = Filtrar
    } else {
        return message('Nenhum fornecedor encontrado com este nome ','info')
    }

})

const message = ((message,tipo)=>{
    emits('message',message,tipo)
})

const SelecionarFornecedor = (event) => {
    axios.post(`/UpdateItems/${encomenda.value.id}`,{id_fornecedor: event.id})
    .then((Response) => {
        fornecedor.estado = false
        store.state.EncomendaCompra = Response.data.encomenda
    }).catch((err) => {
        console.log(err);
    });
}
</script>

<style scoped lang="scss">
@import "../../../assets/faturacao/css/CriarFaturas.scss";
.NumeroOrdem {
  height: 40% !important;
}
.Informacao {
  height: 60% !important;
  .cliente {
    margin-bottom: 10px;
  }
}
</style>
