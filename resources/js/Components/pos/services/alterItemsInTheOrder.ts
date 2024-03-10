import { ItemsTypeScript, OrderTypeScript } from "@/types/pos/order"
import { OrdersServices } from "./ordersServices"
import { serviceMessage } from "@/composable/public/messages"
import { useI18n } from "vue-i18n"
import { storeOrderServices } from "./storeOrderServices"
import { ListPrices } from "@/types/product"
import { useStore } from "vuex"

export const AlterItemsInTheOrder = (()=>{
    const {showMessage} = serviceMessage()
    const {t} = useI18n()
    const store = useStore()
    let orderStore :{order: OrderTypeScript} = store.state.pos
    const {
        userStor,
        sumOrder
    } = OrdersServices()
    const {
        setStoreOrder
    } = storeOrderServices()

    const alterItems = ((number:string)=>{
        orderStore.order.numbers = orderStore.order.numbers + number
        const numbersNumber = Number(orderStore.order.numbers)
        let existProduct = orderStore.order.items.find((item) => item.id === orderStore.order.lastItem);
        if (existProduct) {
            let listPrice = existProduct.list_price.filter((item)=>{
                return item.quantity <= numbersNumber
            })
            var preco = 0;
            if (listPrice.length>0) {

            } else {
                preco = existProduct.preçovenda;
            }
            if (orderStore.order.positionAlter === "quantity") {

                const newItem = alterQuantity(existProduct,numbersNumber,preco)
                if(newItem != null) existProduct = newItem
                setStoreOrder(orderStore.order);

            } else if(orderStore.order.positionAlter == 'discount'){

            }
            else {
                console.log(orderStore.order.positionAlter)
                if (userStor.value.config.length <= 0 || userStor.value.config.price == 0) {
                    
                    return showMessage(t('message.userWithOutAccess'),"warn");
                } else {
                    const total = numbersNumber * existProduct.quantidade;
                    existProduct.total = total;
                    existProduct.preco_pedido = numbersNumber;
                }
            }
            setStoreOrder(orderStore.order);
            sumOrder();
        }
    })

    const checkPrice = ((item: ItemsTypeScript,listPrice: ListPrices[],quantity: number)=>{
        const priceSelect = listPrice[listPrice.length -1];
        if (quantity >= priceSelect.quantity) {
            return priceSelect.price_discount;
        } else {
            return item.preçovenda;
        }
    })

    const alterQuantity = ((item: ItemsTypeScript,quantity: number,price: number):ItemsTypeScript=>{
        if (item.stock_sum_quantity >= quantity) {
            item.quantidade = quantity;
            const total = price * item.quantidade;
            item.total = total;
            item.preco_pedido = price;
            return item
        } else {
            showMessage(t('message.outStock'),'warn')
            return null
        }
    })

    const updateType = ((type: string)=>{
        orderStore.order.numbers = "";
        orderStore.order.positionAlter = type;
    })

    return {
        alterItems,
        checkPrice,
        updateType
    }
})
