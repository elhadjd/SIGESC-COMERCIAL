<template>
    <div class="principal">
        <div class="Header">
            <div class="Header-left">
                <span>
                    <h2>{{$t('words.credit')}}</h2>
                </span>
            </div>
            <div class="Header-right">
                <span class="p-input-icon-right w-100">
                    <i class="fa fa-search"></i>
                    <input @keyup="SearchInvoice" type="text" :placeholder="$t('words.search')">
                </span>
            </div>
        </div>
        <div class="Container">
            <div class="Title">
                <div>{{$t('words.client')}}</div>
                <div>{{$t('words.phone')}}</div>
                <div>{{$t('words.city')}}</div>
                <div>Email</div>
                <div class="TotalOrden">{{$t('words.total')}}</div>
                <div class="TotalOrden">{{$t('phrases.restToPay')}}</div>
            </div>
            <div class="list_items">
                <div v-for="client in clients.list" :key="client.id" class="items">
                    
                    <span @click="ShowOrder(client)">
                        <div>{{client.surname}}</div>
                        <div>{{client.phone}}</div>
                        <div>{{clientcity}}</div>
                        <div>{{client.email}}</div>
                        <div class="TotalOrden">{{formatMoney(client.invoices_sum_total_invoice)}}</div>
                        <div class="dropdown-toggle TotalOrden">{{formatMoney(client.invoices_sum_rest_payable)}}</div>
                    </span>
                    
                    <div v-if="OrdersClient == client.id" class="operations">
                        <span @click="showInvoice(item)" v-for="item in client.invoices" :key="item.id" class="items">
                            <div>{{item.orderNumber+item.id}}</div>
                            <div>{{formatDate(item.DateOrder)}}</div>
                            <div>{{formatDate(item.DateDue)}}</div>
                            <div>{{formatMoney(item.TotalInvoice)}}</div>
                            <div>{{formatMoney(item.RestPayable)}}</div>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";

const clients = ref({
    list:[],
    store:[]
});
const emits = defineEmits(['fatura'])
const OrdersClient = ref(null)

onMounted(async ()=>{
    await getClients()
})

async function getClients() {
    await axios.get('/clients/credit')
    .then((response) => {
        clients.value.list = response.data
        clients.value.store = response.data
    }).catch((err) => {
        console.log(err);
    });
}

const ShowOrder = ((client)=>{
    if (OrdersClient.value != null && OrdersClient.value == client.id) return OrdersClient.value = null;
    return OrdersClient.value = client.id;
})

function showInvoice(invoices) {
   emits('fatura', invoices);
}

const SearchInvoice = ((event)=>{
    const filter = clients.value.store.filter((client)=>{
        return String(client.surname).toLocaleLowerCase().includes(event.target.value)
    })
    clients.value.list = filter

})
</script>

<style lang="scss" scoped>
@include components;
.Container{
    @include form_lists;
    .items{
        -webkit-user-select: none;
        width: 100%;
        border-bottom: 1.5px solid #00000010;
        >span{
            display: flex;
            flex-direction: row;
            align-items: center;
            width: 100% !important;
            >div{
                display: flex;
                width: 100%;
                align-items: center;
            }
            &:hover{
                cursor: pointer;
                background-color: #0000001c;
            }
        }

        .operations{
            border-bottom: 1px solid #00000010;
            background-color: white;
            height: auto;
            max-height: 100px;
            overflow-y: auto;
            @include scroll;
            >span{
                width: 100%;
                display: flex;
                flex-direction: row;
                padding: 3px 20px 3px 20px;
                >div{
                    width: 100%;
                    font-size: 14px;
                    
                }
            }
        }
    }
}
</style>
