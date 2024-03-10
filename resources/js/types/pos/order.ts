import { ListPrices, Product, ProductDiscountTs, ProductType, Stock } from "../product";
import { ProviderProduct } from "../provider";
import { UserTypeScript } from "../userTypeScript";

export interface ItemsTypeScript{
    dataexp: string,
    datafab: string,
    discount?: ProductDiscountTs,
    estado: string,
    quantity: number,
    price_sold: number,
    id: number,
    imposto: string,
    list_price: ListPrices[],
    nome: string,
    preco_pedido: number,
    preçocust: number,
    product?: ItemsTypeScript,
    preçovenda: number,
    product_type: ProductType,
    product_type_id: number,
    qtd: number,
    quantidade: number,
    stock: Stock[],
    stock_sum_quantity: number,
    total: number
    total_cust: number,
}

export interface OrderTypeScript{
    cliente: any;
    id: number,
    state: string,
    client: string,
    number: string,
    session: number,
    user?: UserTypeScript,
    items: ItemsTypeScript[],
    methods: MethodsPaymentTs[],
    total: number,
    lastItem: number,
    numbers: string,
    positionAlter: string,
    formPayment: boolean
}

export interface MethodsPaymentTs{
    id: number,
    method_translate:MethodsPaymentTranslateTs[],
    name: string,
    updated_at: string,
    valor: string,
}

export interface MethodsPaymentTranslateTs{
    id: number,
    local: string,
    translate: string,
    methods_id: number,
}

