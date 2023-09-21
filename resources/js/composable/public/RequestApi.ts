import { useStore } from "vuex";

import axios from "axios"

export const Request = (()=>{

    const store = useStore()
    async function ReqGet(route: String): Promise<void> {
        return await axios.get(`https://bosgc.sisgesc.net/api/${route}`,{
            headers: {
                Authorization: 'oEn34JE6gDfVuZlR6QRWX8Q2byn9repjspVFWoz2SZdncBYePGc7XoKZ8Noo'
            },
        }).then((response) => {
            return response.data
        }).catch((err) => {
            return err.response.data
        });
    }
    async function ReqPost(route: String,param: Object): Promise<void> {
      return await axios.post(`https://bosgc.sisgesc.net/api/${route}`,{
            ...param
        },{
            headers: {
                Authorization: 'oEn34JE6gDfVuZlR6QRWX8Q2byn9repjspVFWoz2SZdncBYePGc7XoKZ8Noo',
            }
        }).then((response) => {
            return response.data
        }).catch((err) => {
            return err.response.data
        })
    }

    return {ReqGet,ReqPost}
})
