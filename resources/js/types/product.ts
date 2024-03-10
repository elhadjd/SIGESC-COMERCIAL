import { ProviderProduct } from "./provider"

export interface Product{
    category:category,
    discount: ProductDiscountTs,
    description: string,
    category_product_id:number,
    codego:string,
    company_id:number,
    created_at:string,
    dataexp:string,
    datafab:string,
    estado:string,
    fabricante:string
    fornecedor:ProviderProduct,
    id:number,
    image:string,
    imposto:string,
    inventory:boolean,
    list_price:ListPrices[],
    imagem: string,
    nome:string,
    preco_medio: number,
    preçocust:number,
    preçovenda: number,
    product_type: ProductType,
    product_type_id:number,
    qtd:number,
    stock:Stock[],
    stock_sum_quantity:number,
    total_cust:number,
    updated_at: string,
    catalog_product: Product_catalog[],
}

export interface category{
    created_at:string
    id:number
    name:string
    updated_at:string
}

export interface ProductDiscountTs{
    product_id: number,
    startDate: string,
    endDate: string,
    discount: number,
    created_at: string,
    updated_at: string
}

export interface ListPrices{
    company_id:number,
    created_at:string,
    id:number,
    price_discount:number,
    produtos_id:number,
    quantity:number,
    updated_at:string,
    user_id:number
}

export interface ProductType{
    created_at:string
    id:number
    name:string,
    updated_at:string
}

export interface Stock{
    armagen_id:number
    created_at:string
    id:number
    produtos_id:number,
    quantity: number,
    updated_at:string
}

export interface ItemType{
    created_at:string,
    id:number
    name:string,
    updated_at:string
}

export interface MovementProductTs{
    created_at:string,
    icon:string,
    count: number,
    id:number,
    name:string,
    updated_at:string
}

export interface StoreTs{
    city:string
    company_id:number
    created_at:string
    house_number:number,
    id:number
    name:string
    neighborhood:string
    stock:Stock[],
    updated_at:string
}

export interface StoreProduct{
    data:Product,
    StateFormShow: boolean,
    stateDisableButtons: boolean,
    movementsProduct: {data:MovementProductTs[],state:boolean,movement: MovementProductTs}
    stateModalCatalog: boolean
}

export interface Product_catalog{
    id:number,
    image: string,
    product_id: number,
    createdAt: string,
    updatedAt: string
}
