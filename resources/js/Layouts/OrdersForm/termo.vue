<template>
    <div class="principal">
        <form @submit="handleSubmitForm">
            <div class="Form-control">
                <label for="DataEncomenda">{{$t('phrases.orderDate')}} : </label>
                <input type="date" id="DataEncomenda" :disabled="stateFormOrder != 'Cotação'" @change="changeDate('DateOrder')" v-model="order.DateOrder">
            </div>
            <div class="Form-control">
                <label for="vencimento"> {{$t('phrases.dueDate')}}: </label>
                <input @change="changeDate('DateDue')" :disabled="stateFormOrder != 'Cotação'" v-model="order.DateDue" id="vencimento" type="date">
            </div>
        </form>
    </div>
</template>

<script setup>

import { ref ,onMounted, watch} from "vue";
import { useStore } from "vuex";
import moment from 'moment'
import axios from "axios";
import useEventsBus from "@/Eventbus";
const store = useStore();
const props = defineProps({
    general: Object,
    order: Object
})
const {bus} = useEventsBus()
const stateFormOrder = ref(props.order.state)
watch(()=>bus.value.get('stateFormOrder'),(payload)=>{
    stateFormOrder.value = payload
})

const changeDate = ((type) =>{ 
    axios.post(`${props.general.routes.addTerms.name}/${type}/${props.order.id}`,{...props.order})
    .catch((err) => {
        console.log(err);
    });
})
</script>

<style scoped lang="scss">
.principal{
    widows: 100%;
    height: 100%;
    form{
        width: 100%;
        .Form-control{
            @include form-control;
        }    
    }
}
</style>
