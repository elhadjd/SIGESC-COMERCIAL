import { OrderTypeScript } from "@/types/pos/order";
import { Product } from "@/types/product";
import axios from "axios";
import { ref,computed } from "vue";
import { useStore } from "vuex";
import { serviceMessage } from "@/composable/public/messages"

export const OrdersServices = (()=>{
    const showProducts = ref<boolean>(false)
    const listOrdersState = ref<boolean>(false)
    const {showMessage} = serviceMessage()

    const store = useStore()
    let orderStore :{order: OrderTypeScript} = store.state.pos
    const progress = ref<boolean>(false)
    const userStor = computed(()=>store.getters['publico/public'].user)
    const orders = ref<OrderTypeScript[]>([])

    const checkOrders = (async(ordersProps: OrderTypeScript[],session: number)=>{
        progress.value = true
        await getOrders(session)
        .then((response) => {
            if(response.data != null){
                if(response.data.data.length > ordersProps.length){
                    localStorage.removeItem('orders'+session)
                    localStorage.setItem("ordersNumbers"+session,response.data.data.length)
                    localStorage.setItem('orders'+session,JSON.stringify(response.data.data))
                    location.reload()
                }
            }
        }).catch((err) => {
            console.log(err);
        }).finally(()=>{
            progress.value = false
        });
    })

    const ClickOnRowItem = (lastItem: number) => {
        orderStore.order.lastItem = lastItem;
        orderStore.order.numbers = "";
    };

    const setNewOrder = () => {

        if (orderStore.order.items.length <= 0) {
            return showMessage(
                "Atenção não e possivel crear duas pedidos vazios",
                "info"
            );
        } else {
            orders.value = JSON.parse(localStorage.getItem('orders'+orderStore.order.session))
            orderStore.order.number = String(orders.value.length);
            orderStore.order.items = [];
            store.state.pos.ClientePos = null;
            orderStore.order.total = 0;
            orderStore.order.cliente = null;
            localStorage.setItem(
                "ordersNumbers" + orderStore.order.session,
                orderStore.order.number
            );
            orders.value.push(orderStore.order);
            localStorage.setItem( "orders" + orderStore.order.session, JSON.stringify(orders.value)
            );
            store.commit("pos/setOrder", orderStore.order);
            listOrdersState.value = false;
        }
    };

    const getOrders = (async(session: number)=>{
        return await axios.get(`/PDV/getOrderSingleUser/${localStorage.getItem('locale') || 'en'}/${session}`)
    })

    const checkIfProductExistAtTheOrder = ((product: Product)=>{
        return orderStore.order.items.find((item) => item.id === product.id);
    })

    const checkCart = ((orderProps: OrderTypeScript,edit: string)=>{
        if (orderProps.state != "Pago"||edit) {
            if (orderProps.items.length) {
                store.state.pos.ClientePos = orderProps.cliente;
                if (edit) {
                    orderStore.order.items = []
                    orderProps.items.forEach(item => {
                        if (item.product) {
                            item.product.preco_pedido = item.price_sold
                            item.product.quantidade = item.quantity
                            item.product.total = item.total
                            orderStore.order.items.push(item.product)
                        }else{
                            item.preco_pedido = item.preco_pedido
                            item.quantidade = item.quantidade
                            item.total = item.total
                            orderStore.order.items.push(item)
                        }
                    });
                }else{
                    orderStore.order.items = orderProps.items;
                }
                sumOrder();
            }
        } else {

            const testar = orders.value.find((o) => o.state === "Cotação");
            if (testar) {
                localStorage.setItem(
                    "ordersNumbers" + orderStore.order.session,
                    testar.number
                );
                orderStore.order.number = testar.number;
                orderStore.order.items = orders.value[testar.number].items;
                store.state.pos.ClientePos = orders.value[testar.number].cliente;
                sumOrder();
            } else {
                orderStore.order.number = String(orders.value.length);

                localStorage.setItem( "ordersNumbers" + orderStore.order.session,orderStore.order.number);
                orderStore.order.number = localStorage.getItem(
                    "ordersNumbers" + orderStore.order.session
                );
                orderStore.order.cliente = store.state.pos.ClientePos;
                orders.value.push(orderStore.order);
                localStorage.setItem(
                    "orders" + orderStore.order.session,
                    JSON.stringify(orders.value)
                );
                sumOrder();
            }
        }
        store.commit("pos/setOrder", orderStore.order);
    })

    const sumOrder = (()=>{
        orderStore.order.total = 0;
        orderStore.order.items.forEach((novo) => {
            orderStore.order.total += Number(novo.total)
        });
    })

    const setClient = (event: string) => {
        orderStore.order.cliente = event;
        orders.value[orderStore.order.number] = orderStore.order;
        localStorage.setItem(
            "orders" + orderStore.order.session,
            JSON.stringify(orders.value)
        );
    };


    const makePayment = () => {
        if (!orderStore.order.items.length) {
            orderStore.order.formPayment = false;
            return showMessage("Não tem nehum item no carrinho por favor adicione","info");
        } else {
            // IdEncomenda.value = orderStore.order.number;
            orderStore.order.formPayment = true;
        }
    };

    return {
        checkOrders,
        getOrders,
        progress,
        userStor,
        orders,
        sumOrder,
        checkIfProductExistAtTheOrder,
        checkCart,
        showProducts,
        listOrdersState,
        setNewOrder,
        ClickOnRowItem,
        setClient,
        makePayment
    }
})
