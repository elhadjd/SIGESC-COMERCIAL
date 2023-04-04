<template>
    <div class="principal">
        <form @submit="handleSubmitForm">
            <div class="Form-control">
                <label for="DataEncomenda">Data de Encomenda : </label>
                <input type="date" id="DataEncomenda" @change="changeDate" v-model="props.invoice.DateOrder">
            </div>
            <div class="Form-control">
                <label for="vencimento"> Data de Vencimento: </label>
                <input @change="changeDate" v-model="props.invoice.DateDue" id="vencimento" type="date">
            </div>
        </form>
    </div>
</template>

<script setup>

import { ref ,onMounted, watch} from "vue";
import { useStore } from "vuex";
import moment from 'moment'
import axios from "axios";
const store = useStore();
const props = defineProps({
    invoice: Number
})


 const changeDate = (() =>{
    axios.post(`ChangeDateInvoice/${props.invoice.id}`,{...props.invoice})
    .catch((err) => {
        console.log(err);
    });
 })
</script>

<style scoped lang="scss">
@import '../../../assets/faturacao/css/CriarFaturas.scss';
</style>
