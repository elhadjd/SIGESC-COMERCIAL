import { StoreProduct, category } from "@/types/product";
import { computed, reactive } from "vue";
import { useStore } from "vuex";
import { ProductServices } from "./productServices";

export const BannerProductServices = (()=>{
    const store = useStore()
    const product = computed<StoreProduct>(()=> store.getters['Product/product'])
    const {stateDrop} = ProductServices()
    const categories = reactive({
        listCategories: <category[]> [],
        category: {
          idProduct: product.value.data.id,
          category:  <category>{created_at: '',id:0,name:'',updated_at: ''},
        },
        StateModal: false,
    });

    const createCategory = (idProduct:number) => {
        categories.StateModal = true;
        categories.category.idProduct = idProduct;
    };

    const AddCategoryObject = (event: category) => {
        product.value.data.category = event
        product.value.data.category_product_id = event.id;
        stateDrop.value = ''
    };

    const addTypeProduct = (type) => {
        product.value.data.product_type = type
        product.value.data.product_type_id = type.id;
        stateDrop.value = ''
    };

    return{
        categories,
        createCategory,
        AddCategoryObject,
        stateDrop,
        addTypeProduct
    }
})