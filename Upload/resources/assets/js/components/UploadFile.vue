<template>
	<div>
	    <div class='file-upload'>
	        <div data-name="errorsArea" v-html="errors"></div>
	        <div class='upload-button media-file' @click="chooseFiles()"></div>
	        <div class="conteiner">
	            <div class='media-file' v-for="(file, key, index) in files" :key="file.id" v-bind:class="{ 'have-errors': file.haveErrors, 'selected': file.selected }" @click="selectFile(file)">
	                <a href='#' v-on:click.stop="del(file)" class="delete-button">&times;</a>
	                <a href="#" v-on:click.stop="edit(file)" v-show="file.id" class="icons-pencil edit-button"></a>
	                <div data-type='imgCon'>
	                    <img :src="file.file_type == 'image' ? file.thumb_url : '/images/system/file.png'" alt="">
	                    <input type="hidden" name="_media-file-uploaded-id[]" :value="file.id" data-type='file-id'>
	                </div>
	                <div class="text" data-type="text" v-if="file.file_type != 'image'">{{ file.orig_name }}</div>
	                <progress v-show="!file.id && !file.haveErrors" class="progress progress-info" :value="file.progress" max="100"></progress>
	            </div>
	        </div>
	        <input type="file" @change="uploadFiles" class="hidden-xs-up" ref="upload" multiple>
	    </div>
	</div>
</template>

<script>
    export default {
    	mounted() {
            //Set data default
            this.$store.commit('uploadStore/ListFiles', this.data);
            this.$store.commit('uploadStore/Params', this.params);
    	},
        props: {
            params: {
                default: [],
                // type: Array
            },
            data: {
                default: [],
                // type: Array
           },
        },
        data() {
            return {
                errors: '',
            }
        },
        computed: {
            files() { return this.$store.state.uploadStore.files },
            editingFile() { return this.$store.state.uploadStore.editingFile },
            attachedFiles() { return this.$store.state.uploadStore.attachedFiles },
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
                        axios.post(this.params['upload-url'], data, config)
                            .then( (response) => {
                                this.$set(this.files, this.files.indexOf(fileInArr), response.data);
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
                                    this.$store.commit('uploadStore/DeleteFile', fileInArr);
                                }, 1000);
                            });  
                    })(files[i]);
                }   
            },
            selectFile: function (file) {
                this.$set(file, 'selected', (!file['selected']) );
            },
            //Изменяем элемент
            edit: function (file) {
                this.$store.commit('uploadStore/editingFile', file);
                $('#UploadEditModal').modal('show');
            },
            del: function (file) {
                if(!confirm('Файл "' + file['orig_name'] + '" будет удален безвозвратно. Удалить файл?'))return

                this.errors = "";
                this.$set(file, 'haveErrors', true);

                axios.delete(this.params['destroy-url'] + '/' + file['id'])
                    .then( (response) => {
                        this.$store.commit('uploadStore/DeleteFile', file);               
                       // Удаляем все элементы со страницы если они выбраны в галерее
                       
                        ///   $('[role=formAttachFiles] [data-type = item][data-id='+el.attr('data-id')+']').remove();

                    })
                    .catch( (error) => {
                        console.log(error.response);
                    });
            }
        }
    }
</script>