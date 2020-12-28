CKEDITOR.dtd.$removeEmpty['i'] = false;
CKEDITOR.dtd.$removeEmpty.i = 0;
CKEDITOR.dtd.$removeEmpty['span'] = false;
CKEDITOR.replace('body', {
    height: 550,
    extraPlugins: 'widget,lineutils,fontawesome',
    contentsCss: [
        '//cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css',
        '/css/bootstrap.min.css',
        '/bossolasco/css/style.css',
    ],
    allowedContent: true,
    toolbarGroups: [
        { name: 'document', groups: ['mode', 'document', 'doctools'] },
        { name: 'clipboard', groups: ['clipboard', 'undo'] },
        { name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing'] },
        { name: 'forms', groups: ['forms'] },
        '/',
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph'] },
        { name: 'links', groups: ['links'] },
        { name: 'insert', groups: ['insert'] },
        '/',
        { name: 'styles', groups: ['styles'] },
        { name: 'colors', groups: ['colors'] },
        { name: 'tools', groups: ['tools'] },
        { name: 'others', groups: ['others'] },
        { name: 'about', groups: ['about'] }
    ],

    removeButtons: 'Save,NewPage,Preview,Print,Find,Replace,SelectAll,Scayt,Form,HiddenField,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,BidiLtr,BidiRtl,Language,Flash,Smiley,SpecialChar,PageBreak,Format,Font,TextColor,BGColor,Maximize,About',

});