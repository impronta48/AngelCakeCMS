/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ]},
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'others' },
		
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align'] },
		{ name: 'styles' },		
		{ name: 'colors' },
		
	];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript,Size,Dimension,Font,Styles,Flash';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;h4;h5;pre';

	// Simplify the dialog windows.
	//config.removeDialogTabs = 'image:advanced;link:advanced';
    
	config.extraPlugins = 'divarea,showblocks,videodetector,bootstrapTabs,\
												image,link,liststyle,showblocks,table,tableselection,\
												tabletools,colordialog,sourcearea,html5video';
	config.allowedContent=true;
	config.filebrowserBrowseUrl = '/js/elFinder/elfinder-cke.html';
};
