<template>
    <vue-simplemde :value="field.value" :configs="config" @input="change" ref="editor" />
</template>

<script>
  import VueSimplemde from 'vue-simplemde'
  // import 'simplemde/dist/simplemde.min.css'
  import 'simplemde-theme-base/dist/simplemde-theme-base.min.css'

export default {
	methods: {
		change: function(text) {
      // Фикс повторного срабатывания
      if (text != this.field.value) this.$emit('change', text)
		},

    attachFile: function(files, link) {
        
        var res = ''
        
        for ( var file of files) {
            if (file.file_type == 'image') {
                res += '!['+file.orig_name+']('+file.orig+')'
            } else {
                res += (link) ? '['+file.orig_name+']('+file.orig+')' : file.orig;
            }
            
            res += ' '
        }
        
        this.$refs.editor.simplemde.codemirror.replaceSelection(res);
    },  
	},
	components: {	VueSimplemde  },
    data() {
        return {

        }
    },
  	computed: {
        value: {
          get: function () { return this.field.value },
          set: function (text) { this.$emit('change', text) }
        },      
        config() {
          var config = {
             showIcons: ["code", "table"],
              toolbar: [
              'bold', 'italic', 'strikethrough', '|', 'code', 'quote', 'unordered-list', 'ordered-list', '|', 'link', 
              {
                name: "image",
                action: (editor) => {
                  this.$bus.$emit('UploadFilesModalShow', {type: 'all', showLink: true, return: this.attachFile}) 
                },
                  className: "fa fa-picture-o",
                  title: "Вставить картинку",
                },
                'table', '|', 'preview', 'side-by-side', 'fullscreen', '|', 'guide'
              ],             
          }  

          return config

        }
    },
    props: [ 'field' ],
}


</script>

<style lang='scss'>
  .vue-simplemde {
    .editor-toolbar::before {
      margin-bottom: 3.5px;
    }
    .editor-toolbar::after {
      margin-top: 3.5px;
    }
  }
</style>


