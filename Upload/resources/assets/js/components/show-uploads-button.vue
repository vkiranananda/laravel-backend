<template>
	<div class="show-uploads-button">
        <button v-if="type == 'text'" type="button" class="text-button btn btn-secondary btn-sm mb-1" @click="show()">Выбрать файл</button>
        <div v-else class='image-button' @click="show()"></div>
    </div>
</template>

<script>
    export default {
        props: {
        	type: {	default: 'text' },
        	url: {},
            'field-name': { default: 'mce' },
            'data-type': { default: 'all' },
        },
        methods: {
            show(fileType) {
                $('#UploadsModal').modal('show');
                if(this.$store.state.uploadStore.selectData.field != this.fieldName){
                    this.$store.commit('uploadStore/SetSelectData', { 
                        type: this.dataType, 
                        field: this.fieldName, 
                        items: [], 
                        url: this.url,
                        delete: false 
                    });
                }
            },
        }
    }
</script>

<style lang='scss'>
    .show-uploads-button {
    	.image-button{
	        background: url(/images/system/file_add.png) no-repeat;
	        background-size: cover;
	        cursor: pointer;
	        border: none;
	    }
    }
</style>