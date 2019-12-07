<template>
    <div class='upload-file'>
        <div class="loader text-center" v-if="loading">
            <img src="/backend/images/loading5.gif" alt="">
        </div>
        <div v-else>
            <div data-name="errorsArea" v-html="errors"></div>
            <div class='upload-button media-file' @click="chooseFiles()"></div>
            <div class="conteiner">
                <div class='media-file' v-for="file in files" :key="file.id">
                    <a href='#' v-on:click.prevent="del(file)" class="delete">&times;</a>
                    <a href="#" v-on:click.prevent="edit(file)" v-show="file.id" class="icons-pencil edit"></a>

                    <div :class="{ 'have-errors': file.haveErrors, 'selected': file.selected }" class="media-file-body" @click="selectFile(file)">
                        <img :src="file.thumb" alt="">
    	                <div class="text" data-type="text" v-if="file.file_type != 'image'">{{ file.orig_name }}</div>
    	                <progress v-show="!file.id && !file.haveErrors" class="progress progress-info" :value="file.progress" max="100"></progress>
                    </div>
                </div>
            </div>
            <input type="file" @change="uploadFiles" class="d-none" ref="upload" multiple>
        </div>
    </div>
</template>

<script>
    export default {
        props: [ 'url', 'config' ],
        data() {
            return {
                errors: '',
                loading: false,
                selectedItems: [],
                urls: [],
                files: [],
                loadedUrl: '',
            }
        },
        watch: {
            // Инитим данные при изменении переменной
            config: function (config) { 
                // Если данные не загружались, загружаем
                if (this.loadedUrl != this.url) {
                    this.loading = true;
                    axios.get(this.url)
                    .then( (response) => {
                        this.urls = response.data.urls
                        this.files = response.data.files
                        this.loading = false

                        // Если запись клонируется...
                        if (response.data.clone) {
                            // Узаываем что это не наш файл, удаление будет происходить только локально
                            for (let file of this.files) file.clone = true 
                        }
                    })
                    .catch( (error) => { console.log(error.response) });     

                    this.loadedUrl = this.url;
                }
            },
        },
        methods: {
            // Выбираем файлы
            chooseFiles() { this.$refs.upload.click() },
            
            // Комитим загруженные файлы
            addUploadedFile(id) { this.store.commit('editForm/addUploadFile', id ) },
            delUploadedFile(id) { this.store.commit('editForm/delUploadFile', id ) },

            unselectFiles() {
                for (var file of this.files) if (file.selected) file.selected = false;
                this.selectedItems = [];
                this.emitSelect()
            },
            emitSelect() {
                // Создаем событие со списком выбранных файлов
                let selFiles = [];
                for (let file of this.files) if (file.selected) selFiles.push(file)
                this.$emit('select', selFiles)      
            },
            //Загружаем файлы
            uploadFiles() {
                var files = this.$refs.upload.files;
                this.errors = "";

                //выходим если пусто
                if(files[0] === undefined) return;

                //выводим файлы для загрузки.
                for (var i  = files.length - 1; i >= 0; i--) {
                    this.files.unshift({progress: 0, orig_name: files[i]['name'], haveErrors: false });

                    ((file) => {
                        var fileInArr = this.files[0];
                        var data = new FormData();

                        data.append('file', file);

                        var config = {
                            onUploadProgress: (progressEvent) => {
                                fileInArr['progress'] = 
                                Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                            }
                        }
                        axios.post(this.urls.upload, data, config)
                            .then( (response) => {
                                //Копируем нужные атрибуты
                                for (var key in response.data) fileInArr[key] = response.data[key];
                                this.selectFile(fileInArr)
                                
                                // Комитим новые файлы
                                this.addUploadedFile(fileInArr.id)
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

            // Выбираем файл
            selectFile(file) {
                if( (this.config.type == 'image' && file.file_type == 'image') ||  this.config.type == 'all') {

                    if (file.selected) {
                        //Удаляем из выбранных элементов
                        this.selectedItems.splice(this.selectedItems.indexOf(file.id), 1);
                        file.selected = false;
                    } else {
                        // console.log('set');
                        //Снимаем пометку с самого первого элемента, если лимит исчерпан
                        if ( this.config.count != undefined && this.config.count <= this.selectedItems.length) {
                            //Ищем индекс в списке файлов и снимаем его.
                            var lastEl = this.selectedItems.length - 1;
                            for ( var fileSearch of this.files ){
                                if (fileSearch.id == this.selectedItems[lastEl]) fileSearch.selected = false;
                            }
                            this.selectedItems.splice(lastEl, 1);
                        }
                        //Добавляем элемент.
                        if ( this.config.count == undefined || this.config.count > this.selectedItems.length)  {
                            this.selectedItems.push(file.id);
                            this.$set(file, 'selected', true);
                        }
                    }
                    this.emitSelect()
                }
            },
            // Изменяем элемент
            edit(file) { 
                this.$bus.$emit( 'UploadFilesEditModalShow', Object.assign({ 
                    deleteMethod: this.del,
                    deleteValue: file,
                    deleteType: 'delete'
                }, file))
            },
            
            // Удаляем файл
            del(file) {

                if (!confirm('Вы уверены что хотите удалить файл: "' + file['orig_name'] + '"?')) return

                // Снимаем выделение если было.
                if (file['selected']) this.selectFile(file);

                // Очищаем ошибки
                this.errors = "";

                // Подсвечиваем удаляемый файл
                this.$set(file, 'haveErrors', true);

                var deleteFile = () => {
                    // Оповестить всех что файл удален
                    this.$bus.$emit('UploadFilesDeleteFile', file.id);
                    
                    // Удаляем из новозагруженных файлов
                    this.delUploadedFile(file.id)
                    
                    // Удаляем из списка
                    this.$delete(this.files, this.files.indexOf(file));
                }

                if (file.clone) deleteFile()

                else {
                    // Удаляем
                    axios.delete(this.urls.destroy + '/' + file['id'])
                        .then( (response) => {
                            deleteFile()
                        })
                        .catch( (error) => { console.log(error.response) })
                }
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
            background: url(/backend/images/file_add.png) no-repeat;
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