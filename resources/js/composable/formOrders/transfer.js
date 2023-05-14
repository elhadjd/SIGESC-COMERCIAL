import useEventsBus from "@/Eventbus";
import axios from "axios";
import { reactive } from "vue";

export const form = ((emits,store,print,order,loading)=>{
    const {emit} = useEventsBus()
    const general = reactive({
        buttons: [
            {
                name: "Confirmar",
                state: true,
                function: submitOrder
            },
            {
                name: "Anular",
                class: 'discartar',
                state: true,
                function: CancelTransfer,
            },
            {
                name: "Imprimir",
                class: 'discartar',
                function: printOrder,
                state: true
            },
            {
                name: "Fechar",
                function: Close,
                class: 'discartar',
                state: true
            },
        ],
        routes: {
            products: {
                name: '/products',
            },
            getRelations: {
                name: '/getArmagens'
            },
            AddRelation: {
                name: 'addLocalDestine',
            },
            saveOrder:{
                name: 'saveTransfer',
            },
            cancelOrder:{
                name: 'cancelTransfer'
            },
            updateOrder:{
                name: 'updateTransfer'
            },
            getItems:{
                name: 'selectOrder',
            },
            addItems:{
                name: 'addItems'
            },
            deleteItem:{
                name: 'deleteItem'
            },
            addTerms: {
                name: 'addDateOrder'
            }
        },
        form:{
            inputs:[
                {
                  product: true,
                  name: 'name',
                  type: 'text',
                  label:'Nome do produto',
                  class: 'nameItem',
                  disabled: true
                },
                {
                  name: 'armagen',
                  type: 'select',
                  label:'Armagen',
                  class: 'nameItem',
                  disabled: false,
                },
                {
                    name: 'quantity',
                    type: 'number',
                    label:'Quantidade',
                    class: 'quantity',
                    disabled: false
                  },
                {
                  name: 'priceSold',
                  type: 'number',
                  label:'Preço',
                  class: 'priceSold',
                  disabled: false
                },
                {
                  name: 'spent',
                  type: 'number',
                  label:'Gasto',
                  class: 'spent',
                  disabled: false
                },
                {
                    name: 'final_price',
                    type: 'number',
                    label:'Preço final',
                    class: 'final_price',
                    disabled: true
                },
                {
                  name: 'total',
                  type: 'number',
                  label:'Total',
                  class: 'total',
                  disabled: true
                }
            ],

            totalOrder:{
                totals: [
                    {
                        title: 'Total da mercadoria',
                        amount: 'totalMerchandise',
                    },
                    {
                        title: 'Total de gastos',
                        amount: 'total_spent',
                    }
                ],
                total: {
                    title: 'Total da transferencia',
                    amount: 'total',
                }
            }
        }
    });

    function printOrder(){
        return print.value = !print.value
    }

    function submitOrder() {
        loading.value = 'Confirmar'
        axios.post(`${general.routes.saveOrder.name}/${order.id}/save`).then((response) => {
            if (response.data.message) emits('message',response.data.message,response.data.type)
            if (response.data.data) return emit('stateFormOrder',response.data.data.state)
            emit('stateFormOrder',response.data.state)
        }).catch((err) => {
            console.log(err);
        }).finally(()=>{
            loading.value = null
        });
    }

    function CancelTransfer() {
        loading.value = 'Anular'
        axios.post(`${general.routes.cancelOrder.name}/${order.id}/cancel`).then((response) => {
            if (response.data.message) emits('message',response.data.message,response.data.type)
            if (response.data.data) return emit('stateFormOrder',response.data.data.state)
            emit('stateFormOrder',response.data.state)
        }).catch((err) => {
            console.log(err);
        }).finally(()=>{
            loading.value = null
        });
    }
    function Close() {
        emits('modulo','Transferencias')
    }

    return {general,CancelTransfer,Close,printOrder}
})
