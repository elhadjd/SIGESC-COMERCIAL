<template>
  <div>
    <input type="file" ref="pdfFile" @change="onFileChange">
    <textarea v-model="extractedText"></textarea>
  </div>
</template>

<script>
import * as pdfjsLib from 'pdfjs-dist';
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.worker.js';
export default {
  data() {
    return {
      extractedText: ''
    };
  },
  methods: {
    async loadPdfText(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => {
          const typedarray = new Uint8Array(reader.result);
          pdfjsLib.getDocument(typedarray).promise
            .then((pdf) => {
              let textContent = [];
              const maxPages = pdf.numPages;
              for (let i = 1; i <= maxPages; i++) {
                pdf.getPage(i).then((page) => {
                  page.getTextContent({
                    normalizeWhitespace: true,
                    disableCombineTextItems: false,
                    includeMarkedContent: true,
                    decodingOptions: {
                        encoding: 'ISO-8859-1' // ou outro formato apropriado
                    }
                  }).then((content) => {
                    const pageTextContent = content.items
                      .map(item => item.str)
                      .join('');
                    textContent.push(pageTextContent);
                    if (i === maxPages) {
                      resolve(textContent.join('\n'));
                    }
                  });
                });
              }
            })
            .catch((error) => {
              reject(error);
            });
        };
        reader.readAsArrayBuffer(file);
      });
    },
    async onFileChange(event) {
      const file = event.target.files[0];
      const extension = file.name.split('.').pop();
      if (extension !== 'pdf') {
        alert('Por favor selecione um arquivo PDF');
        return;
      }
      try {
        const text = await this.loadPdfText(file);
        this.extractedText = text;
      } catch (error) {
        console.error(error);
      }
    }
  }
};
</script>
