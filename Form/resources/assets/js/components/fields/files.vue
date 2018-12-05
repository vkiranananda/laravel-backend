<template>
    <div>
        <div class="attached-files" >
            <div class="mb-3" v-if="сountAgain != 0">
                <show-uploads-button :config="uploadConf"></show-uploads-button>
            </div>
            <div class="conteiner">
                <draggable v-model="files" :options="{draggable:'.item'}">
                    <div v-for="(file, key) in files" class="media-file item" :class="field.type == 'gallery' ? 'image' : 'file'" :key="key">
                        <a href='#' v-on:click.prevent="del(file)" class="delete">&times;</a>
                        <div class="file-body" v-on:click.prevent="edit(file)">
                            <img :src="file.thumb" alt="" class="image">
                            <div class="text">{{ file.orig_name}}</div>
                        </div>
                    </div>
                </draggable>
            </div>
            <div class="clearfix"></div>
            <small class="form-text text-muted" v-if="сountAgain == 0">Привышен лимит. Для того что бы выбрать новый файл сначала удалить имеющийся</small>
        </div>
    </div>
</template>

<script>
    import draggable from 'vuedraggable'
    import showUploadsButton from '../uploads/show-uploads-button'

    export default {
        components: {
            draggable,
            'show-uploads-button': showUploadsButton
        },
        props: {
            field: {
                default: [],
           },
        },
        computed: {
            //Количество файлов доступное для загрузки
            сountAgain () {
                var res = undefined; //Унлимитед
                if (this.field['max-files'] != undefined) {
                    res = this.field['max-files'] - this.field.value.length;
                    if (res < 0) res = 0;
                }
                return res;
            },
            uploadConf () {
                return {
                    type: (this.field['type'] == 'files') ? 'all' : 'image',
                    count: this.сountAgain,
                    attach: this.attachFiles
                }
            },
            files: {
                get () { return this.field.value },
                set (files) { this.$emit('change', files) }
            }
        },
        watch: {
            //Хук на удаление файла
            'store.state.uploadForm.deleteFile': function(id) {
                var exist = false;
                var res = [];
                
                for (var file of this.files) {
                    if (id == file.id) {
                        exist = true;
                        continue;
                    }
                    res.push(file);
                }
                //Если элемент был делам событие change
                if (exist) this.$emit('change', res);
            }
        },
        methods: {
            // Добавляем файлы
            attachFiles (files) {
                var newValue = this.field.value.slice();
                for (var file of files) newValue.unshift(file)
                this.$emit('change', newValue);
                console.log(newValue);
            },
            del(file) {
                this.$delete(this.files, this.files.indexOf(file));
                window.onbeforeunload = $(document).formUnloadPage;
            },
            edit(file) { 
                this.$bus.$emit( 'UploadFilesEditModalShow', Object.assign({ 
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
        margin-bottom: 15px;
        .conteiner {
          display: inline;
        }
        .media-file{
            padding-right: 12px;
            margin-right: 6px;
            margin-bottom: 13px;
            float: left;
            position: relative;
            .file-body {
                cursor: pointer;
                width: 100px;
                height: 100px;
                border: solid 1px #eceeef;
            }
            .delete{
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
                .delete{
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
            float: left;
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
                float: left;
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