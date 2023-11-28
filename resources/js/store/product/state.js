export default {
    data: {
        category: {
            created_at: '',
            id: '',
            name: '',
            updated_at: ''
        },
        category_product_id: 0,
        codego: '',
        company_id: 0,
        created_at: '',
        dataexp: '',
        datafab: '',
        imagem: null,
        estado: '',
        fabricante: '',
        fornecedor: {
            city: '',
            company: {},
            company_id: 0,
            country: '',
            created_at: '',
            email: '',
            id: 0,
            image: '',
            name: '',
            nif: '',
            phone: '',
            pivot: {
                produtos_id: 0,
                fornecedore_id: 0
            },
            sede: '',
            state: '',
            updated_at: '',
        },
        id: 0,
        image: '',
        imposto: '',
        inventory: false,
        list_price: [],
        nome: '',
        preco_medio: 0,
        preçocust: 0,
        preçovenda: 0,
        product_type: {
            created_at: '',
            id: 0,
            name: '',
            updated_at: ''
        },
        product_type_id: '',
        qtd: 0,
        stock: [],
        stock_sum_quantity: 0,
        total_cust: 0,
        updated_at: '',
        catalog_product: [],
    },
    movementsProduct: {
        data: [],
        state: false,
        movement: {
            created_at:'',
            icon:'',
            count: 0,
            id:0,
            name:'',
            updated_at:''
        }
    },
    stateModalCatalog: false,
    productComponentKey: 0,
    StateFormShow:false,
    stateDisableButtons: false,
    

};
