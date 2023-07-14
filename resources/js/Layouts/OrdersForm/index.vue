<template>
   <div class="Principal">
        <div class="Header">
            <h1>Fatura</h1>
        </div>
        <div class="Content">
            <div class="drop-up">
                <button @click="stateDrop = !stateDrop" type="button" class="dropdown-toggle">Açao</button>
            </div>
            
            <div :class="stateDrop ? '' : 'hidden'" class="Botoes" >
                <div v-show="button.state" v-for="button,idx in general.buttons"
                :key="idx">
                    <button
                        v-if="button.name != 'Confirmar' || stateFormOrder == 'Cotação'"
                        @click="button.function"
                        :class="button.class ? 'discartar' : 'botoes'"
                        >
                    {{button.name}}
                    <i v-if="button.name ==  loading" class="fa fa-spinner fa-pulse fa-3x fa-fw edit-product"></i>
                    </button>
                </div>
            </div>
            <div class="Formulario">
                <div class="Form">
                <div class="HeaderFatura">
                    <div class="NumeroOrdem">
                        <strong>Orden / {{props.order.orderNumber}}</strong>
                    </div>
                    <div class="Informacao">
                        <div>
                            <div class="addItem">
                            <button @click="stateFormOrder == 'Cotação' ? openSelect() : ''" type="button" >
                            {{order.relations?.relation?.name != null ? order.relations?.relation?.name: order.relations?.placeholder}}
                            <i :class="Select.state ? 'fa fa-chevron-up' : 'fa fa-chevron-down'"></i>
                            </button>
                            <div v-show="Select.state" class="list-items">
                                <input
                                    type="text"
                                    autofocus
                                    @keyup="SearchRelation"
                                    class="form-control"
                                    placeholder="pesquisa..."
                                    />
                                <div @click="SelectText(item)" v-for="item in relations.list"
                                    :key="item.id">
                                    <span>{{item.name}} </span>
                                    <i v-if="stateSubmit == 'addRelation'+item.id" class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div>
                            <terme
                            @message="message"
                            :general="general"
                            :order="order"
                            />
                        </div>
                    </div>
                </div>
                <div class="FooterFatura">
                    <Items
                        @message="message"
                        :general="general"
                        :order="order"
                        />
                </div>
                </div>
            </div>
        </div>
      <Toast/>
   </div>
</template>

<script setup>
import axios from "axios";
import Toast from "primevue/toast";
import Items from './items.vue'
import terme from './termo.vue'
import { useToast } from "primevue/usetoast";
import { onMounted, ref, watch } from "vue";
import useEventsBus from "@/Eventbus";
const stateDrop = ref(false)
const toast = useToast()
const {bus} = useEventsBus()
const Select = ref({
    state:false
});

const relations = ref({
    store:[],
    list: []
})

const stateSubmit = ref(null)

const props = defineProps({
    general:Object,
    order: Object,
    loading:String
})

const stateFormOrder = ref(props.order.state)

watch(()=>bus.value.get('stateFormOrder'),(payload)=>{
    stateFormOrder.value = payload
})

const openSelect = ((idx)=>{
    relations.value.store = props.order.relations.data
    relations.value.list = props.order.relations.data
    if (Select.value.state == true) return Select.value.state = false
    Select.value.state = true
})
const SelectText = ((item)=>{
    stateSubmit.value = 'addRelation'+item.id
    axios.post(`${props.general.routes.AddRelation.name}/${props.order.id}/${item.id}`)
    .then((response) => {
        props.order.relations.relation = response.data
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        Select.value.state = null
        stateSubmit.value = null
    });
})

const SearchRelation = ((event)=>{
    relations.value.list = relations.value.store.filter((item)=>{
        return String(item.name).toLocaleLowerCase().includes(event.target.value)
    })
})

const message = ((message,type)=>{
    toast.add({
        severity: type,
        summary: 'Informação do sistema',
        detail: message,
        life: 5000,
    })
})
</script>
<style scoped lang="scss">
@include formOrders;
</style>
