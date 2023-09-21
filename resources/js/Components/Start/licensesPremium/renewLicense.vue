<template>
<form class="mini-form">
    <div>
        <div class="Form-control">
            <label for="nif">Nif:</label>
            <input type="text" required v-model="form.nif" placeholder="Digita seu nif" id="nif">
        </div>
        <div class="Form-control">
            <label for="license">E-mail:</label>
            <input type="text" required v-model="form.email" placeholder="Digita seu email" id="license">
        </div>
        <div class="Form-control">
            <label for="license">Seleciona periodo:</label>
            <select>
                <option value="anual">Anual</option>
                <option value="anual">Semestral</option>
                <option value="anual">Trimestral</option>
            </select>
        </div>
    </div>
    <div>
        <button @submit.prevent.stop="handlerSubmitForm">
            Validar
            <i v-if="isSubmit == 0" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
            <font-awesome-icon
            :icon="`fa-solid ${isSubmit == 1 ? 'fa-check' : isSubmit == 2 ? 'fa-xmark': ''}`" />
        </button>
    </div>
</form>
</template>

<script setup>
import { ref } from "vue";

const form = ref({
    email: '',
    nif: '',
    interval: ''
})
const isSubmit = ref(2)
</script>

<style scoped lang="scss">
@import '../../../../assets/Start/index';
.mini-form{
    height: 180px !important;
}
@media screen and (max-width: 850px) {
}
</style>
