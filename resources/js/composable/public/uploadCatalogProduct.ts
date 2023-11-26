import { StoreProduct } from "@/types/product";
import axios from "axios";
import { computed, ref } from "vue";
import { useStore } from "vuex";
import { serviceMessage } from "./messages";

export const useUploadCatalogProduct = (()=>{
    const progress = ref<boolean>(false)
    const {showMessage} = serviceMessage()
    const store = useStore()
    const StateModalConfirm = ref<{
        image_id: number,
        state:boolean
    }>({
        image_id: 0,
        state: false
    })
    const product = computed<StoreProduct>(()=>store.getters['Product/product'])
    
    const onImageChange = (e: { target: { files: { length: number; item: (arg0: number) => { (): any; new(): any; size: any; }; }; }; dataTransfer: { files: any; }; }) => {

        var files = e.target.files || e.dataTransfer.files;
        let arquivo = files[0].name;
        let extension = arquivo.indexOf(".") < 1 ? "" : arquivo.split(".").pop();
        if (extension == "") {
          return false;
        } else {
            let formatos_permitidos = [
                "jpg",
                "png",
                "gif",
                "jpeg",
                "JPG",
                "PNG",
                "GIF",
                "JPEG",
            ];
            let resultado = formatos_permitidos.includes(extension);
            if (resultado) {
                var tamanho_maximo = 2242880;
                var fsizet = 0;
                for (var i = 0; i <= e.target.files.length - 1; i++) {
                    var fsize = e.target.files.item(i).size;
                    fsizet = fsizet + fsize;
                }
                if (fsizet > tamanho_maximo) {
                    return alert("tamanho da imagem muito grande");
                } else {
                     upload(files[0])
                }
            }else{
                return alert('Ficheiro não permetido')
            }
        }
    };
    const upload = async(file) => {
        progress.value = true
        var imagem = new Image();
        var reader = new FileReader();
        reader.onload = (e) => {
            axios.post(`/uploadImageCatalog/${product.value.data.id}`,{image:e.target.result})
            .then((response) => {
                if(response.data.message) {
                    showMessage(response.data.message,response.data.type)
                    product.value.data.catalog_product = response.data.data.catalog_product

                    return
                }
                
                product.value.data.catalog_product = response.data.catalog_product
            }).catch((err) => {
                console.log(err);
            }).finally(()=>{
                progress.value = false
            });
        };

        reader.readAsDataURL(file);
    };

    const confirmDelete = ((id:number)=>{
        StateModalConfirm.value.image_id = id
        StateModalConfirm.value.state = true
    })

    const deleteImage = (async()=>{
        await axios.delete(`/deleteImageInCatalogProduct/${StateModalConfirm.value.image_id}`)
        .then((response) => {
            if(response.data.message) {
                showMessage(response.data.message,response.data.type)
            }
            product.value.data.catalog_product = response.data.catalog_product
        }).catch((err) => {
            showMessage('Erro do servidor','Error')
            console.log(err);
        }).finally(()=>{
            StateModalConfirm.value.image_id = 0
            StateModalConfirm.value.state = false
        });
    })
    return {onImageChange,progress,StateModalConfirm,deleteImage,confirmDelete}
})
