import { OrderTypeScript } from "@/types/pos/order";
import { OrdersServices } from "./ordersServices";
import { computed } from "vue";
import { useStore } from "vuex";

 export const storeOrderServices = (()=>{
    const store = useStore()
    let orderStore :{order: OrderTypeScript} = store.state.pos
    const {
        sumOrder,
        orders,
        checkCart,
        listOrdersState
    } = OrdersServices()

    const setStoreOrder = () => {
        orders.value = JSON.parse(localStorage.getItem('orders'+orderStore.order.session))
        orders.value[Number(orderStore.order.number)] = orderStore.order;
        localStorage.setItem(
        "orders" + orderStore.order.session,
        stringify(orders.value)
        );
        getOrderAtStore(orderStore.order);
    };

    function stringify(obj: any) {
        let cache = [];
        let str = JSON.stringify(obj, function(key, value) {
          if (typeof value === "object" && value !== null) {
            if (cache.indexOf(value) !== -1) {
              return;
            }
            cache.push(value);
          }
          return value;
        });
        cache = null;
        return str;
    }

    const getOrderAtStore = ((orderProps: OrderTypeScript,edit?: string)=>{

        if (JSON.parse(localStorage.getItem("orders" + orderProps.session))) {
            orders.value = JSON.parse(localStorage.getItem("orders" + orderProps.session));
            orderStore.order.number = localStorage.getItem("ordersNumbers" + orderProps.session);
            orderStore.order.session = orderProps.session
            if(!orders.value[Number(orderProps.number)]){
                return checkCart(orders.value[Number(orderProps.number)-1],edit);
            }
            return checkCart(orders.value[Number(orderProps.number)],edit);
        } else {
            orderStore.order.number = "0";
            orderProps.items = []
            orderProps.total = 0
            orders.value.push(orderProps);
            orderStore.order.number = String(orders.value.length - 1);
            localStorage.setItem(
            "ordersNumbers" + orderProps.session,
            orderProps.number
            );
            localStorage.setItem(
            "orders" + orderProps.session,
            JSON.stringify(orders.value)
            );
            // orderStore.order = orders.value[orderStore.order.number]
            store.commit("pos/setOrder", orderStore.order);
        }
    })

    const AlterOrder = (order: OrderTypeScript,edit:string) => {
        localStorage.setItem("ordersNumbers" + order.session, order.number);
        listOrdersState.value = false;
        // InputFocus();
        getOrderAtStore(order,edit);
    };

    const removeItem = (()=>{
        if (orderStore.order.items.length) {
            if(orderStore.order.lastItem != 0){
                let newList = orderStore.order.items.filter(
                (item) => item.id !== orderStore.order.lastItem
                );
                orderStore.order.items = newList;
            }else {
                orderStore.order.items.pop();
            }
        }
        let lastItem = orderStore.order.items[orderStore.order.items.length - 1];
        if (lastItem) {
            orderStore.order.lastItem = lastItem.id;
        }
        store.commit("pos/setOrder", orderStore.order);
        sumOrder();
        setStoreOrder();
    })

    return {
        getOrderAtStore,
        setStoreOrder,
        sumOrder,
        removeItem,
        AlterOrder
    }
})
