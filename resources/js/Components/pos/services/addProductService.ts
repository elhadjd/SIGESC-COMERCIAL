import { serviceMessage } from "@/composable/public/messages"
import { OrdersServices } from "./ordersServices"
import { useI18n } from "vue-i18n"
import { Product } from "@/types/product"
import { AlterItemsInTheOrder } from "./alterItemsInTheOrder"
import { ItemsTypeScript, OrderTypeScript } from "@/types/pos/order"
import { ref } from "vue"
import { storeOrderServices } from "./storeOrderServices"
import { useStore } from "vuex"

export const AddProductAtCart = (()=>{
    const store = useStore()
    let orderStore :{order: OrderTypeScript} = store.state.pos
    const {t} = useI18n()
    const {checkPrice} = AlterItemsInTheOrder()
    const {
        checkIfProductExistAtTheOrder,
        showProducts,
        sumOrder,
        orders
    } = OrdersServices()
    const {setStoreOrder} = storeOrderServices()
    const {showMessage} = serviceMessage()

    const AddProd = (product: Product,session: number) => {
        orderStore.order.session = JSON.parse(localStorage.getItem("session"))
        const item = checkIfProductExistAtTheOrder(product)
        if (item) {
            let listPrice = product.list_price.filter((listPrice)=>{
                return item.quantity <= listPrice.quantity + 1
            })
            var price = 0;
            if (listPrice.length>0) {
                price = checkPrice(item,listPrice,item.quantidade + 1)
            } else {
                price = item.preçovenda;
            }
            var quantidad = item.quantidade + 1;
            if (item.stock_sum_quantity >= quantidad) {

                item.quantidade += 1;
                const total = price * item.quantidade;
                item.total = total;
                item.preco_pedido = price;
            } else {
                return showMessage(t('message.outStock'),'error');
            }
            orderStore.order.lastItem = product.id;
        } else {
            orderStore.order.items.push(setProductInTheItem(product));
            orderStore.order.lastItem = product.id;
            store.commit("pos/setOrder", orderStore.order);

        }

        orderStore.order.session = session
        showProducts.value = false
        sumOrder();
        setStoreOrder();
    }

    const setProductInTheItem = ((product: Product):ItemsTypeScript=>{
        const setProduct : ItemsTypeScript = {
            discount: product.discount,
            total: product.preçovenda,
            quantidade: 1,
            preco_pedido: product.preçovenda,
            dataexp: product.dataexp,
            datafab: product.datafab,
            estado: product.estado,
            quantity: 1,
            price_sold: product.preçovenda,
            id: product.id,
            imposto: '',
            list_price: product.list_price,
            nome: product.nome,
            preçocust: product.preçocust,
            preçovenda: product.preçovenda,
            product_type: product.product_type,
            product_type_id: product.product_type_id,
            qtd: product.qtd,
            stock: product.stock,
            stock_sum_quantity: product.stock_sum_quantity,
            total_cust: product.preçocust,
        }
        return setProduct
    })
    return{
        AddProd
    }
})
