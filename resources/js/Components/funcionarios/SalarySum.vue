<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <span>
               <!-- <FontAwesomeIcon v-if="FormNewWorker.state" icon="fa-solid fa-outdent" /> -->
               <h2>Relatorio</h2>
            </span>
         </div>
         <div class="Header-right">
            <span class="p-input-icon-right w-100">
                <i class="pi pi-search" />
                <input type="search" @keyup="(e)=>filterWorker(e.target.value)" placeholder="Digite nome ou preço do artigo">
            </span>
            <section class="agrupar">
                <span @click="data.date.state = !data.date.state" class="dropdown-toggle">Filtrar</span>
                <div>
                    <div v-if="data.date.state" class="listGroup">
                        <span v-for="item in months" @click="getSalaryByMonth(item.month)" :key="item">{{item.name}}</span>
                    </div>
                </div>
            </section>
         </div>
      </div>
      <div class="Container">
         <div class="list-workers" v-for="worker in data.workers" :key="worker.id">
            <span><img :src="`/worker/image/${worker.image}`" alt=""></span>
            <span>{{worker.name}}</span>
            <span>{{worker.cargo}}</span>
            <span><strong>{{currencyFormat.format(worker.salary)}}</strong></span>
            <span class="dailyExpense">
               <h3>Dispesa diaria</h3>
               <div :class="Number(worker.dailyExpense) > 0 ? 'existExpense':''">
                  <strong>{{currencyFormat.format(worker.dailyExpense)}}</strong>
                  <FontAwesomeIcon icon="fa-solid fa-receipt" />
               </div>
            </span>
            <span class="discount">
               <div>
                  <h3>Desconto</h3>
                  <FontAwesomeIcon icon="fa-solid fa-tags" />
               </div>
               <div :class="Number(worker.dailyExpense) > 0 ? 'existExpense':''" >
                  <strong>{{currencyFormat.format(worker.totalDispenses)}}</strong>
                  <FontAwesomeIcon @mouseenter="drop = worker.id" icon="fa-solid fa-receipt" />
                  <div v-if="drop == worker.id" class="drop">
                     <span @click="AddExpense(worker,'solicitar')">Adicionar</span>
                     <span @click="AddExpense(worker,'show')">Folha de salario</span>
                  </div>
               </div>
               <div :class="Number(worker.totalExpense) > 0 ? 'existDiscount':''" >
                  <strong>{{currencyFormat.format(worker.totalExpense)}}</strong>
                  <FontAwesomeIcon icon="fa-solid fa-arrow-down" />
               </div>
            </span>
            <span class="to-receive">
               <div :class="Number(worker.dailyExpense) > 0 ? 'existExpense':''">
                  <FontAwesomeIcon icon="fa-solid fa-tags" />
                  <strong>{{currencyFormat.format(worker.totalDispenses)}}</strong>
               </div>
               <div :class="Number(worker.dailyExpense) > 0 ? 'existDiscount':''" >
                  <FontAwesomeIcon icon="fa-solid fa-arrow-down" />
                  <strong>{{currencyFormat.format(worker.totalExpense)}}</strong>
               </div>
               <div>
                  <h3>A receber</h3>
                  <strong>{{currencyFormat.format(worker.totalToReceive)}}</strong>
               </div>
            </span>
         </div>
      </div>
      <modalExpense
         :worker="modal.worker"
         @close="AnalisWorker()"
         v-if="modal.state == 'solicitar'"
         @message="message"
         />
      <DetailSalary
         @close="modal.state = ''"
         :worker="modal.worker"
         :session="data.sessions"
         v-if="modal.state == 'show'"
         />
   </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { onMounted, ref } from "@vue/runtime-core";
import { useStore } from "vuex";
import modalExpense from './ModalExpense.vue'
import DetailSalary from './detailSalary.vue'
const drop = ref()
const store = useStore()
const emits = defineEmits(['message'])
const currencyFormat = Intl.NumberFormat("PT-AO",{style:"currency",currency: "AOA"})
const discount = ref(0)

const modal = ref({
    state: "",
    worker: []
})

const data = ref({
    store:[],
    sessions:[],
    workers:[],
    date: {
        month: new Date().getMonth() + 1,
        state: false
    },
});

const months = ref([
    { name: "Janeiro", month: '01'},
	{ name: "Fevereiro", month: '02'},
	{ name: "Março", month: '03'},
	{ name: "Abril", month: '04'},
	{ name: "Maio", month: '05'},
	{ name: "Junho", month: '06'},
	{ name: "Julho", month: '07'},
	{ name: "Agosto", month: '08'},
	{ name: "Setembro", month: '09'},
	{ name: "Outubro", month: '10'},
	{ name: "Novembro", month: '11'},
	{ name: "Dezembro", month: '12'},
])

const message = ((message,type)=>{
     emits('message',message,type)
})

onMounted(()=>{
  AnalisWorker()
})

const getSalaryByMonth = ((date)=>{
    data.value.date.month = date
    AnalisWorker()
})

function AnalisWorker() {
    drop.value = null
    modal.value.state = ""
    store.state.StateProgress = true
    axios.get(`AnalisWorker/${data.value.date.month}`)
    .then((response) => {
        data.value.workers = response.data.workers
        data.value.store = response.data.workers
        data.value.sessions = response.data.sessions
        store.state.StateProgress = false
        data.value.date.state = false
       salarySum()
    }).catch((err) => {
        store.state.StateProgress = false
        message('Aconteceu um erro ao selecionar os funcionario ','info')
        console.log(err);
    });
}

const salarySum = (()=>{
    data.value.workers.forEach((worker)=>{
        worker.totalExpense = filterExpenses(worker).discount
        worker.totalToReceive = filterExpenses(worker).ToReceive
        worker.totalDispenses = filterExpenses(worker).dispenses
    })
})

const filterWorker = ((type)=>{
    const filter = data.value.store.filter((o)=>{
        return String(o.name).toLowerCase().includes(type.toLowerCase())
        || String(o.workers_department_id).toLowerCase().includes(type.toLowerCase())
    })
    data.value.workers = filter
})

const filterExpenses = ((worker)=>{

    let valueExpense = 0
    let ToReceive = 0
    let dispenses = 0
    let discount = 0

    let valueSession = 0
    let bonus = 0

    const sessions = data.value.sessions.filter((item)=>{
        return item.user_id === worker.user_id
    })

    sessions.forEach((session)=>{
        let total = Number(session.cash) - Number(session.orders_values)
        if (Number(total) <= -250) {
            valueSession += Number(total)
        }
        // else{
        //     bonus += Number(total - session.total_geral)
        // }
    })

    worker.expenses.forEach((expense)=>{
        valueExpense += Number(expense.value)
        dispenses = Number(worker.dailyExpense * 30)
        discount = Number(valueExpense)  + Number(String(valueSession).replace('-',''))
        ToReceive = Number(worker.salary) - Number(discount) - dispenses
    })

    return {
        dispenses: dispenses,
        discount: discount,
        ToReceive: ToReceive
    }
})

const AddExpense = ((worker,show)=>{
    modal.value.state = show
    if (show == 'show') {
        return  modal.value.worker = {data: data.value.workers,worker}
    }
    modal.value.worker = worker
})

</script>

<style scoped lang="scss">
@import '../../../assets/funcionarios/scss/salarySum';
@include dropList;
.Header-right{
    display: flex;
    justify-content: unset !important;
    align-items: unset !important;
    gap: 0.2rem;
}
</style>
