<template>
    <vue-simplemde v-model="value" :configs="config" ref="editor"/>
</template>

<script>
import VueSimplemde from 'vue-simplemde'
import 'simplemde-theme-base/dist/simplemde-theme-base.min.css'
// FIXME Не работает отмена записи. Переменная value меняется, но на деле ничего не происходит. Видимо кэш какой то внутри самого компонента.
export default {
    methods: {
        attachFile: function (files, link) {

            var res = ''

            for (var file of files) {
                if (file.file_type == 'image') {
                    res += '![' + file.orig_name + '](' + file.orig + ')'
                } else {
                    res += (link) ? '[' + file.orig_name + '](' + file.orig + ')' : file.orig;
                }

                res += ' '
            }

            this.$refs.editor.simplemde.codemirror.replaceSelection(res);
        },
    },
    components: {VueSimplemde},
    data() {
        return {}
    },
    computed: {
        value: {
            get: function () {
                return this.field.value
            },
            set: function (text) {
                if (this.field.value != text) this.$emit('v-change', text)
            }
        },
        config() {
            var config = {
                renderingConfig: {
                    singleLineBreaks: true
                },
                parsingConfig: {
                    allowAtxHeaderWithoutSpace: true,
                    strikethrough: false,
                    underscoresBreakWords: true,
                },
                showIcons: ["code", "table"],
                toolbar: [
                    'bold', 'italic', 'strikethrough', '|', 'code', 'quote', 'unordered-list', 'ordered-list', '|', 'link',
                    {
                        name: "image",
                        action: (editor) => {
                            this.emitter.emit('UploadFilesModalShow', {
                                type: 'all',
                                showLink: true,
                                return: this.attachFile
                            })
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
    props: ['field'],
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


