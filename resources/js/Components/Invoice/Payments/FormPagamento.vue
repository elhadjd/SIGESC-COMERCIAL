<template>
  <div>
    <Dialog header="Header" class="p-0 border rounded-0" :position="position" v-model:visible="displayModal" :breakpoints="{'960px': '75vw', '640px': '90vw'}" :style="{width: '70vw'}" :modal="true">
        <div class="h-100 text-secondary mt-0">
            <div class="header p-2 border-bottom">
                <span>Fazer Pagamento</span>
            </div>
            <div class="h-50 d-flex p-3">
                <div class="InfoPagamento d-flex w-50" >


                    <div class="w-50 p-2 ClienteMethod">
                        <div>Methodos</div>
                        <div>Cliente</div>
                    </div>


                    <div class="w-50 p-2">
                        <div class="dropdown">
                            <button class="btn p-0 w-100 btn-sm button" @click="methods" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{Methodo}}
                            </button>
                            <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton1">
                                <li v-for="(metodo, index) in $store.state.MethodosPagamento" :key="index">
                                    <a class="dropdown-item" @click="Method(metodo.nome)" href="#">{{metodo.nome}} </a>
                                </li>
                            </ul>
                        </div>


                        <div class="border-bottom">
                            {{fatura.nome}}
                        </div>


                    </div>
                </div>
                <div class="w-50 d-flex p-3">
                    <div class="w-50 TitlsMethod">
                        <div>Valor Total</div>
                        <div>Data de pagamento</div>
                        <div>Numero da fatura</div>
                    </div>
                    <div class="w-50 conteinerinfo">
                        <input type="text" name="valor" class="border-bottom"  v-model="RestoApagar">
                        <div class="border-bottom"> {{currentDateTime()}} </div>
                        <div class="border-bottom">{{'FT'+fatura.created_at.substring(0,fatura.created_at.length - 11)+'/'+fatura.id}} </div>
                    </div>
                </div>
            </div>
            <div >
                <div class="BotoesBaixo">
                    <button class="ConfirmPag" @click="ConfirmarPag">Confirmar Pagamento</button>
                    <button class="CancelPag" @click="$emit('FecharModalPag')">Cancelar</button>
                </div>
            </div>
        </div>
    </Dialog>
  </div>
</template>

<script setup>
import  Dialog  from "primevue/dialog";
import { onMounted, ref } from 'vue'
import {useStore, mapState} from 'vuex'
import moment from 'moment'
import axios from 'axios';
    const displayModal = ref(true)
    const position = ref('top');
    const store = useStore();
    let Methodo = ref('metodos de pagamentos')
    let RestoApagar = ref()
    let fatura = store.state.FaturaFaturacao
    const FrmatDinheiro = ref(new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA'}))

    const emits = defineEmits(['FecharModalPag'])


    const ConfirmarPag = (()=>{
        axios.post('/FazerPagamento',{
            dados: ({
                method: Methodo.value,
                ValorPago: RestoApagar.value,
                data: currentDateTime(),
                id: fatura.id
            })
        })
        .then((Response) => {
                if (!Response.data.message) {
                    emits('FecharModalPag')
                    store.state.FaturaFaturacao = Response.data.fatura
                    store.state.PodeEditar = store.state.FaturaFaturacao.estado
                } else {
                    store.state.messagen = Response.data.message
                }
            }).catch((err) => {
                console.log(err);
            })
    })

    const Method = ((even)=>{
        Methodo.value = even
    })


    onMounted(()=>{
        if (fatura.MethodPagamento !=null) {
            Methodo.value = fatura.MethodPagamento
        }
        axios.get('/BuscarPagamento?IdPagamento='+fatura.id)
        .then((Response) => {
            RestoApagar.value = Response.data.resto

        })
    })
    const currentDateTime = ((date) =>{
        return moment().format('DD MM YYYY')
    })
</script>

<style scoped>
    .header{
        font: 12pt arial;
        font-weight: bold;
    }
    .p-button {
    margin: 0.3rem .5rem;
    min-width: 10rem;
}

p {
    margin: 0;
}
.TitlsMethod div{
    font: 10pt arial;
    font-weight: 600;
    padding: 6px;
}

.conteinerinfo div{
    font: 9pt arial;
    font-weight: 600;
    padding: 5px;
}

.conteinerinfo input{
    padding: 2px;
    width: 100%;
    border: none;
    outline: none;
    font: 10pt arial;
    font-weight: 600;
    color: #666;
    border-bottom: 1px solid #eee;
}
.BotoesBaixo{
    padding: 20px 10px 10px 10px;
    border-top: 1px solid #eee;
}
.BotoesBaixo button{
    border: none;
     outline: none;
     margin: 3px;
     padding: 1px 10px 1px 10px;
}
.ConfirmPag{
    background-color: #a3498b;
    border: 1px solid #6e1c58;
    border-radius: 1px;
    color: white;
}
.CancelPag{
    background-color: white;
    border-radius: 1px;
    color: #888;
}
.CancelPag:hover{
    color: rgb(52, 51, 51);
    border: 1px solid #eee;
}
.ConfirmPag:hover{
    color: white;
    background-color: #6e1c58ca;
}
.confirmation-content {
    display: flex;
    align-items: center;
    justify-content: center;
}
.ClienteMethod div{
    font: 10pt arial;
    font-weight: 600;
    padding: 6px;
}
.p-dialog .p-button {
    min-width: 6rem;
}
.dropdown button{
    border: none;
    outline: none;
    border-bottom: 1px solid #eee;
    box-shadow: 0;
    box-shadow: 0 0 0 0;
    border-radius: 0px;
    text-align: left;
    font: 10pt arial;
    font-weight: 600;
}
.dropdown-menu{
    border-top: none;
    border-top-right-radius: 0;
    border-top-left-radius: 0;
    margin-left: -10px;
    font: 10pt arial;
    font-weight: 600;
}
</style>
