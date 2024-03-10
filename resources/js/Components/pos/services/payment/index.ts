import { serviceMessage } from "@/composable/public/messages";
import { MethodsPaymentTs, OrderTypeScript } from "@/types/pos/order";
import axios from "axios";
import { ref } from "vue"
import { mapMutations, mapState, useStore } from "vuex";
export const PaymentService  = ((emits:any)=>{
    const store = useStore()
    const method = ref('Numerario')
    const orderStore = store.state.pos
    const FormatarDineiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})
    const {showMessage} = serviceMessage()
    const isSubmitting = ref(false)
    const showTotal = ref(true)
    const loadState = ref<boolean>(false)
    const valor_entregue = ref()

    const paid = ref<any>('')

    const totalPaid = ref<number>(0)

    const restPayable = ref<number>(0)

    const clientChange = ref<number>(0)

    const submitState = ref<boolean>(false)

    const orders = ref<OrderTypeScript[]>([])

    const session = ref()

    const setNumbers = ((number: string)=>{
        paid.value = Number(paid.value) + number;
        const MethodPagamento = orderStore.order.methods.find(o => o.name === method.value)
        if (MethodPagamento) {
            MethodPagamento.valor = String(paid.value)
            verifyChange()
            if (restPayable.value <= 0) {
                clientChange.value = restPayable.value;
                restPayable.value = 0;
                submitState.value = true;
            } else {
                clientChange.value = 0;
                submitState.value = false;
            }
        }
    })

    const setAmount = (event: number) => {
        paid.value += String(event);
        const MethodPagamento = orderStore.order.methods.find(o => o.name === method.value)
        if (MethodPagamento) {
            MethodPagamento.valor = String(paid.value)
            verifyChange()
            // A verificar se o troco do cliente ja e positivo
            if (restPayable.value <= 0) {
                clientChange.value = restPayable.value;
                restPayable.value = 0;
                submitState.value = true;
            } else {
                clientChange.value = 0;
                submitState.value = false;
            }
        }

    }

    const setMethods = (event:string)=> {
        paid.value = "";
        method.value = event
        verifyChange()
        if (event == "Multicaixa" || 'Transferencia') {
            const methodPayment = orderStore.order.methods.find(o => o.name === method.value)
            if (methodPayment) {
                restPayable.value = restPayable.value;
                clientChange.value = restPayable.value;
                if(restPayable.value + Number(methodPayment.valor) < 0){
                    methodPayment.valor = 0
                }else{
                    methodPayment.valor = restPayable.value + Number(methodPayment.valor)
                }
                restPayable.value = 0
                submitState.value = true
            }
        }
    }

    const trashNumbers = ()=>{
        paid.value = String(paid.value).substring(0,paid.value.length - 1);
        const existProduct = orderStore.order.methods.find(o => o.name === method.value)
        if (existProduct) {
            var num = String(existProduct.valor).substring(0, existProduct.valor.length - 1);
            existProduct.valor = num
            verifyChange();
            if (restPayable.value <= 0) {
                clientChange.value = restPayable.value
                restPayable.value = 0;
                submitState.value = true;
            } else {
                clientChange.value = 0;
                submitState.value = false;
            }
        }
    }

    const verifyChange = () =>{
        showTotal.value = false;
        totalPaid.value = 0
        var ss = orderStore.order.methods.filter(novo => novo.valor);
        ss.forEach((novo:MethodsPaymentTs) => {
            totalPaid.value += Number(novo.valor);
        });
        restPayable.value = Number(orderStore.order.total) - Number(totalPaid.value);
    }

    const validatePayment = ()=> {
        isSubmitting.value = true
        let number = orderStore.order.number
        orderStore.order.number = 0
        orderStore.order.number = `${orderStore.order.session}-${number}`
        if (restPayable.value <= 0) {
        axios.post("/PDV/ValidatePayment",orderStore.order)
            .then((Response) => {
                orderStore.order.session = 0
                orderStore.order.number = 0
                if (!Response.data.message) {
                    return print(Response.data,number)
                } else {
                    showMessage(Response.data.message,Response.data.type)
                }
            })
            .catch(function (error) {
                axios.post('/PDV/checkInvoice',orderStore.order)
                console.log(error);
            })
            .finally(()=>{
                loadState.value = false
                isSubmitting.value = false
            });
        } else {
            showMessage('Valor insuficiente ','info',)
        }
    }

    const print = ((data,number)=>{
        session.value = localStorage.getItem('session')
        orders.value = JSON.parse(localStorage.getItem('orders'+session.value));
        orderStore.order = data
        orders.value[number] = orderStore.order
        localStorage.setItem('orders'+session.value,JSON.stringify(orders.value))
        emits("sendInvoice",data);
    })

    return {
        setAmount,
        validatePayment,
        verifyChange,
        trashNumbers,
        setNumbers,
        loadState,
        clientChange,
        paid,
        submitState,
        showTotal,
        setMethods,
        restPayable,
        isSubmitting
    }
})
