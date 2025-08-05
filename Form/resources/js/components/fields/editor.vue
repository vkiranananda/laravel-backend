<!-- https://github.com/Alex-D/Trumbowyg/issues/407 как сделать чтобы нельзя было вставлять изображения из буфера обмена -->
<template>
  <div class="editor">
    <upload-files ref="uploadFiles" :upload-url="field['upload-url']" @uploadFile="uploadFile"> </upload-files>
    <div v-if="field.readonly" v-html="field.value" class="readonly-field"></div>
    <div v-else class="small-mce" :class="field.size ? field.size : 'small'">
      <div ref="editor"></div>
    </div>
  </div>
</template>

<script>
import uploadFiles from '../../../../../MediaFile/resources/js/components/upload-files/index.js';

export default {
  components: {
    uploadFiles,
  },
  mounted() {
    if (!this.field.readonly) this.init();
  },
  beforeDestroy() {
    this.editor.trumbowyg('destroy');
  },

  watch: {
    'field.value'(newValue) {
      if (this.currentVal !== newValue) this.editor.trumbowyg('html', newValue);
    },
  },
  methods: {
    init: function () {
      this.editor = $(this.$refs.editor);
      this.editor
        .trumbowyg(this.config)
        .on('tbwchange', () => {
          // Отложенное сохраниение значения.
          clearTimeout(this.timerId);
          this.timerId = setTimeout(() => {
            this.currentVal = this.editor.trumbowyg('html');
            this.$emit('v-change', this.editor.trumbowyg('html'));
          }, 500);
        })
        .on('tbwinit', () => {
          // Устанавливаем начальное значение
          this.editor.trumbowyg('html', this.field.value);
        });
    },
    attachFiles: function (files, link) {
      var res = '';
      for (var file of files) {
        let code = '';
        if (file.type == 'image') {
          let img = `<img alt="" title="" src="${file.url}" data-file-id="${file.id}" />`;
          code = link ? `<a href="${file.url}">${img}</a> ` : img;
        } else {
          code = `<a href="${file.url}" data-file-id="${file.id}">${file.orig_name}</a>`;
        }

        res += `<p>${code}</p>\n`;
      }

      this.editor.trumbowyg('restoreRange');
      this.editor.trumbowyg('execCmd', {
        cmd: 'insertHtml',
        param: res,
      });
    },
    uploadFile: function (file) {
      this.attachFiles([file], false);
    },
  },
  computed: {
    config() {
      var config = {
        lang: 'ru',
        // changeActiveDropdownIcon: true,
        // Ширина картинки
        imageWidthModalEdit: true,
        // Очищаем цсс, изолируем от сайта
        resetCss: true,
        // Очищаем форматирование при вставке
        // removeformatPasted: true,

        // Запрещаем вставку изображений
        disablePasteImages: true,
        // Запрещаем перетаскивание
        disableDragAndDrop: true,

        // tagsToKeep: ['i', 'b', 'strong', 'a'],
        // Автовысота
        autogrow: true,
        // Редктор масштабируется при клике
        autogrowOnEnter: true,

        btnsDef: {
          insertImage: {
            fn: () => {
              this.emitter.emit('FileManagerModalShow', {
                type: 'all',
                showLink: true,
                return: this.attachFiles,
              });
              this.saveRange();
            },
            title: 'Вставить',
            ico: 'insertImage',
          },
          uploadImage: {
            fn: () => {
              this.$refs.uploadFiles.selectFiles();
              this.saveRange();
            },
            title: 'Загрузить',
            ico: 'upload',
          },
          dropdownInsertImage: {
            dropdown: ['insertImage', 'uploadImage'],
            title: 'Вставить изображение',
            ico: 'insertImage',
            hasIcon: true,
          },
        },
      };

      let insertImage = this.field.upload !== false ? 'dropdownInsertImage' : '';

      if (this.field.format == 'fool') {
        config.btns = [
          ['viewHTML'],
          ['undo', 'redo'], // Only supported in Blink browsers
          ['formatting'],
          ['strong', 'em', 'del'],
          ['superscript', 'subscript'],
          ['link'],
          [insertImage],
          ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
          ['unorderedList', 'orderedList'],
          ['horizontalRule'],
          ['removeformat'],
          ['fullscreen'],
        ];
      } else if (this.field.format == 'small') {
        config.btns = [
          ['viewHTML'],
          ['strong', 'em'],
          ['link'],
          [insertImage],
          ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
          ['removeformat'],
          ['fullscreen'],
        ];
      } else {
        config.btns = [
          ['viewHTML'],
          ['formatting'],
          ['strong', 'em'],
          ['link'],
          [image],
          ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
          ['removeformat'],
          ['fullscreen'],
        ];
      }

      return config;
    },
  },
  props: ['field'],
};
</script>

<style lang="scss">
.editor {
  .small-mce.small {
    .trumbowyg {
      &.trumbowyg-box {
        min-height: 100px !important;
      }

      .trumbowyg-editor {
        min-height: 100px !important;
        // user-drag: none;
        // -webkit-user-drag: none; /* For WebKit browsers */
      }

      &.trumbowyg-editor-visible .trumbowyg-textarea,
      &.trumbowyg-editor-hidden .trumbowyg-textarea {
        min-height: 100px;
      }
    }
  }

  .trumbowyg {
    margin: auto;

    .trumbowyg-button-pane {
      background: none;
    }
  }

  .trumbowyg-fullscreen {
    z-index: 25 !important;
  }
}
</style>
