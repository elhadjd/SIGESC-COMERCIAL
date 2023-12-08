<template>
<Progress v-if="ShowPrecess" />
  <div class="FormPrincipal">
    <div class="h-100" v-if="product.StateFormShow">
      <ProductForm @closeForm="OnMounted" />
    </div>
    <div v-else class="Principal">
      <div class="Header">
        <div class="Header-left">
          <h4 class="text-secondary ms-4">{{`${$t('words.evaluation')} ${$t('words.of')} stock`}}</h4>
        </div>
        <div class="Header-right">
          <span class="p-input-icon-right w-100">
            <i class="pi pi-search" />
            <input
              type="text"
              v-model="inputSearch"
              @keyup="SearchProd"
              class="SearchProduct"
              :placeholder="$t('words.search')+'...'"
            />
          </span>
          <div class="Prints">
            <section class="agrupar">
              <span @click="agroup.state = !agroup.state" class="dropdown-toggle">{{$t('words.groupe')}}</span>
              <div v-if="agroup.state">
                <div class="listGroup">
                  <span @click="showAgroup('supplier')">{{$t('words.provider')}}</span>
                  <span @click="showAgroup('category')">{{$t('words.category')}}</span>
                  <span @click="showAgroup('type_product')">{{`${$t('words.type')} ${$t('words.of')} ${$t('words.article')}`}}</span>
                </div>
                  <div v-if="agroup.data?.length" class="subListGroup">
                    <span
                      v-for="item in agroup.data"
                      :key="item.id"
                      @click="getProductAgroup(item.id)"
                    >
                    {{item.name}}
                  </span>
                  </div>
              </div>
            </section>
            <button @click="exportToPDF" class="print">
              <i class="fa fa-file"></i>
              {{$t('words.print')}}
            </button>
          </div>
        </div>
      </div>
      <div id="OrdenCima" class="Container">
          <div class="Title">
            <div class="item-name">{{`${$t('words.name')} ${$t('words.of')} ${$t('words.article')}`}}</div>
            <div>{{$t('words.quantity')}}</div>
            <div>{{$t('phrases.costPrice')}}</div>
            <div>{{$t('phrases.salePrice')}}</div>
            <div>{{$t('phrases.totalCost')}}</div>
            <div>{{$t('phrases.totalSale')}}</div>
            <div>{{$t('phrases.unitProfit')}}</div>
            <div class="TotalOrden">{{$t('phrases.totalProfit')}}</div>
          </div>
          <div class="list_items">
            <div
              @click="showProduct(item)"
              v-for="item in listItems?.data"
              :key="item.id"
              :class="
                item.stock_sum_quantity == 0
                  ? 'bg-warning text-white'
                  : Number(item.preçovenda) < Number(item.preçocust)
                  ? 'bg-danger text-white' : ''" class="rows" >
              <div class="item-name">{{ item.nome }}</div>
              <div class="item">{{ item.stock_sum_quantity }}</div>
              <div class="item">
                {{ formatMoney(item.preçocust) }}
              </div>
              <div class="item">
                {{ formatMoney(item.preçovenda) }}
              </div>
              <div class="item">
                {{ formatMoney(sumItem('cost',item)) }}
              </div>
              <div class="item">
                {{ formatMoney(sumItem('sold',item)) }}
              </div>
              <div class="item">
                {{ formatMoney(sumItem('TotalCost',item)) }}
              </div>
              <div class="Lucro item">
                {{ formatMoney(sumItem('TotalSold',item)) }}
              </div>
            </div>
          </div>

      </div>
      <div class="Footer">
          <div>
            <span>{{$t('phrases.totalCost')}}</span>
            <strong>{{
              formatMoney(valores.totalCusto_geral)
            }}</strong>
          </div>
          <div>
            <span>{{$t('phrases.totalSale')}}</span>
            <strong>{{
              formatMoney(valores.totalVenda_geral)
            }}</strong>
          </div>
          <div>
            <span>{{$t('phrases.totalProfit')}}</span>
            <strong>{{
              formatMoney(valores.totalLucro_geral)
            }}</strong>
          </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import html2pdf from "html2pdf.js";
import { computed, onMounted, reactive, ref } from "@vue/runtime-core";
import axios from "axios";
import { useStore } from "vuex";
import ProductForm from "../products/product/product.vue";
import Progress from "../confirmation/progress.vue";
import pagination from '@/Layouts/paginations/paginate.vue'
const product = computed(()=> store.getters['Product/product'])

const inputSearch = ref("");
const store = useStore();
const listItems = ref();
const valores = ref([]);
const StoreProduct = ref([]);
const ShowPrecess = ref(true);
const loading = ref(null)

const agroup = ref({
  state: false,
  type: null,
  category:[],
  supplier:[],
  type_product:[],
  data: []
});

const getPage = ((data)=>{
    listItems.value = data
    StoreProduct.value = data.data
})

const showProduct = (productShow) => {
  product.value.data = productShow
  product.value.StateFormShow = true
};

const confirmar = async(id) => {
    await axios
    .post(`/confirmProd/${id}`)
    .then((response) => {
      OnMounted();
    })
    .catch((err) => {
      console.log(err);
    });
};

const FormatDinheiro = new Intl.NumberFormat("pt-AO", {
  style: "currency",
  currency: "AOA",
});
const OnMounted = onMounted(async() => {
    await getProducts();
    await getAgrupate();
});

const getProducts = async() => {
    loading.value = 'start'
    await axios
        .get("/products/1500/1")
        .then((Response) => {
            listItems.value = Response.data;
            SumProducts(listItems.value.data)
            StoreProduct.value = Response.data.data;
            ShowPrecess.value = false;
            if (inputSearch.value != "") SearchProd(inputSearch.value);
    }).catch((err) => {
      console.log(err);
      ShowPrecess.value = false;
    }).finally(() => {
        loading.value = null
    });
}

const getAgrupate = async() => {
   await axios
    .get("/Stock/GetAgroup")
    .then((response) => {
      agroup.value = response.data
    })
    .catch((err) => {
      console.log(err);
    })
    .finally(() => {
    });
}

const getProductAgroup = (event) => {
  ShowPrecess.value = true;
  axios
    .get(`/Stock/getProductAgroup/${agroup.value.type}/${event}`)
    .then((response) => {
        listItems.value = response.data.products;
        SumProducts(listItems.value)
        StoreProduct.value = response.data.products;
        ShowPrecess.value = false;
    })
    .catch((err) => {
      ShowPrecess.value = false;
      console.log(err);
    })
}

const showAgroup = (event) => {
  if(agroup.value.type === event) {return agroup.value.data = [], agroup.value.type = null; }
   agroup.value.data = agroup.value[event]
   agroup.value.type = event
}

const SearchProd = (event) => {
  if (inputSearch.value === "false") {
    const Filtrar = StoreProduct.value.filter((object) => {
      return object.stock_sum_quantity <= 0;
    });
    return (lista.value = Filtrar);
  }
  const Filtrar = StoreProduct.value.filter((object) => {
    return (
      String(object.nome)
        .toLowerCase()
        .includes(inputSearch.value.toLowerCase()) ||
      String(object.preçovenda)
        .toLowerCase()
        .includes(inputSearch.value.toLowerCase())
    );
  });
  return SumProducts(Filtrar);
};

const SumProducts = (products) => {
  valores.value.totalCusto_geral = 0;
  valores.value.totalVenda_geral = 0;
  valores.value.totalLucro_geral = 0;
  products.forEach((product) => {
    valores.value.totalCusto_geral += product.preçocust * product.stock_sum_quantity;
    valores.value.totalVenda_geral += product.preçovenda * product.stock_sum_quantity;
    valores.value.totalLucro_geral = valores.value.totalVenda_geral - valores.value.totalCusto_geral;
  });
  listItems.value.data = products
};

const sumItem = ((type,item)=>{
    if (type == 'cost') return item.stock_sum_quantity * item.preçocust
    if(type == 'sold') return item.stock_sum_quantity * item.preçovenda
    if(type == 'TotalSold'){
        let totalSold = item.stock_sum_quantity * item.preçovenda
        let totalCost = item.stock_sum_quantity * item.preçocust
        return totalSold - totalCost
    }
    return item.preçovenda - item.preçocust
})

const exportToPDF = () => {
  const item = document.getElementById("OrdenCima");
  var opt = {
    margin: 0,
    filename: "List_products",
    html2canvas: { scale: 2 },
  }

  html2pdf().set(opt).from(item).save();
};
</script>

<style scoped>
@import "../../../assets/PontoVenda/css/avaliation.css";
</style>
