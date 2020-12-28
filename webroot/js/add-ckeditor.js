// Creo un ckeditor per tutti i controlli che hanno la class="editor"
if (document.querySelector('.editor')) {
    var editors = document.querySelectorAll(".editor");
    editors.forEach(function(edt) {
        ClassicEditor
            .create(edt, {
                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'strikethrough',
                        'removeFormat',
                        'link',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'indent',
                        'outdent',
                        '|',
                        'fontSize',
                        'blockQuote',
                        'insertTable',
                        'mediaEmbed',
                        'undo',
                        'redo',
                        'imageUpload',
                        'horizontalLine'
                    ]
                },
                language: 'it',
                image: {
                    toolbar: [
                        'imageTextAlternative',
                        'imageStyle:full',
                        'imageStyle:side',
                        '|',
                        'linkImage'
                    ]
                },
                styles: [
                    'full',
                    'side'
                ],
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells',
                        'tableCellProperties',
                        'tableProperties'
                    ]
                },
                licenseKey: '',
                simpleUpload: {
                    uploadUrl: '/admin/articles/upload-image.json',
                },
            })
            .catch(error => {
                console.error(error);
            });
    });
}