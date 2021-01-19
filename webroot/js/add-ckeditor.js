// Creo un ckeditor per tutti i controlli che hanno la class="editor"
if (document.querySelector('.editor')) {
    var editors = document.querySelectorAll(".editor");
    editors.forEach(function(edt) {
        CKEDITOR.replace(edt, {
            customConfig: '/js/ckeditor.config.js'
        })
    });
}