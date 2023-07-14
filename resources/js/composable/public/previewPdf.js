export const usePreviewPdf = ((element,client) => {
    const onFileChangePdf = (e) => {
      return new Promise((resolve, reject) => {
        var files = e.target.files || e.dataTransfer.files;
        var tamanho_maximo = 2242880;
        var fsizet = 0;
        for (var i = 0; i <= e.target.files.length - 1; i++) {
          var fsize = e.target.files.item(i).size;
          fsizet = fsizet + fsize;
        }
        if (fsizet > tamanho_maximo) {
          console.log("tamanho de imagem muito grande");
          reject();
        } else {
          createPdf(files[0], resolve);
        }
      });
    };
  
    const createPdf = (file, resolve) => {
      var reader = new FileReader();
  
      reader.onload = (e) => {
        const pdfDataUrl = e.target.result;
        generatePdfPreview(pdfDataUrl);
        resolve();
      };
  
      reader.readAsDataURL(file);
    };
  
    const generatePdfPreview = (pdfDataUrl) => {
      const pdfPreviewElement = document.createElement("embed");
      pdfPreviewElement.src = pdfDataUrl;
      pdfPreviewElement.type = "application/pdf";
      pdfPreviewElement.width = "100%";
      pdfPreviewElement.height = "50px";
      element.innerHTML = "";
      client.pdf = pdfDataUrl
      element.appendChild(pdfPreviewElement);
    };
  
    return { onFileChangePdf };
  });  