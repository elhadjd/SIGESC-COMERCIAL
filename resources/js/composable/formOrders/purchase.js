import useEventsBus from "@/Eventbus";
import axios from "axios";
import { useToast } from "primevue/usetoast";
import { reactive } from "vue";
import { useI18n } from "vue-i18n";
export const form = ((emits,print,Invoice,statePayment,loading,modalConfirm)=>{
    const toast = useToast()
    const {t} = useI18n()
    const {emit} = useEventsBus()
    const general = reactive({
        buttons: [
            {
                name: t('words.confirm'),
                state: true,
                function: submitOrder
            },
            {
                name:`${t('words.added')} ${t('words.payment')}`,
                function: addPayment,
                state: true
            },
            {
                name: t('words.print'),
                class: 'discartar',
                function: printOrder,
                state: true
            },
            {
                name: t('words.annul'),
                class: 'discartar',
                state: true,
                function: CancelPurchase,
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
                name: '/suppliers'
            },
            AddRelation: {
                name: 'addSupplier',
            },
            saveOrder:{
                name: 'confirmOrder',
            },
            cancelOrder:{
                name: 'cancelInvoice'
            },
            updateOrder:{
                name: 'UpdateItems'
            },
            getItems:{
                name: 'getPurchases',
            },
            addItems:{
                name: 'AddItemPuchase'
            },
            deleteItem:{
                name: 'deleteItem'
            },
            addTerms: {
                name: 'ChangeDatePurchase'
            },
            addPayment:{
                name: 'InvoicePayment'
            },
            amountToPay:{
                name: 'getPurchases'
            },
        },
        form:{
            title:  t('words.order'),
            relationType: 'Fornecedor',
            inputs:[
                {
                  product: true,
                  name: 'name',
                  type: 'text',
                  label: t('words.article'),
                  class: 'nameItem',
                  disabled: true
                },
                {
                    name: 'armagen',
                    type: 'select',
                    label: t('words.store'),
                    class: 'nameItem',
                    disabled: false,
                  },
                {
                    name: 'quantity',
                    type: 'number',
                    label: t('words.quantity'),
                    class: 'quantity',
                    disabled: false
                },
                {
                  name: 'priceCost',
                  type: 'number',
                  label: t('words.price'),
                  class: 'priceCost',
                  disabled: false
                },
                {
                    name: 'discount',
                    type: 'number',
                    label: t('words.discount'),
                    class: 'discount',
                    disabled: false
                },
                {
                    name: 'spent',
                    type: 'number',
                    label: t('words.expenses'),
                    class: 'spent',
                    disabled: false
                },
                {
                    name: 'tax',
                    type: 'number',
                    label: t('words.tax'),
                    class: 'tax',
                    disabled: false
                },
                {
                    name: 'finalPrice',
                    type: 'number',
                    label:`${t('words.price')} ${t('words.final')}`,
                    class: 'finalPrice',
                    disabled: true
                },
                {
                  name: 'totalItem',
                  type: 'number',
                  label: t('words.total'),
                  class: 'totalItem',
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
                        title: `${t('words.total')} ${t('words.of')} ${t('words.discount')}`,
                        amount: 'totalDiscount',
                    },
                    {
                        title: `${t('words.total')} ${t('words.of')} ${t('words.expenses')}`,
                        amount: 'TotalSpending',
                    },
                    {
                        title: `${t('words.total')} ${t('words.of')} ${t('words.tax')}`,
                        amount: 'totalTax',
                    }
                ],
                total: {
                    title: `${t('words.total')} ${t('words.of')} ${t('words.order')}`,
                    amount: 'total',
                },
                restPayable: 'restPayable'
            }
        }
    });


    function printOrder(){
        return print.value = !print.value
    }

    function addPayment(){
        if (Invoice.value.state == 'Anulado') return message('Esta fatura já foi anulada','warn');
        if (Invoice.value.state == 'Pago') return message('Pagamento completo','warn');
        statePayment.value = true
    }

    function submitOrder() {
        loading.value = 'Confirmar'
        axios.post(`${general.routes.saveOrder.name}/${Invoice.value.id}/save`).then((response) => {
            if (response.data.message && response.data.type == "error") message(response.data.message,response.data.type)
            if (response.data.message) message(response.data.message,response.data.type)
            if (response.data.data) return emit('stateFormOrder',response.data.data.state)
            emit('stateFormOrder',response.data.state)
        }).catch((err) => {
            console.log(err);
        }).finally(()=>{
            loading.value = null
        });
    }

    function CancelSubmit() {
        loading.value = 'Anular'
        modalConfirm.value.state = null
        axios.post(`${general.routes.cancelOrder.name}/${Invoice.value.id}/cancel`).then((response) => {
            if (response.data.message && response.data.type == "error") message(response.data.message,response.data.type)
            if (response.data.message) message(response.data.message,response.data.type)
            if (response.data.data) return emit('stateFormOrder',response.data.data.state)
            emit('stateFormOrder',response.data.state)
        }).catch((err) => {
            console.log(err);
        }).finally(()=>{
            loading.value = null
        });
    }


    function CancelPurchase() {
        if (Invoice.value.state == 'Anulado') return message('Esta fatura já foi anulada','warn');
        modalConfirm.value.state = 'open'
        modalConfirm.value.message = 'Anular esta Compra '
    }

    function Close() {
        emits('fechar')
    }

    function message(message,tipo){
        toast.add({
            severity: tipo,
            summary: 'Message',
            detail: message,
            life: 5000,
        })
    }

    return {general,CancelPurchase,message,Close,printOrder,CancelSubmit}
})
