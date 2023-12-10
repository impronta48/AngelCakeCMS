CKEDITOR.dtd.$removeEmpty['i'] = false;
CKEDITOR.dtd.$removeEmpty.i = 0;
CKEDITOR.dtd.$removeEmpty['span'] = false;
CKEDITOR.editorConfig = function(config) {
    config.height = '70vh';
    //config.extraPlugins = 'widget,lineutils,uploadimage';
    config.extraPlugins = 'youtube';
    config.contentsCss = [
        '//cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css',
        '/js/node_modules/bootstrap/dist/css/bootstrap.min.css',
    ];
    config.allowedContent = true;
    /* config.toolbarGroups = [
        { name: 'document', groups: ['mode', 'document', 'doctools'] },
        { name: 'clipboard', groups: ['clipboard', 'undo'] },
        { name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing'] },
        '/',
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
        { name: 'links', groups: ['links'] },
        { name: 'insert', groups: ['insert'] },
        '/',
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph'] },
        { name: 'styles', groups: ['styles'] },
        { name: 'colors', groups: ['colors'] },
        { name: 'tools', groups: ['tools'] },
        { name: 'others', groups: ['others'] },
        { name: 'about', groups: ['about'] }
    ]; */
    config.toolbarGroups = [        
        { name: 'clipboard', groups: ['clipboard', 'undo'] },
        { name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing'] },
        { name: 'links', groups: ['links'] },
        '/',
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'paragraph'] },
        '/',
        { name: 'styles', groups: ['styles'] },
        { name: 'insert', items: [ ] },

    ];

    config.removeButtons = 'Save,NewPage,ExportPdf,Preview,Print,SelectAll,Form,HiddenField,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,Smiley,SpecialChar,PageBreak,Font,Maximize,About,Flash,Styles,Size';

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