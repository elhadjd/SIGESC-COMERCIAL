<template>
<div class="principal">
    <div class="Header">
        <div class="Header-left">
            <h2>Historico de login</h2>
        </div>
        <div class="Header-right">
            <span>
                <input type="text" @keyup="search" :placeholder="$t('words.search')">
            </span>
        </div>
    </div>
    <div class="Container">
        <div class="Title">
            <div>Date</div>
            <div>User</div>
            <div>Adress IP</div>
            <div>Browser used</div>
        </div>
        <div class="list_items">
            <div v-for="item in data.list" :key="item.id">
                <div @click="openList(item.id)" class="rows">
                    <div>{{formatDate(item.created_at)}}</div>
                    <div>{{item.user.name}}</div>
                    <div>{{item.ip_address}}</div>
                    <div>{{item.browser}}</div>
                </div>
                <span class="operations" v-if="stateList == item.id">
                    <div v-for="activity in item.activities" :key="activity.id">
                        <span>{{formatHourAndTime(activity.created_at)}}</span>
                        <span>{{activity.body}}</span>
                    </div>
                </span>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import axios from "axios";
import moment from "moment";
import { onMounted, ref } from "vue";
const data = ref({
    list: [],
    store: []
})

const emits = defineEmits(['message'])
const stateList = ref(null)
onMounted(()=>{
    getData()
})

async function getData() {
    axios.get('getLoginRegister')
    .then((response) => {
        data.value.list = response.data
        data.value.store = response.data
    }).catch((err) => {
        emits('message',err.response.data,'error')
        console.log(err);
    });
}

const openList = ((id)=>{
    if (stateList.value == id) return stateList.value = null
    stateList.value = id
})

const search =((e)=>{
    const filter = data.value.store.filter((item)=>{
        return String(moment(item.created_at).format("DD-MM-YYYY")).includes(e.target.value)
        || String(item.user.name).toLocaleLowerCase().includes(e.target.value.toLocaleLowerCase())
        || String(item.browser).includes(e.target.value)
    })
    data.value.list = filter
})
</script>

<style lang="scss" scoped>
@include components;
.Container{
    overflow-x: auto;
    @include form_lists;
    .Title,
    .list_items{
        min-width: 1000px;
    }
    .rows{
        >div{
            padding: 10px !important;
        }
    }
    .operations{
        border-bottom: 1px solid #00000010;
        background-color: white;
        height: auto;
        max-height: 100px !important;
        overflow-y: auto;
        @include scroll;
        &:hover{
                background-color: transparent;
        }
        >div{
            cursor: pointer;
            width: 100%;
            display: flex;
            flex-direction: row;
            padding: 3px 20px 3px 20px;

            &:nth-child(even){
                background: #dddddd8c;
            }
            >span{
                width: 100%;
                font-size: 14px;
            }
            .TotalOrden{
                display: flex;
            }
        }
    }
}
</style>
