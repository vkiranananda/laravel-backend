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
                console.log(this.selectData);
            	var data = this.$refs.uploadedFiles.insertFiles();
            	$('#UploadsModal').modal('hide');
            },
        }
    }
</script>