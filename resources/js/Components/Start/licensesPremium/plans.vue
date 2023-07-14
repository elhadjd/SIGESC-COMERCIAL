<template>
    <div class="principal">
        <div class="cards">
            <div v-for="plan in plans" :key="plan.id" class="card">
                <div>
                    <strong>{{plan.name}}</strong>
                    <button type="button" @click="$emit('addPlan',plan)">Aderir</button>
                </div>
                <div>
                    <ul>
                        <li>Preço: <strong>{{formatMoney(plan.total)}}</strong></li>
                        <li>Disconto: <strong>{{formatMoney(plan.discount)}}</strong></li>
                        <li>A pagar: <strong>{{formatMoney(plan.total - plan.discount)}}</strong></li>
                    </ul>
                </div>
            </div>
        </div>
        
    </div>
</template>

<script setup>
import { reactive } from "vue";
const props = defineProps({
    totals: Object
})
const emits = defineEmits(['addPlan'])
const plans = reactive([
    {
        name: "Trimestral",
        discount: 0,
        total: props.totals.total * 3,
        month: 3
    },
    {
        name: "Semestral ",
        discount: props.totals.total/100*10,
        total: props.totals.total * 5 - props.totals.total/100*10,
        month: 6
    },
    {
        name: "Anual",
        discount: props.totals.total/100*20,
        total: props.totals.total * 6 - props.totals.total/100*20,
        month: 12
    }
])

</script>

<style lang="scss" scoped>
@import '../../../../assets/Start/cards';
.principal{
    
    overflow-y: auto;
    @include scroll;
    .card{
        width: 100%;
        height: 250px !important;
    }
}

@media screen and (max-width: 1200px) {
    .cards{
        display: grid !important;
        grid-template-columns: repeat(2,1fr) !important;
    }
}

@media screen and (max-width: 800px) {
    .principal{
        display: flex;
        justify-content: center;
        align-items: center;
        .cards{
            display: grid !important;
            grid-template-columns: repeat(1,1fr) !important;
            .card{
                height: 300px !important;
                width: 100% !important;
            }
        }
    }
    
}

@media screen and (max-width: 450px) {
    .card{
        width: 70% !important;
    }
}
</style>
