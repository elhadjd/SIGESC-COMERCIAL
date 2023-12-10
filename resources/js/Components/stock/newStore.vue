<template>
   <div class="principal">
      <div class="form-container">
         <div class="Header">
            <button>
               <FontAwesomeIcon icon="fa-solid fa-users"/>
               <div>
                  <span></span>
                  <span>{{$t('words.article')}}</span>
               </div>
            </button>
         </div>
         <div class="Main">
            <div class="Name-Img-control">
               <div class="form-nome">
                  <input type="text" v-model="form.name" :placeholder="$t('words.store')">
               </div>
            </div>
            <div class="info-basic">
               <div class="form-content">
                  <div class="form-Control">
                     <label for="city">{{$t('words.city')}}:</label>
                     <input type="text" v-model="form.city" :placeholder="$t('words.city')" id="city">
                  </div>
                  <div class="form-Control">
                     <label for="bairo">{{$t('words.neighborhood')}}:</label>
                     <input type="text" v-model="form.neighborhood" :placeholder="$t('words.neighborhood')" id="bairo">
                  </div>
                  <div class="form-Control">
                     <label for="numCasa">{{$t('words.houseNumber')}}:</label>
                     <input type="text" v-model="form.house_number" :placeholder="$t('words.houseNumber')" id="numCasa">
                  </div>
               </div>
            </div>
         </div>
         <div class="Footer">
         </div>
      </div>
   </div>
</template>

<script setup>
import useEventsBus from "@/Eventbus";
import { onMounted, ref, watch } from "@vue/runtime-core";
import axios from "axios";

const {bus} = useEventsBus()
const emits = defineEmits(['message','closeForm'])

const props = defineProps({
    armagen: Object
})
const form = ref({
    // id: "",
    name: null,
    city: null,
    neighborhood: null,
    house_number: null
})

onMounted(()=>{
    if (props.armagen.id) {
        form.value = {
            name: props.armagen.name,
            city: props.armagen.city,
            house_number: props.armagen.house_number,
            neighborhood: props.armagen.neighborhood,
            id: props.armagen.id
        }
    }
})

watch(()=>bus.value.get('saveArmagen'),(payload)=>{
    saveArmagen(form.value);
})

const saveArmagen = ((form)=>{
    if (form.name == "" || form.name == null) return message('Preencha os campos','info')
    axios.post('saveStore',{...form})
    .then((response) => {
        emits('message','Sucesso','success')
        emits('closeForm')
    }).catch((err) => {
        console.log(err);
    });
})

const message = ((message,type)=>{
    emits('message',message,type)
})
</script>

<style scoped lang="scss">
.principal{
    background-color: #f9f9f9f9;
    width: 100% !important;
    height: 100% !important;
    overflow-y: auto;
    @include scroll;
    @include formulary
}
</style>
