CKEDITOR.editorConfig = function(config) {
    config.height = '70vh';
    config.extraPlugins = 'widget,lineutils,uploadimage';
    config.contentsCss = [
        '/js/node_modules/bootstrap/dist/css/bootstrap.min.css',
    ];
    config.allowedContent = true;
    config.toolbarGroups = [
        { name: 'document', groups: ['mode', 'document', 'doctools'] },
        { name: 'clipboard', groups: ['clipboard', 'undo'] },
        { name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing'] },
        { name: 'forms', groups: ['forms'] },
        { name: 'styles', groups: ['styles'] },
        { name: 'colors', groups: ['colors'] },
        { name: 'tools', groups: ['tools'] },
        { name: 'others', groups: ['others'] },
        { name: 'about', groups: ['about'] },
        '/',
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph'] },
        { name: 'links', groups: ['links'] },
        { name: 'insert', groups: ['insert'] },
    ];

    //config.removeButtons = 'Save,NewPage,Preview,Print,Find,Replace,SelectAll,Scayt,BidiLtr,BidiRtl,Language,Flash,Smiley,Font,TextColor,BGColor,Maximize,About';	

    //Configurazioni per elFinder
    //config.filebrowserBrowseUrl = '#';
    //config.filebrowserUploadUrl = '/admin/articles/ckeconnector';
    //config.imageUploadUrl = '/admin/articles/ckeconnector';
    config.filebrowserBrowseUrl = '/js/elFinder/elfinder-cke.html';

};

CKEDITOR.stylesSet.add('my_styles', [
    /* Block styles */

    // These styles are already available in the "Format" drop-down list ("format" plugin),
    // so they are not needed here by default. You may enable them to avoid
    // placing the "Format" combo in the toolbar, maintaining the same features.

    { name: 'Paragraph', element: 'p' },
    { name: 'Heading 1', element: 'h1' },
    { name: 'Heading 2', element: 'h2' },
    { name: 'Heading 3', element: 'h3' },
    { name: 'Heading 4', element: 'h4' },
    { name: 'Heading 5', element: 'h5' },
    { name: 'Heading 6', element: 'h6' },
    { name: 'Preformatted Text', element: 'pre' },
    { name: 'Address', element: 'address' },


    { name: 'Italic Title', element: 'h2', styles: { 'font-style': 'italic' } },
    { name: 'Subtitle', element: 'h3', styles: { 'color': '#aaa', 'font-style': 'italic' } },
    {
        name: 'Special Container',
        element: 'div',
        styles: {
            padding: '5px 10px',
            background: '#eee',
            border: '1px solid #ccc'
        }
    },

    /* Inline styles */
    { name: 'Big', element: 'big' },
    { name: 'Small', element: 'small' },
    { name: 'Typewriter', element: 'tt' },

    { name: 'Computer Code', element: 'code' },
    { name: 'Deleted Text', element: 'del' },
    { name: 'Inserted Text', element: 'ins' },

    { name: 'Cited Work', element: 'cite' },
    { name: 'Inline Quotation', element: 'q' },
]);