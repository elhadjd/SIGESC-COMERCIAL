import axios from "axios";

export const Search = ((object,loading)=>{

    const getFilter = (async (path)=>{
        loading.value = 'start'
        await axios.get(path)
        .then((response) => {
            object.search = response.data
        }).catch((err) => {
            console.log(err);
        }).finally(()=>{
            loading.value = null
        });
    })

    return {getFilter}
})
