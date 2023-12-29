<template>
    <div class="form-image">
        <div>
            <img :src="image.img" />
            <span>
                <label for="image">
                    <input type="file" id="image" @change="onFileChange" />
                </label>
            </span>
        </div>
   </div>

    <div class="box">
      <label for="name">{{$t('words.name')}}:</label>
      <input type="text"  placeholder="Nome" required v-model="User.name" id="name" />
   </div>

   <div class="box">
      <label for="phone">{{$t('words.phone')}}:</label>
      <input type="text" placeholder="+244" required v-model="User.phone" id="phone" />
   </div>

   <div class="box">
      <label for="email">Email:</label>
      <input type="email"  placeholder="E-mail" required v-model="User.email" id="email" />
   </div>

    <div class="box">
        <label for="language">{{$t('words.userConfig.language')}}: </label>
        <button type="button" @click="languages.state = !languages.state" id="language">{{User.user_language?.language != ''?User.user_language?.language:$t('words.userConfig.language')}}</button>
        <div class="drop"  v-if="languages.state">
            <span v-for="language in languages.data" :key="language.code" @click="changeLanguage(language)">{{language.language}}</span>
        </div>
    </div>

   <div class="box">
      <label for="password">{{$t('login.password')}}:</label>
      <input type="password" aria-selected="false" required v-model="User.password" id="password" />
   </div>

   <div class="box">
      <label for="password1">{{$t('phrases.confirmPassword')}}:</label>
      <input type="password" aria-selected="false" required v-model="User.password1" id="password1" />
   </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import { useStore } from "vuex";
import {useUploadImage} from '@/composable/public/UploadImage'
const store = useStore()

const User = computed(()=> store.getters['Start/start'].user);

const image = reactive({
	img: User.value.imagem ? User.value.imagem : "/produtos/image/produto-sem-imagem.png",
});

const languages = ref({
    state: false,
    data: [
        {
            code: 'fr',
            language: 'Francé'
        },
        {
            code: 'pt',
            language: 'Portugaise'
        },
        {
            code: 'en',
            language: 'Ingles'
        }
    ]
})
const {createImg,onFileChange} = useUploadImage(User.value, image);

const changeLanguage = ((language)=>{
    User.value.user_language = language
    languages.value.state = false
})

watch(User.value,(newValue)=>{
  if(VerifyObjectValues(newValue)){
        store.commit('Start/StartSaveUser',newValue)
  }
})

function VerifyObjectValues(object) {
    for (let propriety in object) {
        if (object[propriety] === null) {
            return false;
        }
    }
    return true;
}
</script>

<style scoped lang="scss">
@import '../../../assets/start/form';
</style>
