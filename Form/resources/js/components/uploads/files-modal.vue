<template>
    <v-modal name="UploadModal" size="large" title="Медиа файлы">
        <upload-file ref="uploadedFiles" selectable="false" :url="url" :config="config" :method="method"
                     @select="setSelectedFiles"></upload-file>
        <template #footer>
            <label v-if="config.fieldType == 'mce' || config.showLink" class="form-check-label me-5 link-img"><input
                type="checkbox" class="form-check-input" v-model="origLink"> Создать ссылку на оригинал </label>
            <button type="button" class="btn btn-primary" @click="insertFiles()">Вставить</button>
            <button type="button" class="btn btn-secondary" @click="modal.hide('UploadModal')">Закрыть</button>
        </template>
    </v-modal>
</template>

<script>
import uploadFile from './upload-file.vue'

export default {
    //Создаем слушателей событий
    created() {
        this.emitter.on('UploadFilesModalShow', this.showModal)
    },
    beforeDestroy() {
        this.emitter.off('UploadFilesModalShow', this.showModal)
    },

    props: ['url'],
    components: {'upload-file': uploadFile},

    data() {
        return {
            config: {},
            origLink: false,
            lastReturn: false,
            selectedFiles: [],
            method: ''
        }
    },

    methods: {
        // Показываем окно
        showModal(config = {}) {
            this.config = config

            // Снимаем выделения если калбэк разный
            if (this.lastReturn != config.return) {
                //Если не первый вызов
                if (this.lastReturn) this.$refs.uploadedFiles.unselectFiles()
                this.lastReturn = config.return;
            }

            this.modal.show('UploadModal')
        },

        // Выбранные файлы
        setSelectedFiles(files) {
            this.selectedFiles = files
        },

        // Вставка файлов
        insertFiles() {
            this.modal.hide('UploadModal')

            if (this.selectedFiles.length > 0) {
                // Вызываем калбэк
                this.config.return(this.selectedFiles, this.origLink)

                //Убираем выделение после вставки
                this.$refs.uploadedFiles.unselectFiles()
            }
        }
    }
}
</script>
