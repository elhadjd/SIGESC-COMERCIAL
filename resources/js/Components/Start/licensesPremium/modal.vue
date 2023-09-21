<template>
    <div class="Container">
        <plans :totals="totals" @addPlan="addPlan" v-if="plansState"/>
        <div v-else class="apps">
            <div class="app" v-for="app in apps" :key="app.id">
                <span @click="addApp(app)">
                    <font-awesome-icon  v-if="checkApp(app.id)" icon="fa-solid fa-check" />
                    <font-awesome-icon class="add" v-else icon="fa-solid fa-plus" />
                    <div class="price">{{formatMoney(app.price)}}</div>
                    <img :src="`https://bosgc.sisgesc.net/app/image/${app.image}`">
                    <div class="appName">{{app.label}}</div>
                </span>
            </div>
        </div>
        <div class="amount">
            <div>
                <div>
                    <label>Total</label>
                    <strong>{{formatMoney(totals.total + totals.discount)}}</strong>
                </div>
                <div>
                    <label>Disconto</label>
                    <strong>{{formatMoney(totals.discount)}}</strong>
                </div>
                <div>
                    <label>A pagar</label>
                    <strong>{{formatMoney(totals.total)}}</strong>
                </div>
            </div>
            <div>
                <font-awesome-icon @click="plansState = !plansState" :icon="`fa-solid ${!plansState ? 'fa-forward':'fa-left-long'}`" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import plans from './plans.vue'
import axios from "axios"
import { computed, onMounted, reactive, ref } from "vue"
import {Request} from '@/composable/public/RequestApi'
import { useStore } from "vuex";
onMounted(async () => {
    await getTypeLicenses()
    addAppDefault()
    sum()
})

const {ReqGet} = Request()
const plansState = ref(false)
const store = useStore()
const start = computed(()=> store.getters['Start/start'])
const totals = reactive({
    discount: 0,
    total: 0,
    percent: 0
})
const appUser = ref([
    {id:0}
])
const apps = ref([])
async function getTypeLicenses() {
    const response = await ReqGet('newClient')
    apps.value = response
}
function checkApp(appId) {
    return appUser.value.find(e=>e.id == appId)
}
function addAppDefault() {
    if(start.value.license != null && typeof(start.value.license) == 'object'){
        appUser.value = start.value.license
    }else{
        const filter = apps.value.filter((app)=>{
            return app.label == 'configuracoes' || app.label == 'ponto de venda'
        })
        appUser.value = filter
    }

}

function addApp(app) {
    if (appUser.value.find(e=>e.id == app.id)) {
        if (app.label == 'configuracoes') return
        if (appUser.value.length <=2) return
        const newObject = appUser.value.filter(e=>e.id !== app.id)
        appUser.value = newObject
        store.commit('Start/StartSaveLicense',appUser.value)
        totals.month = null
        store.commit('Start/StartSaveTotals',totals)
        return sum()
    }
    appUser.value.push(app)
    totals.month = null
    store.commit('Start/StartSaveTotals',totals)
    store.commit('Start/StartSaveLicense',appUser.value)
    return sum()
}

function sum() {
    var total = 0
    totals.percent = 0
    appUser.value.forEach((app)=>{
        total += Number(app.price)
    })

    if (appUser.value.length == 3) totals.percent = 5
    if (appUser.value.length == 4) totals.percent = 10
    if (appUser.value.length > 4) totals.percent = 15

    var discount = total/100*totals.percent
    totals.total = total - discount
    totals.discount = discount
}

function addPlan(plan) {
    totals.discount = plan.discount
    totals.total = plan.total - plan.discount
    totals.month = plan.month
    store.commit('Start/StartSaveTotals',totals)
}
</script>

<style lang="scss" scoped>
    .Container{
        width: 100%;
        height: 100%;
        display: grid;
        grid-auto-rows: 85% 15%;
        .apps{
            width: 100%;
            height: 100%;
            display: grid;
            grid-template-columns: repeat(4,1fr);
            gap: .7rem;
            overflow-y: auto;
            @include scroll;
            .app{
                width: 100%;
                height: 150px;
                display: flex;
                align-items: center;
                justify-content: center;
                span{
                    width: 80%;
                    height: 70%;
                    background-color: white;
                    position: relative;
                    img{
                        width: 100%;
                        height: 100%;
                        border-radius: 5px;
                    }
                    >svg{
                        transition: 1s;
                        position: absolute;
                        left: 5px;
                        top: 5px;

                    }
                    .add{
                        background-color: transparent;
                        color: transparent;
                    }
                    .price{
                        position: absolute;
                        background-color: var(--bgButtons);
                        color: white;
                        padding: 0px 2px 0px 2px;
                        font-size: 12px;
                        font-weight: 600;
                        right: -10px;
                        top: 5px;
                        transform: rotateZ(35deg);
                        border-radius: 3px;
                    }
                    .appName{
                        max-width: 15ch;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                    }
                }
                &:hover{
                    cursor: pointer;
                    .price{
                        animation: zoom 1s infinite;
                    }
                    svg{
                        background-color: unset;
                        color: var(--bgButtons);
                        font-size: 18px;
                    }
                    @keyframes zoom{
                        0%{
                            font-size: 12px;
                        }
                        50%{
                            font-size: 14px;
                        }
                        0%{
                            font-size: 12px;
                        }

                    }
                }
            }
        }
        .amount{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            gap: .5rem;
            >div:nth-child(1){
                width: 90%;
                display: flex;
                flex-direction: row;
                >div{
                    width: 100% !important;
                    strong,
                    label{
                        width: 100%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                }
            }
            >div:nth-child(2){
                width: 10%;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                position: relative;
                color: var(--bgButtons);
                transition: 1s;
                cursor: pointer;
                border-radius: 3px;
                padding: 10px;
                &:hover{
                    background-color: var(--bgButtons);
                    color: white;
                }
                >svg{
                    width: 100% !important;
                    animation: mover 1s infinite;
                    @keyframes mover {
                        0%{
                            margin-left: -10px;
                        }
                        50%{
                            margin-left: 0px;
                        }
                        100%{
                            margin-left: -10px;
                        }
                    }
                }
            }
        }
    }
    @media screen and (max-width: 1150px) {
        .apps {
            display: grid;
            grid-template-columns: repeat(3,1fr) !important;
        }
    }
    @media screen and (max-width: 850px) {
        .apps {
            display: grid;
            grid-template-columns: repeat(2,1fr) !important;
        }
    }
    @media screen and (max-width: 450px) {
        .apps {
            display: grid;
            grid-template-columns: repeat(1,1fr) !important;
        }
        .amount{
            display: flex;
            flex-direction: column !important;
            align-items: center;
            width: 100%;

            >div:nth-child(1){
                width: 100%;
                display: flex;
                flex-direction: column !important;

                >div{
                    display: flex;
                    justify-content: space-between;
                    strong,
                    label{
                        width: 100%;
                        display: flex;
                        align-items: unset !important;
                        justify-content: unset !important;
                    }
                    strong{
                        width: 100% !important;
                        display: flex;
                        justify-content: flex-end !important;
                    }
                }
            }
            >div:nth-child(2){
                width: 100% !important;
                display: flex;
                flex-direction: column !important;
                padding: 3px;

            }
        }
    }

</style>
