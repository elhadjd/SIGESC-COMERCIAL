export default {

    order: {
        total: 0,
        cliente: '',
        id: 0,
        state: 'Cotação',
        client: '',
        number: '0',
        session: 0,
        items: [],
        methods: [],
        total: 0,
        lastItem: 0,
        numbers: '',
        positionAlter: 'quantity',
        formPayment: false
    },

    Controlo: {
        state: false,
    },
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
        tipo: null,
    },

    Empresa: [],

    GuardarCliente: false,
    Painel: null,
    user: [],
    EncomendaCompra: [],
    titulo: "Faturação",
};
