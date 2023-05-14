import useEventsBus from "@/Eventbus";
import axios from "axios";
import { useToast } from "primevue/usetoast";
import { reactive } from "vue";
export const form = ((emits,print,Invoice,statePayment,loading,modalConfirm)=>{
    const toast = useToast()
    const {emit} = useEventsBus()
    const general = reactive({
        buttons: [
            {
                name: "Confirmar",
                state: true,
                function: submitOrder
            },
            {
                name: "Adicionar pagamento",
                function: addPayment,
                state: true
            },
            {
                name: "Imprimir",
                class: 'discartar',
                function: printOrder,
                state: true
            },
            {
                name: "Anular",
                class: 'discartar',
                state: true,
                function: CancelPurchase,
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
                name: 'ChangeDateInvoice'
            },
            addPayment:{
                name: 'InvoicePayment'
            },
            amountToPay:{
                name: 'getItems'
            },
        },
        form:{
            inputs:[
                {
                  product: true,
                  name: 'name',
                  type: 'text',
                  label:'Produto',
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
                    label:'quantidade',
                    class: 'quantity',
                    disabled: false
                },
                {
                  name: 'priceCost',
                  type: 'number',
                  label:'Preço de compra',
                  class: 'priceCost',
                  disabled: false
                },
                {
                    name: 'discount',
                    type: 'number',
                    label:'Disconto',
                    class: 'discount',
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
                    name: 'tax',
                    type: 'number',
                    label:'Iva',
                    class: 'tax',
                    disabled: false
                },
                {
                    name: 'finalPrice',
                    type: 'number',
                    label:'Preço final',
                    class: 'finalPrice',
                    disabled: true
                },
                {
                  name: 'totalItem',
                  type: 'number',
                  label:'Total',
                  class: 'totalItem',
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
                        title: 'Total de disconto',
                        amount: 'totalDiscount',
                    },
                    {
                        title: 'Total de Gasto',
                        amount: 'TotalSpending',
                    },
                    {
                        title: 'Total de Iva',
                        amount: 'totalTax',
                    }
                ],
                total: {
                    title: 'Total da Compra',
                    amount: 'total',
                }
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
        modalConfirm.value.message = 'Anular esta fatura '
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
