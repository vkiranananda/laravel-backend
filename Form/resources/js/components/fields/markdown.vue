<template>
    <div class="vue-simplemde">
        <textarea ref="simplemde"/>
    </div>
</template>

<script>
// import SimpleMDE from './libs/simplemde/js/simplemde'
// import './libs/simplemde/scss/simplemde-theme-base.scss'
export default {
    props: ['field'],

    mounted() {
        console.log(this.$refs.simplemde)

        let img = 'image'
        if (this.field.upload) {
            img = {
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
            }
        }

        this._simplemde = new SimpleMDE({
            element: this.$refs.simplemde,
            renderingConfig: {
                singleLineBreaks: true
            },
            initialValue: this.field.value,
            parsingConfig: {
                allowAtxHeaderWithoutSpace: true,
                strikethrough: false,
                underscoresBreakWords: true,
            },
            showIcons: ["code", "table"],
            toolbar: ['bold', 'italic', 'strikethrough', '|', 'code', 'quote', 'unordered-list', 'ordered-list', '|', 'link', img, 'table', '|', 'preview', 'side-by-side', 'fullscreen', '|', 'guide'],
        })

        this._simplemde.codemirror.on("change", () => {
            this.$emit('v-change', this._simplemde.value())
        });
    },
    beforeUnmount() {
        this._simplemde = null
    },

    watch: {
        'field.value': function (val) {
            console.log('dfdf')
            if (val != this._simplemde.value()) this._simplemde.value(val);
        },
        lastName: function (val) {
            this.fullName = this.firstName + ' ' + val
        }
    },

    methods: {
        attachFile: function (files, link) {

            let res = ''

            for (let file of files) {
                if (file.file_type == 'image') {
                    let img = '![' + file.orig_name + '](' + file.orig + ')'
                    res += (link) ? '[' + img + '](' + file.orig + ')' : img
                } else {
                    res += (link) ? '[' + file.orig_name + '](' + file.orig + ')' : file.orig;
                }

                res += ' '
            }
            this._simplemde.codemirror.replaceSelection(res);
        },
    },
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


