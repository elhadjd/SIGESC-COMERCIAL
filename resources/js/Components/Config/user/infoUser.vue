<template>
    <div class="Container">
        <div class="Content-left">
            <div class="Form-control">
                <label for="phone">Telefone:</label>
                <input type="text" id="phone" placeholder="+244">
            </div>
            <div class="Form-control">
                <label for="email">E-mail:</label>
                <input type="email" id="email" placeholder="elhadjd73@gmail.com">
            </div>
            <div class="Form-control">
                <label for="address">Morada:</label>
                <input type="text" id="address" placeholder="Luanda">
            </div>
        </div>
        <div class="Content-right">
            <div class="Form-control">
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
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
const genero = ref({
    state: false,
    choose: ''
})

const country = ref({
    choose: '',
    state:false,
    list: [],
    store:[]
})

onMounted(async()=>{
    await axios.get('/data/country.json')
    .then((response) => {
        country.value.list = response.data.data;
        country.value.store = response.data.data;
    }).catch((err) => {
        console.log(err);
    });
})

const searchCountry = ((event)=>{
    const filter = country.value.store.filter((item)=>{
        return String(item.name).toLocaleLowerCase().includes(event.target.value)
    })
    country.value.list = filter
})

const chooseCountry = ((item)=>{
    country.value.choose = item.name
    country.value.state = false
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
