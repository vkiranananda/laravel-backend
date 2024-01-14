<template>
    <!--    <div class="small-mce" :class="field.size ? field.size : 'small'">-->
    <div ref="editor" class="editor-blocks border rounded-2 position-relative"></div>
    <!--    </div>-->
</template>


<script>
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import Quote from '@editorjs/quote';
import Marker from '@editorjs/marker';
// import CodeTool from '@editorjs/code';
import Delimiter from './libs/editorjs/plugins/delimiter'
import Delimiter2 from '@editorjs/delimiter'
import Table from '@editorjs/table';
import Underline from '@editorjs/underline';
import TextAlign from './libs/editorjs/plugins/text-align'
import BackendImage from './libs/editorjs/plugins/image'
import Paragraph from '@editorjs/paragraph'
import InlineCode from '@editorjs/inline-code'
import RawTool from '@editorjs/raw'

// import Embed from '@editorjs/embed'
// import Warning from '@editorjs/warning'

import formData from '../../store/form-data'

export default {
    editor: null,
    mounted() {
        formData.setFieldProp({
            fields: this.fields,
            name: this.field.name,
            property: 'saveMethod',
            value: this.saveData
        })
        this.editor = new EditorJS({
            holder: this.$refs.editor,
            logLevel: 'ERROR',

            inlineToolbar: ['link', 'marker', 'underline', 'bold', 'italic', 'inlineCode'],
            onChange: (api, event) => {
                formData.beforeClose()
            },
            data: this.field.value,
            tools: {
                header: {
                    class: Header,
                    tunes: ['TextAlign'],
                },
                paragraph: {
                    class: Paragraph,
                    tunes: ['TextAlign'],
                    preserveBlank: true,
                },
                image: {
                    class: BackendImage,
                    tunes: ['TextAlign'],
                },
                inlineCode: InlineCode,
                list: {
                    class: List,
                    inlineToolbar: true,
                },
                quote: {
                    class: Quote,
                    inlineToolbar: true,
                    config: {
                        quotePlaceholder: 'Введите цитату',
                        captionPlaceholder: 'Введите автора',
                    },
                },
                marker: Marker,
                // Глючит
                // code: CodeTool,
                delimiter: Delimiter,
// Чет не работает
                // embed: {
                //     class: Embed,
                //     inlineToolbar: true
                // },
                underline: Underline,
                table: {
                    class: Table,
                    tunes: ['TextAlign'],
                    inlineToolbar: true,
                },
                // warning: {
                //     class: Warning,
                //     inlineToolbar: true,
                //     config: {
                //         titlePlaceholder: 'Заголовок',
                //         messagePlaceholder: 'Текст',
                //     },
                // },
                raw: RawTool,
                TextAlign: {
                    class: TextAlign,
                    config: {
                        default: "left",
                        blocks: {
                            header: 'center',
                            list: 'right',
                            image: 'center'
                        }
                    },
                }
            },
            i18n: {
                messages: {
                    blockTunes: {
                        "delete": {
                            "Delete": "Удалить"
                        },
                        "moveUp": {
                            "Move up": "Переместить вверх"
                        },
                        "moveDown": {
                            "Move down": "Переместить вниз"
                        }
                    },

                    toolNames: {
                        "Text": "Параграф",
                        "Heading": "Заголовок",
                        "List": "Список",
                        "Warning": "Примечание",
                        "Checklist": "Чеклист",
                        "Quote": "Цитата",
                        "Code": "Код",
                        "Delimiter": "Разделитель",
                        "Raw HTML": "HTML-фрагмент",
                        "Table": "Таблица",
                        "Link": "Ссылка",
                        "Marker": "Маркер",
                        "Bold": "Полужирный",
                        "Italic": "Курсив",
                        "InlineCode": "Моноширинный",
                    },
                    ui: {
                        "blockTunes": {
                            "toggler": {
                                "Click to tune": "Нажмите, чтобы настроить",
                                "or drag to move": "или перетащите"
                            },
                        },
                        "inlineToolbar": {
                            "converter": {
                                "Convert to": "Конвертировать в"
                            }
                        },
                        "toolbar": {
                            "toolbox": {
                                "Add": "Добавить"
                            }
                        }
                    },
                    tools: {
                        "table": {
                            "With headings": "С заголовками",
                            "Without headings": "Баз заголовков",
                            "Add row above": "Добавить строку сверху",
                            "Add row below": "Добавить строку снизу",
                            "Delete row": "Удалить строку",
                            "Add column to left": "Добавить столбец слева",
                            "Add column to right": "Добавить столбец справа",
                            "Delete column": "Удалить столбец",
                        },

                        "quote": {
                          "Align Left": "Текст слева",
                          "Align Center": "Текст по центру"
                        },
                        "list": {
                            "Unordered": "Ненумерованный",
                            "Ordered": "Нумерованный"
                        },
                        "warning": { // <-- 'Warning' tool will accept this dictionary section
                            "Title": "Название",
                            "Message": "Сообщение",
                        },

                        /**
                         * Link is the internal Inline Tool
                         */
                        "link": {
                            "Add a link": "Вставьте ссылку"
                        },
                        /**
                         * The "stub" is an internal block tool, used to fit blocks that does not have the corresponded plugin
                         */
                        "stub": {
                            'The block can not be displayed correctly.': 'Блок не может быть отображен'
                        }
                    },
                }
            }
        });
    },

    methods: {
        saveData: async function () {
            let res = await this.editor.save()
            this.editor.render(res)
            return res
        },
        // attachFile: function (files, link) {
        //
        //     var res = ''
        //
        //     for (var file of files) {
        //         if (file.file_type == 'image') {
        //             let img = '<img alt="" title="" src="' + file.orig + '" data-id="' + file.id + '" />';
        //             res += (link) ? '<a href="' + file.orig + '">' + img + '</a> ' : img
        //         } else {
        //             res += (link) ? '<a href="' + file.orig + '">' + file.orig_name + '</a> ' : file.orig;
        //         }
        //
        //         res += ' '
        //     }
        //
        //     this.$refs.editor.el.trumbowyg('restoreRange');
        //     this.$refs.editor.el.trumbowyg('execCmd', {
        //         cmd: 'insertHtml',
        //         param: res
        //     });
        // },
    },

    props: ['field', 'fields'],
}


</script>

<style lang='scss'>
.editor-blocks {
    padding-right: 15px;
    padding-top: 15px;

    .codex-editor__redactor {
        padding-bottom: 20px !important;
    }

    .ce-toolbar__content {
        max-width: 100%;
    }

    .toolbar__actions, .ce-toolbar__actions {
        left: 10px;
        right: auto;
    }

    .ce-block__content {
        max-width: 100%;
        padding-left: 70px;
    }

    .cdx-quote__text {
        min-height: 80px;
    }
    .ce-rawtool__textarea {
        min-height: 100px;
    }
    //.ce-code__textarea {
    //    min-height: 70px;
    //    max-height: 330px;
    //}
}
</style>


