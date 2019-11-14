<template>
    <modal id="UploadModal" size="large" title="Медиа файлы">
        <upload-file selectable="false" ref="uploadedFiles" :url="url" :config="config"></upload-file>
        <div slot="footer">
            <label v-if="config.fieldType == 'mce' || config.showLink" class="form-check-label mr-5 link-img"><input type="checkbox" class="form-check-input" v-model="origLink"> Создать ссылку на оригинал </label>
            <button type="button" class="btn btn-primary" @click="insertFiles()">Вставить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        </div>
    </modal>
</template>

<script>
    import uploadFile from './upload-file'

    export default {
        //Создаем слушателей событий
        created () { this.$bus.$on('UploadFilesModalShow', this.showModal) },
        beforeDestroy() { this.$bus.$off('UploadFilesModalShow') },

        props: [ 'url' ],
        components: { 'upload-file': uploadFile },
        data() { return { config: {}, origLink: false, lastReturn: false } },
        computed: {
            // Получаем список методов для работы с дочерним компонентом
            uMethods () { return this.store.state.uploadForm.methods },
        },
        methods: {
            // Показываем окно
            showModal (config = {}) {
                this.config = config
               
                // Снимаем выделения если калбэк разный
                if (this.lastReturn != config.return) {
                    //Если не первый вызов
                    if (this.lastReturn) this.uMethods.unselectFiles();
                    this.lastReturn = config.return;
                }

                $('#UploadModal').modal('show');
            },

            // Вставка файлов
            insertFiles()
            {
                $('#UploadModal').modal('hide')

                // Вызываем калбэк
                this.config.return(this.uMethods.getSelectedFiles(), this.origLink)

                //Убираем выделение после вставки
                this.uMethods.unselectFiles();
            }
        }
    }
</script>