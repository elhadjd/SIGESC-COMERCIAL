<template>
    <div class="principal">
        <form @submit="handleSubmitForm">
            <div class="Form-control">
                <label for="DataEncomenda">Data de Encomenda : </label>
                <input type="date" id="DataEncomenda" @change="changeDate" v-model="props.Order.created_at">
            </div>
            <div class="Form-control">
                <label for="vencimento"> Data de recepção: </label>
                <input @change="changeDate" v-model="props.Order.updated_at" id="vencimento" type="date">
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed, onMounted } from "@vue/runtime-core";
import { useStore } from "vuex";
import moment from 'moment'

const props = defineProps({
    Order: Object
})

    const encomenda = computed(()=>{
        return useStore().state.EncomendaCompra
    })
    const FormatDate = ((date) =>{
        return moment(date).format('DD-MM-YYYY')
    })
</script>

<style lang="scss" scoped>
.principal{
    .Form-control{
        @include form-control;
    }
}

</style>
