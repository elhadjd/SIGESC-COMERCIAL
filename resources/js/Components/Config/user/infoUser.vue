<template>
    <div class="Container">
        <div class="form-content">
            <div class="Form-control">
                <label for="phone">Telefone:</label>
                <input type="text" id="phone" @keyup="(e)=>$emit('changeInput',e)" :value="data.perfil?.phone" placeholder="+244">
            </div>
            <div class="Form-control">
                <label for="email">Celular:</label>
                <input type="celular" id="celular" @keyup="(e)=>$emit('changeInput',e)" :value="data.perfil?.celular" placeholder="244">
            </div>
            <div class="Form-control">
                <label for="address">Morada:</label>
                <input type="text" :value="data.perfil?.address" @keyup="(e)=>$emit('changeInput',e)" id="address" placeholder="Luanda">
            </div>
        </div>
        <div class="form-content">
            <div class="Form-control">
                <label for="country">Pais de naicimento:</label>
                <input type="text" id="country" @keyup="searchCountry" :value="data.perfil?.country" @click="country.state = !country.state"  placeholder="Angola">
                <div class="drop" v-if="country.state">
                    <span v-for="country in country.list" :key="country" @click="chooseCountry(country)">{{country.name}}</span>
                </div>
            </div>
            <div class="Form-control">
                <label for="gender">Genero:</label>
                <input type="text" :value="data.perfil?.gender" @change="(e)=>$emit('changeInput',e)" @click="genero.state = !genero.state" id="gender" placeholder="H">
                <div class="drop" v-if="genero.state">
                    <span @click="handleGender('Feminino')">Feminino</span>
                    <span @click="handleGender('Masculino')">Masculino</span>
                </div>
            </div>
            <div class="Form-control">
                <label for="birthday">Data de naicimento:</label>
                <input type="date" @change="(e)=>$emit('changeInput',e)" :value="data.perfil?.birthday" id="birthday">
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

const emits = defineEmits(['changeInput'])

const props = defineProps({
    data: Object
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

function handleGender(choose) {
    props.data.perfil.gender = choose
    genero.value.state = false
}

const searchCountry = ((event)=>{
    const filter = country.value.store.filter((item)=>{
        return String(item.name).toLocaleLowerCase().includes(event.target.value)
    })
    country.value.list = filter
})

const chooseCountry = ((item)=>{
    props.data.perfil.country = item.name
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
        .Form-control{

            position: relative;
            @include form-control;
            @include dopDown;
        }
    }
    @media screen and (max-width: 650px) {
        flex-direction: column !important;
    }
}
</style>
