<template>
   <div class="principal">
      <div id="Container" class="Modal" >
         <div class="Header">
            <span>
            <img :src="`/company/image/${company.image}`" >
            </span>
            <span>
            {{company.name}}
            </span>
            {{FormatDate()}}
         </div>
         <div class="Header-two">
            <span class="Name">{{data.name}}</span>
            <div class="salary">
               <div>
                  <div>Salario do contrato:</div>
                  <div>Dispesas diarias:</div>
                  <div>Dispesa solicitada:</div>
                  <div>Disconto da empresa:</div>
               </div>
               <div class="money">
                  <span>
                     {{formatMoney(data.salary)}} 
                     <FontAwesomeIcon icon="fa-sack-dollar"/>
                  </span>
                  <span>
                     {{formatMoney(data.totalDispenses)}} 
                     <FontAwesomeIcon icon="fa-solid fa-receipt" />
                  </span>
                  <span>
                     {{formatMoney(totalExpenses)}} 
                     <FontAwesomeIcon icon="fa-solid fa-money-bill-1-wave"/>
                  </span>
                  <span>
                     {{formatMoney(data.totalExpense)}} 
                     <FontAwesomeIcon icon="fa-solid fa-arrow-down" />
                  </span>
               </div>
               <div class="ToReceive">
                  <div>Salario a receber</div>
                  <span>{{formatMoney(data.totalToReceive)}}</span>
                  <FontAwesomeIcon icon="fa-solid fa-hand-holding-dollar"/>
               </div>
            </div>
         </div>
         <div class="Container">
            <div class="Expenses">
               <span>Dispesas solicitadas</span>
               <div class="List" v-for="expense in data.expenses" :key="expense.id">
                  <div>num: {{expense.id}}</div>
                  <div>Data: {{formatDate(expense.created_at)}}</div>
                  <div>Tipo de dispesa: {{expense.type}}</div>
                  <div>Total: {{formatMoney(expense.value)}}</div>
               </div>
            </div>
            <div class="Expenses">
               <span>Discontos</span>
               <div class="List" v-for="session in sessions" :key="session.id">
                  <div>num: {{session.id}}</div>
                  <div>Data de abertura: {{formatDate(session.created_at)}} </div>
                  <div>Data de Fecho: {{formatDate(session.updated_at)}} </div>
                  <div>{{formatMoney(session.falta)}}</div>
               </div>
            </div>
         </div>
         <div class="Footer">
            <button @click="$emit('close')" class="descartar">Fechar</button>
            <button @click="exportToPDF">imprimir</button>
         </div>
      </div>
   </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import { useStore } from "vuex";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import axios from "axios";
import moment from 'moment'
import html2pdf from "html2pdf.js";

const props = defineProps({
    worker: Object,
    session:Object
})

const data = ref(props.worker.worker)
const store = useStore()
const company = ref(store.state.Empresa)
const sessions = ref([])
const expenses = ref([])

const totalExpenses = ref(0)


const OnMounted = onMounted(()=>{
    filterSession(data.value, props.session)
    filterExpenses(data.value)
})

const FormatDate = (() =>{
    return moment().format('DD-MM-YYYY H:mm:ss')
 })

const filterSession = ((worker,listSession)=>{

    const filter = listSession.filter((session)=>{
        return session.user_id === worker.user_id
    })

    const filterDiscount = filter.filter((session)=>{
        let total = Number(session.cash) - Number(session.orders_values)
        return total <= -250
    })
    filterDiscount.forEach((session)=>{
        let total = Number(session.cash) - Number(session.orders_values)
        session.falta = Number(total)
    })

    if (filter.length > 0) return sessions.value = filterDiscount
})

const filterExpenses = ((worker)=>{
    worker.expenses.forEach((filter)=>{
        totalExpenses.value += Number(filter.value)
    })
})


 const exportToPDF = (()=> {
  const item = document.getElementById("Container");
  var opt = {
    margin: 0,
    filename: `Folha de salario de ${data.value.name}`,
    html2canvas: { scale: 2},
  };

  html2pdf().set(opt).from(item).save();
})

</script>

<style lang="scss" scoped>
@import '../../../assets/funcionarios/scss/detailSalary'
</style>