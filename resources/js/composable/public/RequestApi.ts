import { useStore } from "vuex";

import axios from "axios"

export const Request = (()=>{

    const store = useStore()
    async function ReqGet(route: String): Promise<void> {
        return await axios.get(`http://127.0.0.1:8000/api/${route}`,{
            headers: {
                Authorization: 'CQAS9vkO0HTqZ8t9Igorqnvfm8yBYMeiKRhadwmSSIFLIBx4AevO8ZCX7aSm'
            },
        }).then((response) => {
            return response.data
        }).catch((err) => {
            return err.response.data
        });
    }
    async function ReqPost(route: String,param: Object): Promise<void> {
      return await axios.post(`http://127.0.0.1:8000/api/${route}`,{
            ...param
        },{
            headers: {
                Authorization: 'CQAS9vkO0HTqZ8t9Igorqnvfm8yBYMeiKRhadwmSSIFLIBx4AevO8ZCX7aSm',
            }
        }).then((response) => {
            return response.data
        }).catch((err) => {
            return err.response.data
        })
    }

    return {ReqGet,ReqPost}
})
