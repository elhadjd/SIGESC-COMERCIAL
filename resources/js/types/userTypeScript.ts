interface Profile{
    address: string
    birthday:string,
    celular: string,
    country:string,
    created_at:string,
    gender: string,
    id:number,
    phone:string,
    updated_at: string,
    user_id: number,
}
interface user_languageTs{
    id:number,
    user_id: number,
    code: string,
    language: string
}
interface RoleTs{
    name: string,
    translate: RoleTranslateTs,
    id: number
}
interface RoleTranslateTs{
    role_is: number,
    locale: string,
    translate: string,
    id: number
}
export interface UserTypeScript{
    id: number,
    armagen: {},
    armagen_id: number,
    company_id: number,
    config: number,
    email: string,
    image: string,
    name: string,
    perfil: Profile,
    user_language: user_languageTs,
    surname: string,
    state: string,
    roles: RoleTs
}
