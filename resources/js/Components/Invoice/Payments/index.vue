<template>
  <div class="principal">
    <div class="Header">
      <div class="titulosheader">
        <div>
          <h2>Pagamentos</h2>
        </div>

        <div>
          <span class="Pesquisar p-input-icon-right w-100">
            <i class="fa fa-searchs"></i>
            <input
              class="pesquisar"
              @keyup="SearchPayment"
              type="search"
              name="PesquisarPagamentos"
              
              placeholder="Pesquisa por nome do clente"
            />
          </span>
        </div>
      </div>

      <div class="TitulosPagamentos">
        <div>Data</div>
        <div>Numero da Fatura</div>
        <div>Methodo</div>
        <div>Cliente</div>
        <div class="text-end">Valor Pago</div>
        <div class="text-end">Valor apos Pagamento</div>
        <div>Estado</div>
      </div>
    </div>
    <div class="Container">
      <div
        v-for="Pagamento in lista"
        :key="Pagamento.id"
        class="d-flex ListPagamentos"
      >
        <div>
          {{
            Pagamento.created_at.substring(0, Pagamento.created_at.length - 8)
          }}
        </div>
        <div>
          {{
            "FT" +
            Pagamento.created_at.substring(
              0,
              Pagamento.created_at.length - 12
            ) +
            "/00" +
            Pagamento.id
          }}
        </div>
        <div>{{ Pagamento.nome }}</div>
        <div>{{ Pagamento.nome }}</div>
        <div class="text-end">
          {{ FrmatDinheiro.format(Pagamento.ValorPago) }}
        </div>
        <div class="text-end">
          {{ FrmatDinheiro.format(Pagamento.TotalAposPagamento) }}
        </div>
        <div class="estado">
          <span>
            {{ "Confirmado" }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useStore } from "vuex";

const emit = defineEmits(["message"]);
const store = useStore();

let FrmatDinheiro = ref(
  new Intl.NumberFormat("pt-AO", { style: "currency", currency: "AOA" })
);

const ListPagamentos = ref([]);

const lista = ref();

onMounted(() => {
  axios.get("/BuscarPagamento").then((Response) => {
    lista.value = Response.data;
    ListPagamentos.value = Response.data;
    store.state.MostrarModalPagamento = false;
  });
});

const SearchPayment = (event) => {
  const filterPayment = ListPagamentos.value.filter((pagamento) => {
    return (
      String(pagamento.nome)
        .toLowerCase()
        .includes(event.target.value.toLowerCase()) ||
      String(pagamento.ValorPago)
        .toLowerCase()
        .includes(event.target.value.toLowerCase()) ||
      String(pagamento.data)
        .toLowerCase()
        .includes(event.target.value.toLowerCase()) ||
      String(pagamento.apelido)
        .toLowerCase()
        .includes(event.target.value.toLowerCase())
    );
  });
  if (filterPayment.length > 0) {
    lista.value = filterPayment;
  } else {
    emit("message", "Nenhum pagamento encontrado", "info");
  }
};
</script>

<style scoped lang="scss">
.principal {
  width: 100%;
  height: 100%;
  .Header {
    width: 100%;
    height: 20%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    .titulosheader {
      padding: 8px;
      display: grid;
      grid-template-columns: repeat(2,1fr);
      align-items: center;
      div {
        width: 100%;
        h2 {
            font-size: 19px;
        }
        .pesquisar {
            @include input_normal;
        }
      }
    }

    .TitulosPagamentos {
      display: flex;
      background-color: rgb(246, 246, 246);
      border-top: 1px solid #eee;
      height: 50px;
      div {
        width: 14.28%;
        border-left: 1px solid #eee;
        height: 100%;
        padding: 8px;
        font: 10pt arial;
        font-weight: bold;
      }
    }
  }

  .Container {
    height: 80%;
    max-height: 80%;
    overflow-y: auto;
    @include scroll;
    .ListPagamentos {
      border-bottom: 1px solid rgb(246, 246, 246);
      div {
        width: 14.28%;
        padding: 5px 10px 5px 5px;
        font: 9pt arial;
        font-weight: 600;
      }
      .estado {
        span {
          background-color: #a3498b8b;
          color: #220119;
          padding: 2px;
          border-radius: 30px;
          padding-left: 5px;
          padding-right: 5px;
        }
      }
      &:hover {
        cursor: pointer;
        background-color: rgb(246, 246, 246);
      }
      &:nth-child(even) {
        background-color: #f9f9f98b;
      }
    }
  }
}
</style>
