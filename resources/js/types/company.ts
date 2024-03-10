import { UserTypeScript } from "./userTypeScript"

export interface ActivityTs{
    created_at:string
    id:number
    name:string,
    updated_at:string
}
export interface currency_companyTs{
    code:string,
    company_id:number,
    createdAt:string,
    created_at:string,
    currency:string,
    digits:number,
    id:number,
    number:number,
    updatedAt:string,
    updated_at:string
}
export interface Email_verifyTs{
    code:string,
    company_id:number,
    created_at:string,
    id:number,
    isVerify:boolean,
    updated_at:string,
    user_id:number,
}
export interface companyTs{
    activity:ActivityTs
    activity_type_id:number,
    armagens:StoresTs[]
    city:string,
    country:string
    created_at:string
    currency:string
    currency_company:currency_companyTs,
    description:string
    email:string
    email_verify:Email_verifyTs,
    house_number:string,
    id:number,
    image:string,
    latitude:string,
    license:LicenseTs,
    longitude:string,
    manager:UserTypeScript,
    name:string,
    nif:string,
    phone:string,
    sede:string,
    shopOnline:boolean,
    updated_at:string
}

export interface StoresTs{
    city:string,
    company_id:number,
    created_at:string,
    house_number:string,
    id:number,
    name:string,
    neighborhood:string,
    updated_at:string,
}

interface LicenseTs{
    app_license:App_licenseTs[]    
    company_id:number,
    from:string,
    hash:string,
    id:number,
    state:string,
    to:string,
    type_license_id:number,
}

export interface App_licenseTs{
    app_id:number,
    apps:AppsTs[],
    company_id:number,
    id:number,
    license_id:number,
}

export interface AppsTs{
    app_translate: AppTranslateTs[],
    company_id:number,
    href:string,
    id:number,
    image:string,
    name:string,
    state:string,
}

export interface AppTranslateTs{
    app_id:number
    id:number
    local:string,
    translate:string
}

