export interface ProviderProduct{
    city:string,
    company:any,
    company_id:number,
    country:string,
    created_at:string,
    email:string,
    id:number,
    image:string,
    name:string,
    nif:string,
    phone:string,
    pivot:{produtos_id: number, fornecedore_id: number},
    sede:string,
    state:string,
    updated_at:string,
}