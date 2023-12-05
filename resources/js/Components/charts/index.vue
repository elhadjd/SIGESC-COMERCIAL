<template>
   <div class="principal">
      <div class="Header">
         <div class="Header-left">
            <span>
               <h2>Relatorio</h2>
            </span>
            <span>
               <font-awesome-icon @click="changeChart('bar')" icon="fa-solid fa-chart-column"/>
               <font-awesome-icon @click="changeChart('doughnut')" icon="fa-solid fa-chart-pie" />
               <font-awesome-icon @click="changeChart('line')" icon="fa-solid fa-chart-area" />
               <font-awesome-icon @click="changeChart('bubble')" icon="fa-solid fa-chart-gantt" />
            </span>
         </div>
         <div class="Header-right">
            <span>
            <input placeholder="Pesquisar..." type="text">
            </span>
            <span>
               <div class="groupeBy">
                  <div>
                     <button @click="stateFilter = stateFilter > 0 ? 0: 1">
                        <i class="fa fa-bars"></i>
                        <span>Filtrar</span>
                     </button>
                  </div>
                  <div v-if="stateFilter > 0" class="Form1 shadow-lg">
                     <div>
                        <label>
                           <input type="radio" id="user" value="user"  @change="filterOption('user')" v-model="checkOption">
                           <div for="user" class="Opcoes">Usuarios</div>
                        </label>
                     </div>
                     <div>
                        <label>
                           <input type="radio" id="days" value="days" @change="OnMounted()" v-model="checkOption">
                           <div for="days" class="Opcoes">Dias</div>
                        </label>
                     </div>
                     <div>
                        <label>
                           <input type="radio" id="state" @change="filterOption('state')" value="state" v-model="checkOption">
                           <div for="state" class="Opcoes">Estado</div>
                        </label>
                     </div>
                     <div>
                        <label>
                           <input type="radio" id="date" value="date" @click="stateFilter = stateFilter >1 ? 1 : 2" v-model="checkOption">
                           <div for="date" class="Opcoes">Data</div>
                        </label>
                        <div v-if="stateFilter == 2" class="SubOpcao shadow-lg">
                           <label v-for="item ,i in date" :key="i" >
                                <input
                                    type="radio"
                                    :value="item"
                                    v-model="userChoose"
                                    @click="handleItemClick(item,i)"
                                    :id="item" class="InputRadio"
                                >
                                <span v-if="item.filter != 'year'" :for="item" >{{item.name}}</span>
                                <input
                                    v-else
                                    type="number"
                                    placeholder="Ano"
                                    @keyup="handleYearInput($event.target.value)"
                                >
                                <div v-if="stateOptions == i" class="options">
                                    <span
                                        v-for="(option,index) in item.options"
                                        @click="handleOptionClick(item.filter, option.start, option.end)"
                                        :key="index"
                                        >
                                    {{option.name}}</span>
                                </div>
                           </label>
                        </div>
                     </div>
                  </div>
               </div>
            </span>
         </div>
      </div>
      <div class="Container">
         <div :class="chartType == 'doughnut' ? 'doughnut':''" class="Contents">
            <Bar :data="chartData" v-if="chartType == 'bar'" :key="chartType"/>
            <doughnut :data="chartData" v-if="chartType == 'doughnut'" :key="chartType"></doughnut>
            <Line :data="chartData" v-if="chartType == 'line'" :key="chartType"></Line>
            <Bubble :data="chartData" v-if="chartType == 'bubble'" :key="chartType"></Bubble>
         </div>
      </div>
   </div>
</template>

<script setup>
import Chart from 'chart.js/auto';
import { Title } from 'chart.js';
import { Doughnut,Bubble,Line,Bar } from 'vue-chartjs';
import { onMounted, ref } from 'vue';
import {analisOrders} from '@/composable/public/charts/index'
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { useStore } from 'vuex';
const store = useStore()
const props = defineProps({
    prefix: String
})
const {getOrder,date,filterOption,changeChart,chartData,chartType,forData,handleOptionClick,stateOptions,handleItemClick,handleYearInput} = analisOrders(props.prefix)
const analisType = ref('Faturas');
const checkOption = ref(null)
const users = ref(store.state.publico.company.users)
const OnMounted = onMounted(async()=>{
    await getOrder()
})
const userChoose = ref(0)
const stateFilter = ref(0)
</script>

<style scoped lang="scss">
@import '../../../assets/charts/index';
</style>