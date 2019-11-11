<template>
	<div>
		<show-uploads-button :config="{type: 'all', fieldType: 'mce'}" v-if="field.upload"></show-uploads-button>
    	<textarea :id='mceId'></textarea>
	</div>
</template>

<script>
	import showUploadsButton from '../uploads/show-uploads-button'

    export default {
        components: { 'show-uploads-button': showUploadsButton },
  		mounted: function () { 
			tinymce.init({
	            selector: '#'+this.mceId,
	            plugins: [
	            	'advlist lists charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking save table  directionality emoticons template paste textpattern link paste image autolink '
	            	],
	            	//codesample
	            language: 'ru',
	         	toolbar: 'undo redo | bold italic underline |alignleft aligncenter alignright alignjustify | forecolor fontsizeselect | bullist numlist outdent indent | link image ',
	         	//codesample
	            // fontsize_formats: '0.8rem 0.9rem 1rem 1.1rem 1.2rem 1.5rem 1.7rem 2rem',

	            convert_urls : false,
	            paste_data_images: true,
	            height: this.height+'px',
	            image_dimensions: false,
	            image_title: true,
	            image_class_list: [
	                {title: 'Нет обтекания', value: ''},
	                {title: 'Текст слева', value: 'image-text-left'},
	                {title: 'Текст справа', value: 'image-text-right'},
	            ],

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
  		beforeDestroy: function () { this.editor.destroy() }, 
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