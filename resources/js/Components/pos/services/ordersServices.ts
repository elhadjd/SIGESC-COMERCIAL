import axios from "axios";
import { ref } from "vue";

export const OrdersServices = (()=>{

    const progress = ref<boolean>(false)

    const checkOrders = (async(session: number)=>{
        const ordersStore = localStorage.getItem('Encomendas'+session)
        const orders = JSON.parse(ordersStore)
        progress.value = true
        await getOrders(session)
        .then((response) => {
            if(response.data != null){
                if(response.data.data.length > orders.length){
                    localStorage.removeItem('Encomendas'+session)
                    localStorage.setItem("NumeroPedidos"+session,response.data.data.length)
                    localStorage.setItem('Encomendas'+session,JSON.stringify(response.data.data))
                    location.reload()
                }
            }
        }).catch((err) => {
            console.log(err);
        }).finally(()=>{
            progress.value = false
        });

    })

    const getOrders = (async(session: number)=>{
        return await axios.get(`/PDV/getOrderSingleUser/${localStorage.getItem('locale') || 'en'}/${session}`)
    })

    return {checkOrders,getOrders,progress}
})
