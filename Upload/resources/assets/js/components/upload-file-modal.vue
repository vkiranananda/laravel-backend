<template>
    <div>
        <modal id="UploadsModal" size="large" title="Медиа файлы">
            <upload-file selectable="false" ref="uploadedFiles"></upload-file>
            <div slot="footer">
                <label v-if="conf['fieldName'] == 'mce'" class="form-check-label mr-5 link-img"><input type="checkbox" class="form-check-input" v-model="origLink"> Создать ссылку на оригинал </label>
                <button type="button" class="btn btn-primary" @click="insertFiles()">Вставить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </modal>
    </div>
</template>

<script>
    export default {
        created () {
            this.$bus.$on('UploadFileModalButton', this.showModal)
        },
        beforeDestroy(){
            this.$bus.$off('UploadFileModalButton');
        },

        data() {
            return {
                origLink: false,
                conf: []
            }
        },
        methods: {
            showModal(conf)
            {
                $('#UploadsModal').modal('show');
                this.conf = conf;
            },
            insertFiles()
            {
               // var attachedRes = Object.assign({}, this.selectData);
                var files = this.$refs.uploadedFiles.files;
                var attachedFiles = [];

                $('#UploadsModal').modal('hide');

                for (var i = 0; i < files.length; i++) {
                    if(files[i].selected){
                        if(this.conf.fieldName == 'mce'){
                           if( files[i].file_type == 'image'){
                                var res = '<img alt="" src="'+files[i].orig+'" data-id="'+files[i].id+'" />';
                                if( this.origLink ) res = '<a href="'+files[i].orig+'">'+res+'</a> ';
                            } else {
                                var res = files[i].orig;
                                if( this.origLink ) res = '<a href="'+res+'">'+files[i].orig_name+'</a> ';
                                else res += ' ';
                            }
                            tinymce.activeEditor.insertContent(res);
                        }else {
                            attachedFiles.push(files[i]);
                        }
                    }
                }
                this.$refs.uploadedFiles.unselectFiles();
                if(attachedFiles.length > 0){
                    this.$bus.$emit('UploadAttacheFiles', this.conf.fieldName, attachedFiles);
                }
            },
        }
    }
</script>