<template>
   <div class="principal">
      <div class="Modal">
         <form @submit.prevent.stop="InvoiceCancelPassword">
            <div class="Header">
               <h2>Usuario autorizado</h2>
            </div>
            <div class="Container">
               <select @change="selectUser" class="users">
                  <option selected>Seleciona o usuario</option>
                  <option
                     v-for="item in user.data"
                     :key="item.id"
                     :value="item.user_id"
                  >
                     {{item.user.name}}
                  </option>
               </select>
               <div class="password">
                  <input v-model="password" type="password" placeholder="senha" required>
               </div>
            </div>
            <div class="Footer">
               <button class="Descartar" type="button" @click="$emit('close',false)">Fechar</button>
               <button type="submit">Confirmar</button>
            </div>
         </form>
      </div>
   </div>
</template>

<script setup>
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import { ref,onMounted, reactive } from "vue";

const props = defineProps({
    invoice:{
        required:true,
        type: Object
    }
})

const toast = useToast()

const user = reactive({
    state:false,
    data:[],
    id:null
});

const emit = defineEmits(['close','showOrder']);

const password = ref();

onMounted(() => {
    getUsersAuthorized();
});

const selectUser = (event) => {
   user.id = event.target.value
}

const message = ((message , type)=>{
    toast.add({
        summary: "message",
        severity: type,
        detail: message,
        life: 5000,
    })
})

function getUsersAuthorized() {
   axios.get('/PDV/getUsersAuthorized').then((response) => {
    user.data = response.data
    }).catch((err) => {
        console.log(err);
    });
}

function InvoiceCancelPassword() {
    axios.post(`/PDV/get/CancelInvoice/${user.id}/${props.invoice.id}`,
      {password: password.value}
    ).then((response) => {
        message(response.data.message,response.data.type)
        if (response.data.type != 'error') {
         emit('showOrder',response.data.data,'edit') 
        }
    }).catch((err) => {
        console.log(err);
    });
}

</script>

<style lang="scss" scoped>
.principal {
    @include modal;
}
</style>
