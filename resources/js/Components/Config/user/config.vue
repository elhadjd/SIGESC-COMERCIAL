<template>
    <div class="Container">
        <div class="Content-left">
            <div class="Form-control">
                <label for="armagen">{{$t('words.store')}}:</label>
                <input type="text" :value="data?.armagen?.name" @click="armagens.state = !armagens.state" id="armagen" placeholder="Luanda">
                <div class="drop" v-if="armagens.state">
                    <span v-for="item in armagens.armagens" :key="item.id" @click="chooseArmagen(item)">{{item.name}}</span>
                </div>
            </div>

            <div class="Form-control">
                <label for="price">{{$t('words.userConfig.accesPrice')}}:</label>
                <input type="checkbox" @change="(e)=>$emit('changeInput',e)" :value="data?.config?.price" id="price">
            </div>

            <div class="Form-control">
                <label for="quantity">{{$t('words.userConfig.stockMovement')}}:</label>
                <input type="checkbox" @change="(e)=>$emit('changeInput',e)" :value="data?.config?.quantity" id="quantity">
            </div>
        </div>
        <div class="Content-right">
            <div class="Form-control">
                <label for="infoCompany" class="truncate">{{$t('words.userConfig.print')}}:</label>
                <input type="checkbox" @change="(e)=>$emit('changeInput',e)" :value="data?.config?.infoCompany" id="infoCompany">
            </div>
            <div class="Form-control">
                <label for="print">{{$t('words.userConfig.reprint')}}:</label>
                <input type="checkbox" @change="(e)=>$emit('changeInput',e)" :value="data?.config?.infoCompany" id="print">
            </div>
             <div class="Form-control">
                <label for="language">{{$t('words.userConfig.language')}}:</label>
                <button id="language" @click="languages.state = !languages.state">{{data.user_language ? data?.user_language.language : 'selecionar Idioma'}}</button>
                <div class="drop" v-if="languages.state">
                    <span v-for="language in languages.data" :key="language.code" @click="chooseLanguage(language)">{{language.language}}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios, { all } from "axios";
import { onMounted, ref } from "vue";
const emits = defineEmits(['chooseArmagen','changeInput','language'])
const armagens = ref({
    armagens:[],
    state: false,
    choose: [],
    data: []
})

const languages = ref({
    state: false,
    data:[
        {
            language: 'Francé',
            code: 'fr'
        },
        {
            language: 'Portugais',
            code: 'pt'
        },
        {
            language: 'Ingles',
            code: 'en'
        }
    ]
})

const props = defineProps({
    data: Object
})

onMounted(async()=>{
    Promise.all([
       await getUsers(),
    ])
})

const chooseLanguage = ((language)=>{
    localStorage.setItem('locale',language.code)
    emits('language',language)
    props.data.user_language = language
    languages.value.state = false
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
    props.data.armagen = item
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
    overflow: unset !important;
    >div{
        width: 100%;
        height: 100%;
        gap: .5rem;padding: 10px;
        .Form-control{
            position: relative;
            @include form-control;
            @include dopDown;
            input[type=checkbox]{
                width: 30px !important;
            }
            .drop{
                margin-left: 0 !important;
                width: 95%;
            }
        }
    }
    @media screen and (max-width: 650px) {
        flex-direction: column !important;
    }
}
</style>
