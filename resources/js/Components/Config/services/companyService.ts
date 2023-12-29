import axios from 'axios';
import currencyCodes, { CurrencyCodeRecord } from 'currency-codes'
import { ref } from 'vue';

export const useCompanyService = ((props: any,emits:any)=>{
    const allCurrencies = currencyCodes.data;
    const currencyAll = ref<CurrencyCodeRecord[]>(currencyCodes.data)
    const company = ref(props.company)
    const showDrop = ref("")
    const progress = ref(false)
    const changeCurrency = ((currency:CurrencyCodeRecord)=>{
        company.value.currency_company = currency
        showDrop.value = ''
    })
    const SearchCurrency = ((text:string)=>{
        const filter = allCurrencies.filter((currency)=>{
            return currency.currency.toLocaleLowerCase().includes(text.toLocaleLowerCase())||
            currency.code.toLocaleLowerCase().includes(text.toLocaleLowerCase())
        })
        currencyAll.value = filter
    })

    const saveCompany = (()=>{
        if(progress.value) return
        progress.value = true
        axios.post('saveCompany',{...company.value})
        .then((response) => {
            emits('message',response.data.message,response.data.type)
            emits('close')
        }).catch((err) => {
            console.log(err);
        }).finally(()=>{
            progress.value = false
        });
    })
    return {
        changeCurrency,
        currencyAll,
        company,
        showDrop,
        SearchCurrency,
        progress,
        saveCompany,
        allCurrencies
    }
})
