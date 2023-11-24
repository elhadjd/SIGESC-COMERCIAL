import { useToast } from "primevue/usetoast";

export const serviceMessage = (()=>{
    const toast = useToast();
    const showMessage = (message,type)=>{
        toast.add({
            severity: type,
            summary: 'Informação',
            detail: message,
            life:5000,
        })
    }
    return {
        showMessage
    }
})
