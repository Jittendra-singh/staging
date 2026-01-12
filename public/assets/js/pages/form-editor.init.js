/*
Template Name: Minia - Admin & Dashboard Template
Author: Themesbrand
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Form editor Init Js File
*/
if(document.querySelector( '.ckeditor-classic' ) ){
    document.querySelectorAll('.ckeditor-classic').forEach(editor => {
        ClassicEditor
            .create( editor, {
				toolbar: [
				'heading',
				'bold', 'italic', 'underline',
				'bulletedList', 'numberedList',
				'link',
				'undo', 'redo'
				]
			} )
            .then( function(editor) {
                editor.ui.view.editable.element.style.height = '50px';   
            })
            .catch( function(error) {
                console.error( error );
            });
    });
}