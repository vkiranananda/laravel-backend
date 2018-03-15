<template>
    <form :action="saveUrl" role="formAjax" method="POST">
        <modal id='UploadEditModal' title="Свойства файла" :loading="loading">
            <fields-list v-if="fields" :data="fields" ></fields-list>
            <div slot="footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" role="submit" data-send-text="Сохраняю..." v-on:click="closeForm">Сохранить</button>
            </div>
        </modal>
    </form>
</template>

<script>
    export default {
        created () {
            this.$bus.$on('UploadEditFile', this.getData);
        },
        beforeDestroy(){
            this.$bus.$off('UploadEditFile');
        },
        props: [ 'url' ],
        fileId: '',
        data() {
            return { 
                fields: false,
                loading: true,
                saveUrl: ''
            }
        },
        methods: {
            getData(id) {
                $('#UploadEditModal').modal('show');

                if(id == this.fileId) return;

                this.loading = true;
                axios.get(this.url + '/' + id )
                    .then( (response) => {
                        this.fileId = id;
                        this.saveUrl = response.data.saveUrl + '/' + id;
                        this.fields = response.data.fields;
                        this.loading = false;
                    })
                    .catch( (error) => {
                        console.log(error.response.data);
                    }); 
            },
            closeForm() {
                $('#UploadEditModal').modal('hide');
            }
        }
    }
</script>