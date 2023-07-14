<template>
    <div class="payment">
        <div class="banks">
            <header>
                <span>Banco</span>
                <span>Nome</span>
                <span>Numero</span>
                <span>Iban</span>
            </header>
            <main>
                <div v-for="account in data?.accounts" :key="account.id" class="rows">
                    <span>{{account.bank?.name}}</span>
                    <span>{{account.name}}</span>
                    <span>{{account.number}}</span>
                    <span>{{account.iban}}</span>
                </div>
            </main>
        </div>
        <div class="totals">
            <div>
                <label for="upload">
                    <input type="file" @change="handleUpload" id="upload">
                    <font-awesome-icon icon="fa-solid fa-cloud-arrow-up" />
                </label>
                <div>
                    <img v-if="image.img != null" :src="image.img" alt="">
                    <div v-else v-html="pdfPreview"></div>
                    <button
                        v-if="image.img != null || pdfPreview != ''"
                        :disabled="loading == 2"
                        @click="uploadForm"
                    >
                        <span v-if="loading ==0">Enviar</span>
                        <i v-if="loading ==1" class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>
                        <font-awesome-icon v-if="loading ==2" icon="fa-solid fa-check" />
                    </button>
                </div>
            </div>
            <div>
                <div>
                    <label>Total:</label>
                    <span>{{formatMoney(data.client.account_client[0].total)}}</span>
                </div>
                <div>
                    <label>Disconto:</label>
                    <span>{{formatMoney(data.client.account_client[0].discount)}}</span>
                </div>
                <div>
                    <label>A pagar:</label>
                    <span>{{formatMoney(data.client.account_client[0].total - data.client.account_client[0].discount)}}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { usePreviewPdf } from "@/composable/public/previewPdf";
import { useUploadImage } from "@/composable/public/UploadImage";
import { defineProps, reactive, ref } from "vue";
import {Request} from '@/composable/public/RequestApi'
import Swal from 'sweetalert2/dist/sweetalert2.js'

const props = defineProps({
  data: Object,
});
const {ReqPost} = Request()
const loading = ref(0)
const element = document.createElement("div");

const image = reactive({
  img: null,
});

const pdfPreview = ref("");

const { onFileChange } = useUploadImage(props.data.client,image);

const { onFileChangePdf } = usePreviewPdf(element,props.data.client);

const handleUpload = (e) => {
  var files = e.target.files || e.dataTransfer.files;
  let arquivo = files[0].name;
  let extension = arquivo.indexOf(".") < 1 ? "" : arquivo.split(".").pop();
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
  let result = formatos_permitidos.includes(extension);
  loading.value = 0
  if (extension == "pdf") {
    image.img = null
    onFileChangePdf(e).then(() => {
      pdfPreview.value = element.outerHTML;
    });
  } else {
    if (!result) return message("Tipo de arquivo inválido", "warn");
    onFileChange(e);
  }
};

async function uploadForm() {
    loading.value = 1
    const response = await ReqPost('uploadFile',props.data.client)
    loading.value = 2
    Swal.fire({
        position: 'center',
        icon: 'success',
        title: 'Upload de arquivo efetuado com sucesso',
        text: 'Sera notificado via email assim que o pagamento tiver confirmado. ou liga para 929147445',
        footer: 'Obrigado pela sua prefirencia',
        showConfirmButton: true,
    })

}
</script>

<style scoped lang="scss">
@import '../../../../assets/Start/accounts';
</style>
