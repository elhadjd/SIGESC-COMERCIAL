<template>
    <div class="principal">
        <div class="header">

            <div>
                <h1>Fornecedores</h1>
                <div class="buttons" v-if="estadoFormulario">
                    <button @click="GuardarFornecedor">Guardar</button>
                    <button @click="Descartar">Descartar</button>
                </div>
                <button @click="NovoFornecedor" v-else>Adicionar fornecedore</button>
            </div>

            <div>
                <input type="text" @keyup="PesquisarFornecedor" placeholder="pesquisa aqui..." />
            </div>
        </div>

        <div class="Container">
            <NovoFornecedore v-if="estadoFormulario" @voltar="Descartar" :Dados="Dados"/>

            <div v-else class="Clientes">
                <label @click="mostrarDetalhesCompleto(listaFornecedor)"
                    class="clientes"
                    v-for="listaFornecedor in listaFornecedores"
                    :key="listaFornecedor.id">
                    <div class="FormClient">
                        <div>
                            <img :src="'Clientes/image/'+listaFornecedor.imagem" />
                        </div>

                        <div>
                            <strong>{{ listaFornecedor.nome }}</strong>
                            <p>{{ listaFornecedor.email }}</p>
                            <p>{{ listaFornecedor.telefone }}</p>
                        </div>
                    </div>

                </label>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref, reactive, watch, computed } from "vue";
import NovoFornecedore from '@/components/suppliers/NovoFornecedor.vue'
import { useStore } from "vuex";

const store = useStore()

const Dados = ref(null)



const ListaPais = ref(false);

const estadoFormulario = ref(false);

const listaFornecedores = ref([]);

const lista = ref([]);

const Descartar = (()=>{
    estadoFormulario.value = false
    onMountede()
})

const NovoFornecedor = (()=>{
    Dados.value = null
    estadoFormulario.value = true
})


const onMountede = onMounted(async () => {
  await axios
    .get("/fornecedores")
    .then((response) => {
        listaFornecedores.value = response.data;
    })
    .catch((erro) => {
      console.log(erro);
    });
});

const mostrarDetalhesCompleto = (event) => {
    Dados.value = event
    estadoFormulario.value = true
    store.state.GuardarCliente = false
};

const Guardar = computed(()=>{
    return store.state.GuardarCliente
})

const PesquisarFornecedor = ((event)=>{
    if (event.target.value.length === 2) {
        axios.get("/fornecedores",{lista:event.target.value})
        .then((Response) => {
            listaFornecedores.value = Response.data;
            lista.value = Response.data
        })
        .catch((err)=>{
            console.log(err);
        })
    }else if(event.target.value.length >= 3){
        var liste = lista.value
        const NovaLista = liste.filter(Fornecedor=>{
            return Fornecedor.nome.includes(event.target.value)
        })
        listaFornecedores.value = NovaLista
    }else if(event.target.value.length == 0){
        return onMountede()
    }
})

const GuardarFornecedor = (()=>{
    store.state.GuardarCliente = true
})

watch(Guardar, (novo)=>{
    if (novo == false) {
        onMountede()
        estadoFormulario.value = false
    }
})
</script>

<style scoped lang="scss">
@import "../../../assets/Clientes/css/index";
</style>
