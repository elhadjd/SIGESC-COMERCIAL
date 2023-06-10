<template>
    <div class="Container">
        <div class="apps">
            <div class="app" v-for="app in apps" :key="app.id">
                <span>
                    <font-awesome-icon icon="fa-solid fa-plus" />
                    <div class="price">{{formatMoney(app.price)}}</div>
                    <img :src="`http://127.0.0.1:8000/app/image/${app.image}`">
                    <div class="appName">{{app.label}}</div>
                </span>
            </div>
        </div>
        <div class="amount">

        </div>
    </div>
</template>

<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import axios from "axios"
import { onMounted, ref } from "vue"
onMounted(async () => {
    await getTypeLicenses()
})

const apps = ref([])
async function getTypeLicenses() {

   await axios.get('http://127.0.0.1:8000/api/newClient', {
        headers: {
            Authorization: 'WP9zHJVjiivYQKgMXyPrroqYOEvexaYzYZJgdFqCqMc6aGa013daIOPNhSq9'
        }
    })
    .then((response) => {
        apps.value = response.data
    })
    .catch((err) => {
        console.log(err);
    });
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
                        animation: mover 1s infinite;
                    }
                    svg{
                        background-color: unset;
                        color: var(--bgButtons);
                        font-size: 18px;
                    }
                    @keyframes mover{
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
    }

</style>
