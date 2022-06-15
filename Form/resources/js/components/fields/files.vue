<template>
    <div class="attached-files">
        <div v-if="сountAgain != 0 && field.readonly != true">
            <show-uploads-button :config="config"></show-uploads-button>
        </div>
        <div class="conteiner">
            <div ref="listSortable" class="d-flex flex-row">
                <div v-for="file in files" class="media-file item" :key="file.id"
                     :class="field.type == 'gallery' ? 'image' : 'file'">
                    <a href='#' v-on:click.prevent="del(file)" class="delete"
                       v-if="field.readonly != true">&times;</a>
                    <div class="file-body" v-on:click.prevent="edit(file)">
                        <img :src="file.thumb" alt="" class="image">
                        <div class="text">{{ file.orig_name }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!--        <small class="form-text text-muted" v-if="сountAgain == 0">Привышен лимит. Для того что бы выбрать новый файл сначала удалить имеющийся</small>-->
    </div>
</template>

<script>
import Sortable from '../../../../../resources/js/libs/sortable'
import showUploadsButton from '../uploads/show-uploads-button'

const cloneDeep = require('clone-deep')

export default {
    mounted() {
        this._sortable = new Sortable(this.$refs.listSortable, {
            onEnd: (evt) => {
                let res = this.files.slice()
                let oldEl = res[evt.oldIndex]
                res[evt.oldIndex] = res[evt.newIndex]
                res[evt.newIndex] = oldEl
                console.log(res)
                this.$emit('v-change', res)
            }
        });
    },
    beforeUnmount() {
        if (this._sortable !== undefined) this._sortable.destroy();
    },
    created() {
        this.emitter.on('UploadFilesDeleteFile', this.delById)
    },
    beforeDestroy() {
        this.emitter.off('UploadFilesDeleteFile', this.delById)
    },
    components: {
        'show-uploads-button': showUploadsButton
    },
    props: ['field'],
    computed: {
        // Количество файлов доступное для загрузки
        сountAgain() {
            let res = undefined; // Унлимитед
            if (this.field['max-files'] != undefined) {
                res = this.field['max-files'] - this.field.value.length;
                if (res < 0) res = 0;
            }
            return res;
        },
        config() {
            return {
                type: (this.field['type'] == 'files') ? 'all' : 'image',
                count: this.сountAgain,
                return: this.attachFiles
            }
        },
        files() {
            return this.field.value
        },
    },
    methods: {
        // Добавляем файлы
        attachFiles(files) {
            let newValue = this.files.slice()
            for (let file of files) newValue.push(file)
            this.$emit('v-change', newValue)
        },
        // Когда файл удаляется полностью
        delById(id) {
            let exist = false
            let res = [];

            for (let file of this.files) {
                if (id == file.id) {
                    exist = true
                    continue
                }
                res.push(file)
            }
            // Если элемент был делаем событие change
            if (exist) this.$emit('v-change', {value: res, changed: false})
        },
        del(file) {
            let res = this.files.slice()
            res.splice(this.files.indexOf(file), 1)
            this.$emit('v-change', res)
        },
        edit(file) {
            this.emitter.emit('UploadFilesEditModalShow', Object.assign({
                deleteMethod: this.del,
                deleteValue: file,
                deleteType: 'unfasten'
            }, file))
        }
    }
}
</script>

<style lang='scss'>
.attached-files {
    .conteiner {
        display: inline;
    }

    .media-file {
        padding-right: 12px;
        margin-right: 6px;
        margin-top: 13px;
        position: relative;

        .file-body {
            cursor: pointer;
            width: 100px;
            height: 100px;
            border: solid 1px #eceeef;
        }

        .delete {
            position: absolute;
            right: -1px;
            top: -8px;
            font-size: 18px;
            text-align: center;
            color: red;
            text-decoration: none;
            display: none;
            cursor: pointer;
        }

        &:hover {
            .delete {
                display: inline-block;
            }
        }

        img {
            width: 100%;
            height: 100%;
        }
    }

    .image {
        .text {
            display: none;
        }
    }

    .file {
        position: relative;
        padding-right: 10px;
        margin-right: 21px;
        margin-bottom: 21px;
        white-space: nowrap;

        .file-body {
            width: auto;
            height: 32px;
            border: 0;
        }

        img {
            width: auto;
            height: 100%;
        }

        .text {
            overflow: hidden;
            width: 100%;
            font-size: 12px;
            padding: 1px;
            opacity: 0.7;
            display: inline-block;
            vertical-align: middle;
            margin: 6px 10px;
            max-width: 400px;

        }
    }
}
</style>
