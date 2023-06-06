<template>
    <div class="principal">
        <div class="Header">

            <div class="Header-left">
                <h2>Fornecedores</h2>
                <div class="buttons" v-if="suppliers.state">
                    <button @click="GuardarFornecedor">Guardar</button>
                    <button @click="Descartar">Descartar</button>
                </div>
                <button @click="NewSupplier" v-else>Adicionar fornecedore</button>
            </div>

            <div v-if="!suppliers.state" class="Header-right">
                <span>
                    <input type="text" @keyup="PesquisarFornecedor" placeholder="pesquisa aqui..." />
                </span>
            </div>
        </div>

        <div class="Container">

            <new-supplier v-if="suppliers.state" @voltar="Descartar" :supplier="supplier"/>

            <div v-else class="Clientes">
                <label
                    class="clientes"
                    v-for="supplier in suppliers.list"
                    :key="supplier.id" @click="showSupplier(supplier)">
                    <div class="FormClient">
                        <div>
                            <img :src="'/supplier/image/'+supplier.image" />
                        </div>

                        <div>
                            <span>{{ supplier.name }}</span>
                            <span>{{ supplier.email }}</span>
                            <span>{{ supplier.phone }}</span>
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
import newSupplier from './newSupplier.vue'
import { useStore } from "vuex";

const store = useStore()

const supplier = ref([])
const suppliers = ref({
    list:[],
    store:[],
    state:false
});

const lista = ref([]);

const Descartar = (()=>{
    suppliers.value.state = false
    getSuppliers()
})

const NewSupplier = (()=>{
    axios.post('/NewSupplier')
    .then((response) => {
        showSupplier(response.data)
    }).catch((err) => {
        console.log(err);
    });
})


onMounted(async () => {
 getSuppliers()
});

const getSuppliers = (async()=>{
     await axios
    .get("/suppliers")
    .then((response) => {
        suppliers.value.list = response.data;
        suppliers.value.store = response.data;
    })
    .catch((erro) => {
      console.log(erro);
    });
})

const showSupplier = (event) => {
    supplier.value = event
    suppliers.value.state = true
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
        return getSuppliers()
    }
})

const GuardarFornecedor = (()=>{
    store.state.GuardarCliente = true
})

watch(Guardar, (novo)=>{
    if (novo == false) {
        getSuppliers()
        suppliers.value.state = false
    }
})
</script>

<style scoped lang="scss">
@import "../../../assets/Clientes/css/index";
</style>
