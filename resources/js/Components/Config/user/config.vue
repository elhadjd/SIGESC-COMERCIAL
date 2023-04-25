<template>
    <div class="Container">
        <div class="Content-left">
            <div class="Form-control">
                <label for="armagen">Armagen:</label>
                <input type="text" v-model="armagens.choose.name" @click="armagens.state = !armagens.state" id="armagen" placeholder="Luanda">
                <div class="drop" v-if="armagens.state">
                    <span v-for="item in armagens.armagens" :key="item.id" @click="chooseArmagen(item)">{{item.name}}</span>
                </div>
            </div>

            <div class="Form-control">
                <label for="prices">Acesso aos preços:</label>
                <input type="checkbox" v-model="armagens.choose.name" id="prices">
            </div>

            <div class="Form-control">
                <label for="quantity">Pode fazer entrada de produto:</label>
                <input type="checkbox" v-model="armagens.choose.name" id="quantity">
            </div>
        </div>
        <div class="Content-right">

        </div>
    </div>
</template>

<script setup>
import axios, { all } from "axios";
import { onMounted, ref } from "vue";
const emits = defineEmits(['chooseArmagen'])
const armagens = ref({
    armagens:[],
    state: false,
    choose: [],
    data: []
})

onMounted(async()=>{
    Promise.all([
       await getUsers(),
    ])
})

async function getUsers() {
    await axios.get('users')
    .then((response) => {
        armagens.value.armagens = response.data.armagens;
        armagens.value.data = response.data;
    }).catch((err) => {
        console.log(err);
    });
}
const chooseArmagen = ((item)=>{
    emits('chooseArmagen',item)
    armagens.value.choose = item
    armagens.value.state = false
})
</script>

<style scoped lang="scss">
.Container{
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: row;
    gap: .5rem;
    box-sizing: border-box !important;
    background-color: white !important;
    padding: 10px;
    >div{
        width: 100%;
        height: 100%;
        gap: .5rem;padding: 10px;
        .Form-control{

            position: relative;
            @include form-control;
            @include dopDown;
        }

    }
}
</style>
