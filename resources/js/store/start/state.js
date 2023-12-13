export default {
    start: {
        company: {
            name: null,
            nif: null,
            phone: null,
            activity: {
                name: '',
                id: 0,
                created_at:'',
                updated_at:''
            },
            country: {
                name: '',
                code: ''
            },
            imagem: null,
            city: null,
            currency_company: {
                currency: '',
                code: '',
                digits: 0,
                number: 0
            }
        },
        user: {
            name: null,
            email: null,
            password: null,
            phone: null,
            imagem: null,
            user_language: {
                local: '',
                name: ''
            }
        },
        license: null,
        totals: [],
        step: 0,
    },
}
