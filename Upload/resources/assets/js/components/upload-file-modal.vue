<template>
	<div>
	   	<modal id="UploadsModal" size="large" title="Медиа файлы">
	   		<upload-file selectable="false" ref="uploadedFiles"></upload-file>
	        <div slot="footer">
	 			<label v-if="selectData.field == 'mce'" class="form-check-label mr-5 link-img"><input type="checkbox" class="form-check-input" v-model="origLink"> Создать ссылку на оригинал </label>
	            <button type="button" class="btn btn-primary" @click="insertFiles()">Вставить</button>
	            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	        </div>
	    </modal>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                origLink: false,
            }
        },
        computed: {
            selectData() { return this.$store.state.uploadStore.selectData },
        },
        methods: {
            insertFiles()
            {
                var attachedRes = Object.assign({}, this.selectData);
                var files = this.$refs.uploadedFiles.files;
                attachedRes['items'] = [];

                $('#UploadsModal').modal('hide');

                for (var i = 0; i < files.length; i++) {
                    if(files[i].selected){
                        if(this.selectData.field == 'mce'){
                           if( files[i].file_type == 'image'){
                                var res = '<img alt="" src="'+files[i].orig+'" data-id="'+files[i].id+'" />';
                                if( this.origLink ) res = '<a href="'+files[i].orig+'">'+res+'</a> ';
                            } else {
                                var res = files[i].orig;
                                if( this.origLink ) res = '<a href="'+res+'">'+files[i].orig_name+'</a> ';
                            }
                            tinymce.activeEditor.insertContent(res);
                        }else {
                            attachedRes.items.push(files[i]);
                        }
                    }
                }
                this.$store.commit('uploadStore/SetSelectData', attachedRes );
            },
        }
    }
</script>