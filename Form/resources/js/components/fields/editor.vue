<template>
    <div class="small-mce" :class="field.size ? field.size : 'small'">
        <div ref="editor"></div>
    </div>
</template>


<script>
function loadScript(url, callback) {
    const script = document.createElement('script'); // Создаем новый элемент <script>
    script.type = 'text/javascript'; // Устанавливаем тип
    script.src = url; // Указываем путь к файлу

    // Если у вас есть функция обратного вызова, которая должна быть вызвана после загрузки скрипта
    script.onload = function () {
        if (callback) callback();
    };

    // Добавляем элемент <script> в <head> или <body>
    document.head.appendChild(script);
}

function loadCSS(url, callback) {
    const link = document.createElement('link'); // Создаем новый элемент <link>
    link.rel = 'stylesheet'; // Устанавливаем атрибут rel
    link.type = 'text/css'; // Устанавливаем тип
    link.href = url; // Указываем путь к файлу

    // Если у вас есть функция обратного вызова, которая должна быть вызвана после загрузки стилей
    link.onload = function () {
        if (callback) callback();
    };

    // Добавляем элемент <link> в <head>
    document.head.appendChild(link);
}

export default {
    mounted() {
        // if (window.trumbowygLoad === true) {
            this.init()
        // } else {
        //     window.trumbowygLoad = true
        //     loadCSS('/js/trumbowyg/ui/trumbowyg.min.css')
        //     loadScript('/js/trumbowyg/trumbowyg.min.js', () => {
        //         loadScript('/js/trumbowyg/langs/' + this.config.lang + '.min.js', () => {
        //             window.trumbowygLoad = true
        //             console.log('init editor')
        //             this.init()
        //         })
        //     })
        // }
    },
    beforeDestroy() {
        this.editor.trumbowyg('destroy');
    },

    watch: {
        'field.value'(newValue) {
            if (this.currentVal !== newValue) this.editor.trumbowyg('html', newValue);
        }
    },
    methods: {
        init: function () {
            this.editor = $(this.$refs.editor)
            this.editor.trumbowyg(this.config).on('tbwchange', () => {
                // Отложенное сохраниение значения.
                clearTimeout(this.timerId)
                this.timerId = setTimeout(() => {
                    this.currentVal = this.editor.trumbowyg('html')
                    this.$emit('v-change', this.editor.trumbowyg('html'))
                }, 500)
            }).on('tbwinit', () => {
                // Устанавливаем начальное значение
                this.editor.trumbowyg('html', this.field.value)
            });

        },
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

            this.editor.trumbowyg('restoreRange');
            this.editor.trumbowyg('execCmd', {
                cmd: 'insertHtml',
                param: res
            });
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
                removeformatPasted: true,

                // tagsToKeep: ['i', 'b', 'strong', 'a'],
                // Автовысота
                autogrow: true,
                // Редктор масштабируется при клике
                autogrowOnEnter: true,

                btnsDef: {
                    insertImage: {
                        fn: () => {
                            this.emitter.emit('UploadFilesModalShow', {
                                type: 'all',
                                showLink: true,
                                return: this.attachFile
                            })
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
            } else if (this.field.format == 'small') {
                config.btns = [
                    ['viewHTML'],
                    ['strong', 'em'],
                    ['link'],
                    [image],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
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


