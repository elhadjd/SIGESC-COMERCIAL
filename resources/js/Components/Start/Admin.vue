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
      <label for="name">Nome:</label>
      <input type="text"  placeholder="Nome" required v-model="User.name" id="name" />
   </div>

   <div class="box">
      <label for="phone">Telefone:</label>
      <input type="text" placeholder="+244" required v-model="User.phone" id="phone" />
   </div>

   <div class="box">
      <label for="email">Email:</label>
      <input type="email"  placeholder="E-mail" required v-model="User.email" id="email" />
   </div>

   <div class="box">
      <label for="password">Senha:</label>
      <input type="password" aria-selected="false" required v-model="User.password" id="password" />
   </div>

   <div class="box">
      <label for="password1">Repita a senha:</label>
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

const {createImg,onFileChange} = useUploadImage(User.value, image);

onMounted(()=>{
    console.log(User.value);
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
