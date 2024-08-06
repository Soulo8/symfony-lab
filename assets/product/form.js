import { Dropzone } from 'dropzone';
import 'dropzone/dist/dropzone.css';

const dropzoneElement = document.querySelector('#my-dropzone');

const dropzone = new Dropzone(dropzoneElement, {
    url: "/product/new",
    //autoProcessQueue: false,
    paramName: "images",
    maxFilesize: 2,
    accept: function(file, done) {
      done();
    },
    /*init: function() {
      this.on("addedfile", file => {
        // Créer un nouvel input de fichier
        const inputFile = document.createElement('input');
        inputFile.type = 'file';
        inputFile.name = 'images[]';
        inputFile.style.display = 'none';

        // Associer l'objet fichier à l'input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        inputFile.files = dataTransfer.files;

        // Ajouter l'input au formulaire
        dropzoneElement.appendChild(inputFile);
      });
    }*/
});

// code suivant ne fonctionne pas
/*
Dropzone.options.myDropzone = {
    paramName: "images",
    maxFilesize: 2,
    accept: function(file, done) {
      done();
    }
};
*/