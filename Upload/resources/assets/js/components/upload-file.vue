<template>
    <div class='upload-file'>
        <div data-name="errorsArea" v-html="errors"></div>
        <div class='upload-button media-file' @click="chooseFiles()"></div>
        <div class="conteiner">
            <div class='media-file' v-for="(file, key, index) in files" :key="file.id">
                <a href='#' v-on:click.prevent="del(file)" class="delete">&times;</a>
                <a href="#" v-on:click.prevent="edit(file)" v-show="file.id" class="icons-pencil edit"></a>

                <div :class="{ 'have-errors': file.haveErrors, 'selected': file.selected }" class="media-file-body" @click="selectFile(file)">
                    <img :src="file.thumb" alt="">
                    <input v-if="file.newUploadedFile" type="hidden" name="_media-file-uploaded-id[]" :value="file.id" />
	                <div class="text" data-type="text" v-if="file.file_type != 'image'">{{ file.orig_name }}</div>
	                <progress v-show="!file.id && !file.haveErrors" class="progress progress-info" :value="file.progress" max="100"></progress>
                </div>
            </div>
        </div>
        <input type="file" @change="uploadFiles" class="hidden-xs-up" ref="upload" multiple>
    </div>
</template>

<script>
    export default {
        props: {
            params: { default: function () { return [] } },
            data: { default: function () { return [] } },
            selectable: { default: false }
        },
        conf: this.params,
        dataUrl: '',
        data() {
            return {
                files: this.data,
                errors: '',
            }
        },
        computed: {
            selectData() { return this.$store.state.uploadStore.selectData },
        },
        watch: {
            //Очищаем все выбраные элементы
            selectData: function() {
                if (this.dataUrl != this.selectData.url) {
                    axios.get(this.selectData.url)
                    .then( (response) => {
                        this.conf = response.data.params;
                        this.files = response.data.data;
                        this.dataUrl = this.selectData.url;
                    })
                    .catch( (error) => {
                        console.log(error.response);
                    });      
                }
                for (var i = 0; i < this.files.length; i++) {
                    if(this.files[i].selected)this.files[i].selected = false;
                }
            }
        },
        methods: {
            chooseFiles: function ()
            {
                this.$refs.upload.click();
            },
            uploadFiles: function ()
            {
                var files = this.$refs.upload.files;
                this.errors = "";
                //выходим если пусто
                if(files[0] === undefined){
                    return;
                }
                //выводим файлы для загрузки.
                for (var i  = files.length - 1; i >= 0; i--) {
                    this.files.unshift({progress: 0, orig_name: files[i]['name'], haveErrors: false });

                    ( (file) => {
                        var fileInArr = this.files[0];
                        var data = new FormData();

                        data.append('file', file);

                        var config = {
                            onUploadProgress: (progressEvent) => {
                                fileInArr['progress'] = 
                                Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                            }
                        }
                        axios.post(this.conf['upload-url'], data, config)
                            .then( (response) => {
                                var indexFile = this.files.indexOf(fileInArr);
                                response.data.newUploadedFile = true;
                                this.$set(this.files, indexFile, response.data);
                                this.selectFile(this.files[indexFile]);
                            })
                            .catch( (error) => {
                                console.log(error.response);
                                var errors = fileInArr['orig_name'] + ": "; 
                                for(var prop in error.response.data.errors['file']) {
                                    errors +=  " " + error.response.data.errors['file'][prop];
                                }
                                this.errors += errors + "<br>";
                                
                                this.$set(fileInArr, 'haveErrors', true);
                                setTimeout( () => {
                                    this.$delete(this.files, this.files.indexOf(fileInArr) );
                                }, 1000);
                            });  
                    })(files[i]);
                }   
            },
            selectFile: function (file) {
                if(this.selectable){ 
                    if( (this.selectData.type == 'image' && file.file_type == 'image') ||  this.selectData.type == 'all'){
                        this.$set(file, 'selected', (!file['selected']) );
                    }
                }
            },
            //Выводит список файлов
            insertFiles: function (type) {
                var res = Object.assign({}, this.selectData);
                res.items = [];
                for (var i = 0; i < this.files.length; i++) {
                    if(this.files[i].selected){
                        res.items.push(this.files[i]);
                    }
                }
                this.$store.commit('uploadStore/SetSelectData', res );
            },
            //Изменяем элемент
            edit: function (file) {
                this.$store.commit('uploadStore/EditFile', file.id );
                $('#UploadEditModal').modal('show');
            },
            del: function (file) {
                if(!confirm('Файл "' + file['orig_name'] + '" будет удален безвозвратно. Удалить файл?'))return

                this.errors = "";
                this.$set(file, 'haveErrors', true);

                axios.delete(this.conf['destroy-url'] + '/' + file['id'])
                    .then( (response) => {
                        this.$store.commit('uploadStore/DeleteFile', file.id );
                        this.$delete(this.files, this.files.indexOf(file) );
                    })
                    .catch( (error) => {
                        console.log(error.response);
                    });
            }
        }
    }
</script>

<style lang='scss'>
    .upload-file {
        margin-bottom: 15px;

        .conteiner {
            display: inline;
        }

        .media-file {
            width: 150px;
            height: 150px;
            float: left;
            margin-right: 16px;
            margin-bottom: 11px;
            position: relative;
            vertical-align: top;
            .delete{
                position: absolute;
                right: 0;
                top: -7px;
                display: inline-block;
                text-align: center;
                color: red;
                text-decoration: none;
                border-radius: 13px;
                font-size: 22px;
                display: none;
            }
            .edit{
                position: absolute;
                right: 0;
                top: 17px;
                display: inline-block;
                text-align: center;
                color: blue;
                text-decoration: none;
                border-radius: 13px;
                font-size: 12px;
                display: none;
            }
            &:hover{
                .delete, .edit {
                    display: inline-block;
                }
            }
            .media-file-body {
                width: 130px;
                height: 130px;
                position: relative;
                border: solid 1px #eceeef;
                cursor: pointer;
                img {
                    width: 100%;
                    height: 100%;
                }

                .text {
                    position: absolute;
                    bottom: 0px;
                    left: 0px;
                    text-align: center;
                    max-height: 74px;
                    overflow: hidden;
                    background-color: white;
                    width: 100%;
                    word-wrap: break-word;
                    font-size: 12px;
                    padding: 1px;
                    opacity: 0.7;
                }
                progress{
                    width: 118px;
                    position: absolute;
                    bottom: 4px;
                    left: 0px;
                    height: 12px;
                    margin: 4px 5px;
                    background-color: white !important;
                    color: #0275d8  !important;
                }
                &.have-errors{
                    border: solid 1px red;
                }
                &.selected{
                    -webkit-box-shadow: 0px 0px 11px 3px rgba(2,117,216,1);
                    -moz-box-shadow: 0px 0px 11px 3px rgba(2,117,216,1);
                    box-shadow: 0px 0px 11px 3px rgba(2,117,216,1);
                }
            }
        }

        .upload-button {
            background: url(/images/system/file_add.png) no-repeat;
            background-size: contain;
            cursor: pointer;
            border: none;
            margin: 0px 20px 10px 15px;
            width: 130px;
            height: 140px;
        }
    }
    #UploadsModal {
        .modal-lg {
            max-width: 868px;
        }
    }
</style>