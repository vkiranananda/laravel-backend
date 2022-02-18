<template>
    <div class="small-mce" :class="field.size ? field.size : 'small'">
        <trumbowyg ref="editor" v-model="content" :config="config"></trumbowyg>
    </div>
</template>


<script>
import trumbowyg from 'vue-trumbowyg';
import 'trumbowyg/dist/ui/trumbowyg.css';
import 'trumbowyg/dist/langs/ru';
import fecha from "fecha";
// import './libs/trumbowyg-insert-image';
// import 'trumbowyg/plugins/resizimg/trumbowyg.resizimg';

export default {
    methods: {
        attachFile: function (files, link) {

            var res = ''

            for (var file of files) {
                if (file.file_type == 'image') {
                    let img = '<img alt="" title="" src="' + file.orig + '" data-id="' + file.id + '" />';
                    res += (link) ? '<a href="' + file.orig + '">' + img + '</a> ' : img
                } else {
                    res += (link) ? '<a href="' + file.orig + '">' + file.orig_name + '</a> ' : file.orig;
                }

                res += ' '
            }

            this.$refs.editor.el.trumbowyg('restoreRange');
            this.$refs.editor.el.trumbowyg('execCmd', {
                cmd: 'insertHtml',
                param: res
            });
        },
    },
    components: {trumbowyg},
    computed: {
        content: {
            get: function () {
                return this.field.value
            },
            set: function (content) {
                this.$emit('v-change', content)
            }
        },
        config() {
            var config = {
                lang: 'ru',
                // changeActiveDropdownIcon: true,
                // Ширина картинки
                imageWidthModalEdit: true,
                // Очищаем цсс, изолируем от сайта
                resetCss: true,
                // Очищаем форматирование при вставке
                removeformatPasted: true,
                // Автовысота
                autogrow: true,
                // Редктор масштабируется при клике
                autogrowOnEnter: true,

                btnsDef: {
                    insertImage: {
                        fn: () => {
                            console.log('123')
                            this.emitter.emit('UploadFilesModalShow', {type: 'all', showLink: true, return: this.attachFile})
                            this.saveRange()
                        },
                        ico: 'insertImage'
                    }
                },
            }

            let image = (this.field.upload) ? 'insertImage' : ''

            if (this.field.format == 'fool') {
                config.btns = [
                    ['viewHTML'],
                    ['undo', 'redo'], // Only supported in Blink browsers
                    ['formatting'],
                    ['strong', 'em', 'del'],
                    ['superscript', 'subscript'],
                    ['link'],
                    [image],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['unorderedList', 'orderedList'],
                    ['horizontalRule'],
                    ['removeformat'],
                    ['fullscreen']
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
                    ['fullscreen']
                ];
            }

            return config

        }
    },
    props: ['field'],
}


</script>

<style lang='scss'>
.small-mce.small {
    .trumbowyg {
        &.trumbowyg-box {
            min-height: 100px !important;
        }

        .trumbowyg-editor, {
            min-height: 100px !important;
        }

        &.trumbowyg-editor-visible .trumbowyg-textarea, &.trumbowyg-editor-hidden .trumbowyg-textarea {
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


</style>


