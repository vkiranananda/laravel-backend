<template>
    <div ref="editor" class="editor-blocks border rounded-2 position-relative"></div>
</template>


<script>
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import Quote from '@editorjs/quote';
import Marker from '@editorjs/marker';
import Delimiter from './libs/editorjs/plugins/delimiter'
import Big from './libs/editorjs/plugins/big'

import Table from '@editorjs/table';
import Underline from '@editorjs/underline';
import TextAlign from './libs/editorjs/plugins/text-align'
import StyledText from './libs/editorjs/plugins/styled-text'
import Image from './libs/editorjs/plugins/image'
import InlineCode from '@editorjs/inline-code'
import RawTool from '@editorjs/raw'

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

        let config = {
            holder: this.$refs.editor,
            logLevel: 'ERROR',

            inlineToolbar: ['link', 'marker', 'underline', 'bold', 'italic', 'big', 'inlineCode'],
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
                    tunes: ['TextAlign'],
                },
                inlineCode: InlineCode,
                list: {
                    class: List,
                    inlineToolbar: true,
                },
                marker: Marker,
                underline: Underline,
                big: Big,
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
                            "Delete": "Удалить",
                            "Click to delete": "Нажмите для удаления"
                        },
                        "moveUp": {
                            "Move up": "Переместить вверх"
                        },
                        "moveDown": {
                            "Move down": "Переместить вниз"
                        },

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
                        "Underline": "Подчеркнутый",
                        "InlineCode": "Моноширинный",
                        "Big": "Увеличить размер",
                        "Image": "Картинка"
                    },
                    ui: {
                        "blockTunes": {
                            "toggler": {
                                "Click to tune": "Нажмите, чтобы настроить",
                                "or drag to move": "или перетащите"
                            },
                        },

                        "toolbar": {
                            "toolbox": {
                                "Add": "Добавить"
                            }
                        },
                        popover: {
                            Filter: "Фильтр",
                            "Nothing found": "Ничего не найдено",
                            "Convert to": "Конвертировать в"
                        },
                    },
                    tools: {
                        "header": {
                            "Header": "Заголовок",
                            "Heading 1": "Заголовок 1",
                            "Heading 2": "Заголовок 2",
                            "Heading 3": "Заголовок 3",
                            "Heading 4": "Заголовок 4",
                            "Heading 5": "Заголовок 5",
                            "Heading 6": "Заголовок 6",
                        },
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
                        "warning": {
                            "Title": "Название",
                            "Message": "Сообщение",
                        },
                        "link": {
                            "Add a link": "Вставьте ссылку"
                        },
                        "stub": {
                            'The block can not be displayed correctly.': 'Блок не может быть отображен'
                        },
                    },
                }
            }
        }

        const keyToConf = {
            'styled-text': {
                class: StyledText,
                langKey: 'Styled Text'
            },
            'table': {
                class: Table,
                langKey: 'Table'
            },
            'raw': {
                class: RawTool,
                langKey: 'Raw HTML'
            },
            'delimiter': {
                class: Delimiter,
                langKey: 'Delimiter'
            },
            'quote': {
                class: Quote,
                langKey: 'Quote'
            },
            'image': {
                class: Image,
                langKey: 'Image'
            },
        }

        if (this.field.plugins) {
            for (let pluginKey in this.field.plugins) {
                let conf = keyToConf[pluginKey]
                if (!conf) {
                    console.error(`editor-blocks: plugin "${pluginKey}" not found`)
                    continue
                }
                let plugin = this.field.plugins[pluginKey]
                if (plugin.label) config.i18n.messages.toolNames[conf.langKey] = plugin.label
                config.tools[pluginKey] = {}

                switch (pluginKey) {
                    case 'styled-text':
                        config.tools[pluginKey].config = {styles: plugin.styles}
                        break;
                    case 'quote':
                        config.tools[pluginKey].config = {
                            quotePlaceholder: 'Введите цитату',
                            captionPlaceholder: 'Введите автора',
                        }
                        break;
                    default:
                }
                config.tools[pluginKey].class = conf.class

                if (plugin['text-align'] === true) config.tools[pluginKey].tunes = ['TextAlign']
                if (plugin['toolbar'] === true) config.tools[pluginKey].inlineToolbar = true
            }
        }

        this.editor = new EditorJS(config);
    },

    methods: {
        saveData: async function () {
            let res = await this.editor.save()
            this.editor.render(res)
            return res
        }
    }
    ,

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
}
</style>


