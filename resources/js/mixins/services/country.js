import axios from "axios"
export const country = ()=> {

    let list = []
       axios.get('/data/country.json')
        .then((response) => {
            list.push(response.data);
        }).catch((err) => {
            console.log(err);
        });
    return {list}
}

