<template>
    <div class="Container">
        <div class="Content-left">
            <div class="Form-control">
                <label for="armagen">Armagen:</label>
                <input type="text" v-model="armagens.choose.name" @click="armagens.state = !armagens.state" id="armagen" placeholder="Luanda">
                <div class="drop" v-if="armagens.state">
                    <span v-for="item in armagens.list" :key="item.id" @click="chooseArmagen(item)">{{item.name}}</span>
                </div>
            </div>

            <div class="Form-control">
                <label for="prices">Acesso aos preços:</label>
                <input type="checkbox" v-model="armagens.choose.name" id="prices" placeholder="Luanda">
            </div>

            <div class="Form-control">
                <label for="quantity">Pode fazer entrada de produto:</label>
                <input type="checkbox" v-model="armagens.choose.name" id="quantity" placeholder="Luanda">
            </div>
        </div>
        <div class="Content-right">
            <!-- <div class="Form-control">
                <label for="country">Pais de naicimento:</label>
                <input type="text" id="country" @keyup="searchCountry" v-model="country.choose" @click="country.state = !country.state"  placeholder="Angola">
                <div class="drop" v-if="country.state">
                    <span v-for="country in country.list" :key="country" @click="chooseCountry(country)">{{country.name}}</span>
                </div>
            </div>
            <div class="Form-control">
                <label for="genero">Genero:</label>
                <input type="text" v-model="genero.choose" @click="genero.state = !genero.state" id="genero" placeholder="H">
                <div class="drop" v-if="genero.state">
                    <span @click="genero.choose = 'Feminino'">Feminino</span>
                    <span @click="genero.choose = 'Masculino'">Masculino</span>
                </div>
            </div>
            <div class="Form-control">
                <label for="date">Data de naicimento:</label>
                <input type="date" id="date" placeholder="Luanda">
            </div> -->
        </div>
    </div>
</template>

<script setup>
import axios, { all } from "axios";
import { onMounted, ref } from "vue";
const emits = defineEmits(['chooseArmagen'])
const armagens = ref({
    list:[],
    state: false,
    choose: []
})

onMounted(async()=>{
    Promise.all([
       await getUsers(),
    ])
})

async function getUsers() {
    await axios.get('users')
    .then((response) => {
        armagens.value.list = response.data.armagens;
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
