import axios from "axios";
import { computed, reactive ,ref} from "vue"
import {serviceMessage} from '../../../../composable/public/messages'
import { useStore } from "vuex";
import { ItemType, MovementProductTs, Product, StoreProduct, category } from "@/types/product";
export interface modalMovementsStockTs{
    stateShow: boolean,
    TypesMovements: MovementProductTs[],
    product: Product,
    store: []
}

export const ProductServices = ((emits?:any)=>{
    const store = useStore()
    const {showMessage} = serviceMessage()
    const product = computed<StoreProduct>(()=> store.getters['Product/product'])
    const stateDrop = ref<string>('')
    const news = ref({
        message: 'Fica calma, não se apresa,as novidades estão chegando ',
        type: 'great',
        title: 'Novidade',
        state: false
    })
    const element = reactive<{img:string}>({
        img: "/produtos/image/" + product.value.data.image,
    });

    const loadProduct = ((product: Product)=>{
        store.commit('Product/changeProduct',product)
    })
    const movementsStockProduct = ref<modalMovementsStockTs>({
        stateShow: false,
        TypesMovements: [],
        product: product.value.data,
        store: []
    });

    const itemType = ref<ItemType[]>([])

    const moveProductStock = (event: any) => {
        movementsStockProduct.value.store = event;
        movementsStockProduct.value.product = product.value.data;
        movementsStockProduct.value.stateShow = true;
    };

    const calcMovementProduct = (event: any) => {
        movementsStockProduct.value.TypesMovements = [];
        event.forEach((element:any) => {
            element.count = 0;
            if (element.name == "Entrada" || element.name == "Saida") {
                movementsStockProduct.value.TypesMovements.push(element)
            }
            if (element.movements) {
                element.movements.forEach((item: { quantity: number; }) => {
                    element.count += item.quantity;
                });
            }
        });
        store.commit('Product/loadMovementsProduct',event)
    };

    const submit = async() => {
        if (!product.value.data.imagem) {
            product.value.data.imagem = null;
        }
        await axios
        .post(`/update/${product.value.data.id}`, {...product.value})
        .then((Response) => {
            showMessage(Response.data.message,Response.data.type)
        }).catch((err) => {
            console.log(err);
        }).finally(()=>{
            emits('saved')
        });
    };

    const changePrices = (typePrice: string | number, price: number) => {
        product.value.data[typePrice] = price;
    };

    const ShowMovements = (movement: MovementProductTs) => {
        product.value.movementsProduct.movement = movement
        product.value.movementsProduct.state = true
    };

    async function publishProductOnline ():Promise<void> {
        stateDrop.value = 'shopOnline'
        const promise = new Promise<void>(resolve=>{
            setTimeout(()=>{
                news.value.state = true
                stateDrop.value = ''
                resolve()
            },3000)
        })
        return promise
    }

    return {
        loadProduct,
        element,
        submit,
        movementsStockProduct,
        itemType,
        calcMovementProduct,
        stateDrop,
        moveProductStock,
        ShowMovements,
        changePrices,
        publishProductOnline,
        news
    }
})
