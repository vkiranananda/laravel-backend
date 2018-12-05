<template>
	<div>
		<show-uploads-button :config="{type: 'all', fieldType: 'mce'}" v-if="field.upload"></show-uploads-button>
    	<textarea :id='mceId'></textarea>
	</div>
</template>

<script>

	import tinymce from 'tinymce/tinymce'
	import 'tinymce/plugins/advlist'
	import 'tinymce/plugins/lists'
	import 'tinymce/plugins/charmap'
	import 'tinymce/plugins/print'
	import 'tinymce/plugins/preview'
	import 'tinymce/plugins/hr'
	import 'tinymce/plugins/anchor'
	import 'tinymce/plugins/pagebreak'
	import 'tinymce/plugins/searchreplace'
	import 'tinymce/plugins/wordcount'
	import 'tinymce/plugins/visualblocks'
	import 'tinymce/plugins/visualchars'
	import 'tinymce/plugins/code'
	import 'tinymce/plugins/fullscreen'
	import 'tinymce/plugins/insertdatetime'
	import 'tinymce/plugins/nonbreaking'
	import 'tinymce/plugins/save'
	import 'tinymce/plugins/table'
	import 'tinymce/plugins/contextmenu'
	import 'tinymce/plugins/directionality'
	import 'tinymce/plugins/emoticons'
	import 'tinymce/plugins/template'
	import 'tinymce/plugins/paste'
	import 'tinymce/plugins/textcolor'
	import 'tinymce/plugins/colorpicker'
	import 'tinymce/plugins/textpattern'
	// import 'tinymce/plugins/codesample'
	import 'tinymce/plugins/link'
	import 'tinymce/plugins/image'
	import 'tinymce/plugins/autolink'

	import 'tinymce/themes/modern/theme';

	import 'tinymce-i18n/langs/ru'

	// import clone from 'lodash.clone'

	import showUploadsButton from '../uploads/show-uploads-button'

    export default {
        components: { 'show-uploads-button': showUploadsButton },
  		mounted: function () { 
  		console.log('mount'+this.mceId);			
			tinymce.init({
	            selector: '#'+this.mceId,
	            plugins: [
	            	'advlist lists charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking save table contextmenu directionality emoticons template paste textcolor colorpicker textpattern link paste image autolink '
	            	],
	            	//codesample
	            language: 'ru',
	         	toolbar: 'undo redo | bold italic underline |alignleft aligncenter alignright alignjustify | forecolor fontsizeselect | bullist numlist outdent indent | link image ',
	         	//codesample
	            fontsize_formats: '0.8rem 0.9rem 1rem 1.1rem 1.2rem 1.5rem 1.7rem 2rem',

	            convert_urls : false,
	            paste_data_images: true,
	            height: this.height,
	            image_dimensions: false,
	            image_title: true,
	            image_class_list: [
	                {title: 'Нет обтекания', value: ''},
	                {title: 'Текст слева', value: 'image-text-left'},
	                {title: 'Текст справа', value: 'image-text-right'},
	            ],
	            skin_url: '/backend/tinymce/lightgray',
	            // base_url: '/backend/tinymce/',
	            // base: '//tinymce/',
	            setup: (editor) => {
	            	this.editor = editor;
				    editor.on("init", () => {
				    	editor.setContent(this.field.value);
				    });
	                editor.on('change', () => {
	                    this.$emit('change', editor.getContent());
	                });
	            }
			});

  		},
  		beforeDestroy: function () {
  			this.editor.destroy();
  		}, 
        data() {
            return {
                editor: null,
                mceId: 'tinymce-'+Math.random().toString(36).substr(2, 9),
                //htmlBody: this.field.value
            }
        },

  		computed: {
            height () {
            	if(this.field.height == undefined) return 300;
            	else return this.field.height;
            }
        },

        props: [ 'field' ],
    }
</script>