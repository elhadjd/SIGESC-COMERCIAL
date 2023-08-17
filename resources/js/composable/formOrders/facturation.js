import useEventsBus from "@/Eventbus";
import axios from "axios";
import { useToast } from "primevue/usetoast";
import { reactive } from "vue";
export const form = ((print,Invoice,statePayment,loading,form_faturacao,modalConfirm)=>{
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
                function: CancelTransfer,
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
                name: '/clients'
            },
            AddRelation: {
                name: 'addClient',
            },
            saveOrder:{
                name: 'ConfirmOrder',
            },
            cancelOrder:{
                name: 'cancelInvoice'
            },
            updateOrder:{
                name: 'UpdateRows'
            },
            getItems:{
                name: 'getItems',
            },
            addItems:{
                name: 'AddItem'
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
            title: 'Fatura',
            relationType: 'Cliente',
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
                  name: 'PriceSold',
                  type: 'number',
                  label:'Preço de venda',
                  class: 'PriceSold',
                  disabled: false
                },
                {
                    name: 'Discount',
                    type: 'number',
                    label:'Disconto',
                    class: 'Discount',
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
                    name: 'final_price',
                    type: 'number',
                    label:'Preço final',
                    class: 'final_price',
                    disabled: true
                },
                {
                  name: 'TotalSold',
                  type: 'number',
                  label:'Total',
                  class: 'TotalSold',
                  disabled: true
                }
            ],

            totalOrder:{
                totals: [
                    {
                        title: 'Total da mercadoria',
                        amount: 'TotalMerchandise',
                    },
                    {
                        title: 'Total de disconto',
                        amount: 'discount',
                    },
                    {
                        title: 'Total de Iva',
                        amount: 'tax',
                    }
                ],
                total: {
                    title: 'Total da Fatura',
                    amount: 'TotalInvoice',
                },
                restPayable: 'RestPayable'
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


    function CancelTransfer() {
        if (Invoice.value.state == 'Anulado') return message('Esta fatura já foi anulada','warn');
        modalConfirm.value.state = 'open'
        modalConfirm.value.message = 'Anular esta fatura '
    }

    function Close() {
        form_faturacao.value = 'Ordens'
    }

    function message(message,tipo){
        toast.add({
            severity: tipo,
            summary: 'Message',
            detail: message,
            life: 5000,
        })
    }

    return {general,CancelTransfer,message,Close,printOrder,CancelSubmit}
})
