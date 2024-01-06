<template>
<div class="principal">
    <div class="Header">
        <div class="Header-left">
            <span>
                <h2>{{$t('words.payment')}}</h2>
            </span>
        </div>
        <div class="Header-right">

        </div>
    </div>
    <div class="Container">
        <div class="Title">
            <div>{{$t('words.order')}}</div>
            <div>{{$t('words.user')}}</div>
            <div>{{$t('words.client')}}</div>
            <div>{{$t('words.date')}}</div>
            <div class="TotalOrden">{{$t('words.payment')}}</div>
            <div class="TotalOrden">{{$t('phrases.restToPay')}}</div>
        </div>
        <div class="list_items">
            <div v-for="method in methods.list" :key="method.id" class="items">

                <span @click="ShowOrder(method)">
                    <div>{{method.method_translate[0].translate}}</div>
                    <div>{{method.payments.length + ` Pagamento${method.payments.length >1 ? "s" : ""}`}}</div>
                </span>

                <div v-if="OrdersMethod == method.id" class="operations">
                    <span v-for="item in method.payments" :key="item.id" class="items">
                        <div>{{item.invoice?.orderNumber+item.id}}</div>
                        <div>{{item.invoice?.user.surname}}</div>
                        <div>{{item.invoice?.client?.surname}}</div>
                        <div>{{formatDate(item.created_at)}}</div>
                        <div class="TotalOrden">{{formatMoney(item.Amount)}}</div>
                        <div class="TotalOrden">{{formatMoney(item.TotalPayments)}}</div>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { onMounted, ref } from "vue";
const methods = ref({
    list:[],
    store: []
})

const OrdersMethod = ref(null)

onMounted(async ()=>{
    await getClients()
})

async function getClients() {
    await axios.get('/getPayments')
    .then((response) => {
        methods.value.list = response.data
        methods.value.store = response.data
    }).catch((err) => {
        console.log(err);
    });
}

const ShowOrder = ((client)=>{
    if (OrdersMethod.value != null && OrdersMethod.value == client.id) return OrdersMethod.value = null;
    return OrdersMethod.value = client.id;
})
</script>

<style scoped lang="scss">
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
                .TotalOrden{
                    display: flex;
                }
            }
        }
    }
}
</style>
