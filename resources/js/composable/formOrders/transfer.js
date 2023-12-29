import useEventsBus from "@/Eventbus";
import axios from "axios";
import { reactive } from "vue";
import { useI18n } from "vue-i18n";

export const form = ((emits,store,print,order,loading)=>{
    const {emit} = useEventsBus()
    const {t} = useI18n()
    const general = reactive({
        buttons: [
            {
                name: t('words.confirm'),
                state: true,
                function: submitOrder
            },
            {
                name: t('words.annul'),
                class: 'discartar',
                state: true,
                function: CancelTransfer,
            },
            {
                name: t('words.print'),
                class: 'discartar',
                function: printOrder,
                state: true
            },
            {
                name: t('words.close'),
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
                  label:t('words.article'),
                  class: 'nameItem',
                  disabled: true
                },
                {
                  name: 'armagen',
                  type: 'select',
                  label:t('words.store'),
                  class: 'nameItem',
                  disabled: false,
                },
                {
                    name: 'quantity',
                    type: 'number',
                    label:t('words.quantity'),
                    class: 'quantity',
                    disabled: false
                  },
                {
                  name: 'priceSold',
                  type: 'number',
                  label:t('words.price'),
                  class: 'priceSold',
                  disabled: false
                },
                {
                  name: 'spent',
                  type: 'number',
                  label:t('words.expenses'),
                  class: 'spent',
                  disabled: false
                },
                {
                    name: 'final_price',
                    type: 'number',
                    label:`${t('words.price')} ${t('words.final')}`,
                    class: 'final_price',
                    disabled: true
                },
                {
                  name: 'total',
                  type: 'number',
                  label:t('words.total'),
                  class: 'total',
                  disabled: true
                }
            ],

            totalOrder:{
                totals: [
                    {
                        title: `${t('words.total')} ${t('words.of')} ${t('words.merchandise')}`,
                        amount: 'totalMerchandise',
                    },
                    {
                        title: `${t('words.total')} ${t('words.of')} ${t('words.expenses')}`,
                        amount: 'total_spent',
                    }
                ],
                total: {
                    title:  `${t('words.total')} ${t('words.of')} ${t('words.transfer')}`,
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
        emits('modulo',t('words.transfer'))
    }

    return {general,CancelTransfer,Close,printOrder}
})
