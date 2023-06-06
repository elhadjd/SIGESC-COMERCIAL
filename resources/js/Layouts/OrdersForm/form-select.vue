<template>
<div class="addItem">
    <button @click="getProducts" type="button" >
        Selecionar produtos
        <i :class="stateSubmit ? 'fa fa-spinner fa-pulse fa-3x fa-fw' : products.state ? 'fa fa-chevron-up' : 'fa fa-chevron-down'"></i>
    </button>
    <div v-show="products.state" class="list-items">
        <input
        type="text"
        autofocus
        v-model="textSearch"
        @keyup="SearchProduct"
        class="form-control"
        placeholder="pesquisa..."
        />
        <div v-for="product in products.list" @click="$emit('addItem',product)" :key="product.id" >
            <span>{{ product.nome }}</span>
            <span>{{formatMoney(general.form.totalOrder.total.title == 'Total da transferencia' ? product.preçocust: product.preçovenda)}}</span>
            <slot></slot>
        </div>
        <section class="newItem">
            <button @click="$emit('newProduct',textSearch,'create')">Criar {{textSearch}}</button>
            <button @click="$emit('newProduct',textSearch,'createUpdate')">Criar e editar {{textSearch}}</button>
        </section>
    </div>
</div>
</template>

<script setup>
import { ref } from "vue";

const textSearch = ref(null)

const props = defineProps({
    general:Object,
    order:Object
})
const stateSubmit = ref(false)
const emit = defineEmits(['addItem','message','newProduct'])

const products = ref({
    state: false,
    list: [],
    store: []
})

async function getProducts() {
    if (stateSubmit.value || products.value.state) return products.value.state = false
    stateSubmit.value = true
    axios.get(`${props.general.routes.products.name}/1500/1`)
    .then((response) => {
        products.value.list = response.data.data
        products.value.store = response.data.data
        products.value.state = true
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        stateSubmit.value = false
    });
}

const SearchProduct = ((event)=>{
    textSearch.value = event.target.value
    const FilterSearch = products.value.store.filter(object=>{
        return String(object.nome).toLowerCase().includes(event.target.value.toLowerCase())
    })
    products.value.list = FilterSearch
})

</script>

<style scoped lang="scss">
@include form-select;
</style>
