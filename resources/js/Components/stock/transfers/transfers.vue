<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <span>
               <h2>Transferencias</h2>
            </span>
            <div class="buttons">
               <button @click="newTransfer">
                  <span>Transferir Produtos</span>
                  <FontAwesomeIcon icon="fa-solid fa-right-long" />
               </button>
            </div>
         </div>
         <div class="Header-right">
            <span>
                <input @keyup="(e)=>searchTransfer(e.target.value)" type="text" placeholder="pesquisar">
            </span>
            <pagination v-if="loading != 'start'" @page="getPage" :object="transfers.list"/>
         </div>
      </div>
      <div class="Container">
         <div class="list">
            <div class="Title">
               <div>Numero de pedido</div>
               <div>Responsavel</div>
               <div>Armagen de destino</div>
               <div>Data da transferencia</div>
               <div>Total</div>
               <div>Estado</div>
            </div>
            <div class="list_items">
               <div @click="showTransfer(item)" v-for="item in transfers.list.data" :key="item.id" class="rows">
                  <div>{{item.orderNumber}}</div>
                  <div>{{item.user.surname}}</div>
                  <div>{{item.store_to?.name}}</div>
                  <div>{{FormatDate(item.created_at)}}</div>
                  <div>{{formatDinheiro.format(item.total)}}</div>
                  <div :class="item.state">
                     <span>{{item.state}}</span>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</template>

<script setup>
import pagination from '@/Layouts/paginations/paginate.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { onMounted, ref } from '@vue/runtime-core';
import axios from 'axios';
const formatDinheiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'});
import moment from 'moment'
const emits = defineEmits(['handleModule','message'])
const transfers = ref({
    list: [],
    storeList: [],
})
const loading = ref(null)
onMounted(async()=>{
    await getTransfers()
})
const getPage = ((data)=>{
    transfers.value.list = data
    transfers.value.storeList = data.data
})
const getTransfers = (async()=>{
    loading.value = 'start'
   await axios.get('getTransfers')
    .then((response) => {
        transfers.value.list = response.data
        transfers.value.storeList = response.data.data
    }).catch((err) => {
        console.log(err);
    }).finally(()=>{
        loading.value = null
    });
})

const showTransfer = ((transfer)=>{
    emits('handleModule','transfer',transfer)
})

const FormatDate = ((data) =>{
    return moment(data).format('DD-MM-YYYY')
})

const searchTransfer = ((e)=>{
    const filter = transfers.value.storeList.filter((item)=>{
        return String(item.user.surname).toLowerCase().includes(String(e).toLowerCase())
        || String(item.orderNumber).toLowerCase().includes(String(e).toLowerCase())
        || String(item.total).includes(String(e))
        || String(item.store_to?.name).toLowerCase().includes(String(e).toLowerCase())
    })
    transfers.value.list.data = filter
})

const newTransfer = (()=>{
    axios.post('newTransfer')
    .then((response) => {
        emits('handleModule','transfer',response.data)
    }).catch((err) => {
        console.log(err);
    });
})
</script>

<style scoped lang="scss">
@include components;
@mixin stateTransfer($bgColor,$color){
    span{
        width: 80px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: $bgColor;
        border-radius: 5px;
        color: $color;
    }
}
.list{
    @include form_lists;
    .list_items{
        .Cotação{
            @include stateTransfer(rgba(40, 167, 69, 0.5), rgb(1, 48, 12))
        }
        .Annulado{
            @include stateTransfer(rgba(255, 0, 0, 0.232), red)
        }
        .Confirmado{
            @include stateTransfer(#a3498b8b, #340126)
        }
    }
}
.buttons{
    display: flex;
    gap: .5rem;
    .descartar{
        @include botao_descartar;
    }
    button{
        display: flex;
        align-items: center;
        position: relative;
        box-sizing: border-box;
        width: 180px;
        >svg{
            position: absolute;
            font-size: 20px;
            right: 0px;
            animation: icon 1s linear infinite;
        }
        @keyframes icon {
            0%{
                right: 15px;
            }
            25%{
                right: 13px;
            }
            50%{
                right: 12px;
            }
            75%{
                right: 13px;
            }
            100%{
                right: 15px;
            }
        }
    }
}
</style>
