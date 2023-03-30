<template>
    <div class="Principal">
        <div class="Header">
            <h1><span>S</span>ISGESC</h1>
        </div>
        <div class="Container">
            <div :class="`databases ${databaseActive == database.name ? 'active': ''}`" 
            v-for="database,index in databases" :key="index"
            @click="SelectDatabase(database.name)">
                <img src="./database.jpeg" alt="">
                <span>{{database.name}}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
const databases = ref(Array)
const databaseActive = ref()
onMounted(async()=>{
    await axios.get('/databases')
    .then((response) => {
        databases.value = response.data
        return getLocalStore()
    }).catch((err) => {
        console.log(err);
    });
})

const emits = defineEmits('closeFormData')

const getLocalStore = (()=>{
    if (document.cookie.length > 0) {
        let c_start = document.cookie.indexOf('database' + "=");
        if (c_start != -1) {
            c_start = c_start + 'database'.length + 1;
           let c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            databaseActive.value = unescape(document.cookie.substring(c_start, c_end));
        }
    }
    else{
        console.log('o');
    }

})

const SelectDatabase = ((e)=>{
    document.cookie = 'database' + "=" + (e || "")
    emits('closeFormData');
    return getLocalStore()
})
</script>

<style lang="scss" scoped>
    .Principal{
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        align-items: center;
        .Header{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 25%;
            h1{
                font-family: arial;
                font-size: 40px;
                font-weight: 800;
                color: #714B67;
                span{
                    color: #017e84;
                }
            }
        }
        .Container{
            width: 450px;
            height: 180px;
            background-color: rgba(255, 255, 255, 0.631);
            border: 1px solid rgba(0, 0, 0, 0.055);
            .databases{
                width: 100%;
                padding: 10px;
                height: 60px;
                display: flex;
                border-bottom: 1px solid #00000005;
                background-color: rgba(0, 0, 0, 0.055);
                align-items: center;
                font: 17pt arial;
                font-weight: 600;
                color: #666;
                &:hover{
                    background-color: #00000015;
                    cursor: pointer;
                }
                img{
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    padding: 5px;
                    margin-right: 20px;
                }
            }
            .active{
                background-color: #017e84;
                color: white;
                &:hover{
                    background-color: #017e844a;
                    color: #017e84;
                }
            }
        }
    }
</style>
