<template>
    <div>
        <modal id="UploadModal" size="large" title="Медиа файлы">
            <upload-file selectable="false" ref="uploadedFiles" :url="url" :config="config"></upload-file>
            <div slot="footer">
                <label v-if="config.fieldType == 'mce'" class="form-check-label mr-5 link-img"><input type="checkbox" class="form-check-input" v-model="origLink"> Создать ссылку на оригинал </label>
                <button type="button" class="btn btn-primary" @click="insertFiles()">Вставить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </modal>
    </div>
</template>

<script>
    import uploadFile from './upload-file'

    export default {
        //Создаем слушателей событий
        created () { this.$bus.$on('UploadFilesModalShow', this.showModal) },
        beforeDestroy() { this.$bus.$off('UploadFilesModalShow') },

        props: [ 'url' ],
        components: { 'upload-file': uploadFile },
        data() { return { origLink: false, lastFieldKey: '' } },
        computed: {
            config () { return this.store.state.uploadForm.filesUploadConfig },
            //Получаем список методов для работы с дочерним компонентом
            uMethods () { return this.store.state.uploadForm.methods },
        },
        watch: {
            //Показываем модальное окно если передан нормальный конфиг
            config: function (config) { 

            }
        },
        methods: {
            //Показываем окно
            showModal () {
                // Снимаем выделения
                if (this.lastFieldKey != this.config.fieldKey) {
                    //Если не первый вызов
                    if (this.lastFieldKey != '') this.uMethods.unselectFiles();
                    this.lastFieldKey = this.config.fieldKey;
                }
                $('#UploadModal').modal('show');
            },
            // Вставка файлов
            insertFiles()
            {
                var files = this.uMethods.getSelectedFiles();
                
                $('#UploadModal').modal('hide');

                //Вставка в редактор
                if (this.config.fieldType == 'mce') {
                    for ( var file of files) {
                       if (file.file_type == 'image') {
                            var res = '<img alt="" src="'+file.orig+'" data-id="'+file.id+'" />';
                            if( this.origLink ) res = '<a href="'+file.orig+'">'+res+'</a> ';
                        } else {
                            var res = file.orig;
                            if( this.origLink ) res = '<a href="'+res+'">'+file.orig_name+'</a> ';
                            else res += ' ';
                        }
                        tinymce.activeEditor.insertContent(res);
                    }
                } else this.config.attach(files); //Для аттачей

                //Убираем выделение после вставки
                this.uMethods.unselectFiles();
            }
        }
    }
</script>