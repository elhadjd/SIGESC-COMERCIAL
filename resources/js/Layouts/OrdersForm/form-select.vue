<template>
<div class="addItem">
    <button @click="getProducts" type="button" >
        {{$t('words.article')}}s
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
            <button @click="$emit('newProduct',textSearch,'create')">{{$t('words.create')}} {{textSearch}}</button>
            <button @click="$emit('newProduct',textSearch,'createUpdate')">{{$t('words.create')}} e edit {{textSearch}}</button>
        </section>
    </div>
</div>
</template>

<script setup>
import { serviceMessage } from "@/composable/public/messages";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const textSearch = ref(null)
const {t} = useI18n()
const props = defineProps({
    general:Object,
    order:Object
})
const stateSubmit = ref(false)
const emit = defineEmits(['addItem','message','newProduct'])
const {showMessage} = serviceMessage()
const products = ref({
    state: false,
    list: [],
    store: [],
    firstList: []
})

async function getProducts() {
    if (stateSubmit.value || products.value.state) return products.value.state = false
    stateSubmit.value = true
    axios.get(`${props.general.routes.products.name}/200/1`)
    .then((response) => {
        products.value.list = response.data.data
        products.value.store = response.data.data
        products.value.firstList = response.data.data
        products.value.state = true
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        stateSubmit.value = false
    });
}

const SearchProduct = (async(event)=>{
    if(event.target.value == null || event.target.value == ''){
        products.value.list = products.value.firstList
        products.value.store = products.value.firstList
        return
    }
    textSearch.value = event.target.value
    const FilterSearch = products.value.store.filter(object=>{
        return String(object.nome).toLowerCase().includes(event.target.value.toLowerCase())
    })
    if(!FilterSearch.length) {
        return await searchDataToApi(event)
    }
    products.value.list = FilterSearch
})

const searchDataToApi = (async(event)=>{
    stateSubmit.value = true
    await axios.get(`/search/produtos/nome/${event.target.value}`)
    .then((response) => {
        if(!response.data.length) return showMessage(t('message.notData',{colum: t('words.article'),value: t('words.name')}),'info')
        products.value.list = response.data
        products.value.store = response.data
    }).catch((err) => {
        if(err.response.data.message) showMessage(err.response.data.message,'error')
        console.error(err);
    }).finally(()=>{
        stateSubmit.value = false
    });
})

</script>

<style scoped lang="scss">
@include form-select;
</style>
