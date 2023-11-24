import { serviceMessage } from "@/composable/public/messages";
import { Product, StoreProduct, StoreTs } from "@/types/product";
import axios from "axios";
import {  computed, reactive, ref } from "vue";
import { ProductServices, modalMovementsStockTs } from "./productServices";
import { useStore } from "vuex";

export const moveProductStockServices = ((props: {data: modalMovementsStockTs},emits: any)=>{
    const {showMessage} = serviceMessage()
    const quantity = ref<number>(0)
    const modalPosition = ref<string>('');
    const displayPosition = ref<boolean>(false);
    const ShowModal = ref<boolean>(false);
    const stores = ref<StoreTs[]>([])
    const store = useStore()
    const {
        calcMovementProduct,
    } = ProductServices(emits)
    const product = computed<StoreProduct>(()=>store.getters['Product/product']);

    const form = reactive({
        tipoOper: '',
        quantity: 0,
        armagen_id: null,
        produtos_id: props.data.product.id,
        movement_type: props.data.store,
        motif: String(),
    });

    const changeStore = ((e:string)=>{
        let types = stores.value.find((item) => item.name.trim() == e.trim() );        
        if (types) {
            return sumQuantity(types)
        }
        quantity.value = 0
    })

    const sumQuantity = ((store: StoreTs)=>{
        form.armagen_id = store.id;
        const filter = store.stock.filter((item)=>{
            return item.produtos_id == product.value.data.id && item.armagen_id == form.armagen_id
        })
        if (filter[0]) {
            return quantity.value = filter[0].quantity
        }
        quantity.value = 0
    })

    const getStores = (async()=>{
        await axios.get('/getArmagens')
        .then((response) => {
            stores.value = response.data.armagens
        }).catch((err) => {
            console.log(err);
        });
    })

    const moveStock = () => {
        if (form.quantity != 0) {
            if (form.armagen_id == null) return showMessage('seleciona um armagem para continuar','info')
            form.produtos_id = product.value.data.id;
            ShowModal.value = true;
            axios
            .post(`/updateQuantity/${form.produtos_id}`, form)
            .then((Response) => {
                if (!Response.data.message) {
                    if (form.tipoOper == "Entrada") {
                        product.value.data.qtd += form.quantity;
                    } else {
                        product.value.data.qtd -= form.quantity;
                    }
                    product.value.data.stock = Response.data.product.stock
                    product.value.data.stock_sum_quantity = Response.data.product.stock_sum_quantity
                    calcMovementProduct(Response.data.movements)
                    emits("closeModal");
                    showMessage("Operação concluida","success")
                } else {
                    showMessage(Response.data.message,"info")
                }
                ShowModal.value = false;
            })
            .catch((err) => {
                console.log(err);
            });
        } else {
            showMessage("A campo quantidade não pode ficar vazio","info")
        }
    };

    return {
        moveStock,
        quantity,
        modalPosition,
        displayPosition,
        form,
        product,
        getStores,
        changeStore,
        ShowModal,
        stores
    }
})