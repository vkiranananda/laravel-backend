<template>
  <v-modal name="UploadModal" size="large" title="Медиа файлы">
    <file-manager ref="fileManager" :modal="true" :list-url="listUrl" @selectFiles="setSelectedFiles"></file-manager>
    <template #footer>
      <label v-if="config.showLink" class="form-check-label me-5 link-img"
        ><input type="checkbox" class="form-check-input" v-model="origLink" /> Создать ссылку на оригинал
      </label>
      <button type="button" class="btn btn-primary" @click="insertFiles()" :disabled="!selectedFiles.length">
        Вставить
      </button>
      <button type="button" class="btn btn-secondary" @click="modal.hide('UploadModal')">Закрыть</button>
    </template>
  </v-modal>
</template>

<script>
import fileManager from './file-manager/index.js';

export default {
  //Создаем слушателей событий
  created() {
    this.emitter.on('FileManagerModalShow', this.showModal);
  },
  beforeDestroy() {
    this.emitter.off('FileManagerModalShow', this.showModal);
  },

  props: {
    listUrl: {
      type: String,
      required: true,
    },
  },
  components: { 'file-manager': fileManager },

  data() {
    return {
      config: {},
      origLink: false,
      lastReturn: false,
      selectedFiles: [],
    };
  },

  methods: {
    // Показываем окно
    showModal(config = {}) {
      this.config = config;

      // Снимаем выделения если калбэк разный
      if (this.lastReturn != config.return) {
        // Если не первый вызов
        if (this.lastReturn) this.$refs.fileManager.clearSelectedFiles();
        // Если первый вызов, то получаем файлы
        else this.$refs.fileManager.getFiles();
        this.lastReturn = config.return;
      }

      this.modal.show('UploadModal');
    },

    // Выбранные файлы
    setSelectedFiles(files) {
      this.selectedFiles = files;
    },

    // Вставка файлов
    insertFiles() {
      this.modal.hide('UploadModal');

      if (this.selectedFiles.length > 0) {
        // Вызываем калбэк
        this.config.return(this.selectedFiles, this.origLink);

        // Убираем выделение после вставки
        this.$refs.fileManager.clearSelectedFiles();
      }
    },
  },
};
</script>
