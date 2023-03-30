import { createStore } from 'vuex'
import VuexPersistence from 'vuex-persist'

const store = createStore({
    state: {
        configCash: [],
        StateProgress: false,
        // variaveis formulario de faturação
        MostrarModal: false,
        MethodosPagamento: null,
        FaturaFaturacao: null,
        PodeEditar: null,
        Modalfatura: false,
        OnLoad: false,
        prodFaturacao: [],
        MostrarModalPagamento: false,
        // dos produtos Produtos
        ListEncomenda: {
            PrimeiraLista: null,
            SegundaLista: null,
            TerceiraLista: null,
            QuartaLista: null,
            mostrar: false,
            MostrarFiltro: 2,
            FiltrosEscolhido: null,
        },
        EstadoMuvemento: {
            estado: false,
            tipo: null
        },

        Empresa: [],

        Controlo: {
            state: false
        },
        GuardarCliente: false,
        Painel: null,
        user: [],
        EncomendaCompra: [],
        titulo: "Faturação",
    },
    mutations: {
        ArmazenarDados(state,payload) {
          state.Empresa = payload.data
          state.user = payload.user
        },
        StoreConfigCash(state,payload){
            state.configCash = payload
        },
        CloseCash(state,payload){
            state.Controlo.state = payload
        }
    },
    plugins:[
        new VuexPersistence({
         storage: window.localStorage
        }).plugin
    ],
    actions: {
    },
});

export default store
