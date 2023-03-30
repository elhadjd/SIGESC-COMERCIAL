<template>
<div class="Principal">
    <form @submit.prevent="opining">
        <div class="opining">
            <header>
                <h3>Abertura de controlo da caixa</h3>
            </header>
            <main>
                <div>
                    <label for="amount">Valor de abertura: </label>
                    <input type="text" v-model="form.amount" placeholder="1,02 AOA" id="amount" ref="inputRef">
                </div>
                <div>
                    <textarea v-model="form.observation" placeholder="Observação" cols="30" rows="10"></textarea>
                </div>
            </main>
            <footer>
                <button type="submit">Abrir</button>
            </footer>
        </div>
    </form>
</div>
</template>

<script setup>
import axios from "axios";
import { ref } from "vue";
import { useCurrencyInput } from "vue-currency-input";
const { inputRef } = useCurrencyInput({currency: 'AOA' })
const props = defineProps({
    session_id: Number
})

const emits = defineEmits(['openControl'])
const form = ref({
    amount: null,
    observation: null
})

const opining = (()=>{
    axios.post(`/PDV/caixa/opiningControl/${props.session_id}`,form.value)
    .then((response) => {
        emits('openControl',response.data);
    }).catch((err) => {
        console.log(err);
    });
})
</script>

<style lang="scss" scoped>
@import '../../../assets/Pos/css/LogIn/index';
</style>
