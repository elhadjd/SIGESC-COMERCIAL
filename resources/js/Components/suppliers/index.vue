<template>
    <div class="principal">
        <div class="header">

            <div>
                <h1>Fornecedores</h1>
                <div class="buttons" v-if="suppliers.state">
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
            <new-supplier v-if="suppliers.state" @voltar="Descartar" :supplier="supplier"/>

            <div v-else class="Clientes">
                <label @click="showSupplier(supplier)"
                    class="clientes"
                    v-for="supplier in suppliers.list"
                    :key="supplier.id">
                    <div class="FormClient">
                        <div>
                            <img :src="'Clientes/image/'+supplier.image" />
                        </div>

                        <div>
                            <strong>{{ supplier.name }}</strong>
                            <p>{{ supplier.email }}</p>
                            <p>{{ supplier.phone }}</p>
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
import newSupplier from '@/components/suppliers/NovoFornecedor.vue'
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
    onMountede()
})

const NovoFornecedor = (()=>{
    supplier.value = []
    suppliers.value.state = true
})


const onMountede = onMounted(async () => {
  await axios
    .get("/supplier")
    .then((response) => {
        suppliers.value.list = response.data;
        suppliers.value.store = response.data;
    })
    .catch((erro) => {
      console.log(erro);
    });
});

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
        return onMountede()
    }
})

const GuardarFornecedor = (()=>{
    store.state.GuardarCliente = true
})

watch(Guardar, (novo)=>{
    if (novo == false) {
        onMountede()
        suppliers.value.state = false
    }
})
</script>

<style scoped lang="scss">
@import "../../../assets/Clientes/css/index";
</style>
