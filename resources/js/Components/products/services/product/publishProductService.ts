import axios from "axios"
import { ProductServices } from "./productServices"
import { serviceMessage } from "@/composable/public/messages"
import { ref } from "vue"

export const usePublishProductService = (()=>{
    const {showMessage} = serviceMessage()
    const {news,product} = ProductServices()

    const button = ref({
        info:{
            message: 'Certifique-se de ter internet ativo pos isso efectuara uma ação na loja online',
            state: false
        },
        stateProgress: false
    })

    const showInfo = ((state:boolean)=>{
        button.value.info.state = state
    })

    async function publishProductOnline ():Promise<void> {
        button.value.stateProgress = true
        const promise = new Promise<void>(resolve=>{
            axios.post(`/publishProduct/${product.value.data.id}`)
            .then((response) => {
                if(response.data.message){
                    showMessage(response.data.message,response.data.type)
                }
                if(response.data.data != null) product.value.data = response.data.data
            }).catch((err) => {
                console.log(err);
                showMessage('Erro do servidor, por favor verifique se tem internete ativo no seu dispositivo e tenta novamente !!!','info')
            }).finally(()=>{
                button.value.stateProgress = false
            });
        })
        return promise
    }
    return {publishProductOnline,showInfo,button}
})
