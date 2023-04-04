<template>
  <div class="principal">

    <div class="titulos">

        <label for="DataEncomenda">Data de Encomenda : </label>

        <label for="DataEncomenda">Data de Recepção : </label>

    </div>

     <div class="campos">
         <input type="text" :value="FormatDate(encomenda.created_at)" :disabled="encomenda.estado != 'Cotação'">
         <input type="text" :value="FormatDate(encomenda.updated_at)" :disabled="encomenda.estado != 'Cotação'">
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from "@vue/runtime-core";
import { useStore } from "vuex";
import moment from 'moment'

    const encomenda = computed(()=>{
        return useStore().state.EncomendaCompra
    })
    const FormatDate = ((date) =>{
        return moment(date).format('DD-MM-YYYY')
    })
</script>

<style lang="scss" scoped>
.principal {
    display:flex;
    flex-direction: row;
    .titulos {
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        label {
            margin-bottom:22px;
        }
    }

    .campos {
        input {
            width:100%;
            margin-bottom:15px;
            padding: 0;
            border:none;
            outline: 0;
            box-shadow: 0 0 0 0;
            border-bottom:1px solid #ddd;
        }
    }
}
</style>
