<template>
  <div class="files-field">
    <div class="mb-2 mt-1">
      <button type="button" class="text-button btn btn-secondary btn-sm me-2" @click="selectFiles()">
        Выбрать файл
      </button>
      <button type="button" class="text-button btn btn-secondary btn-sm" @click="uploadFile()">Загрузить файл</button>
    </div>
    <div class="card">
      <div class="card-body p-0">
        <upload-files ref="uploadFiles" :upload-url="field['upload-url']" @uploadFile="uploadFile">
          <file-list
            :files="field.value"
            :list-type="field['file-type'] === 'image' ? 'grid' : 'list'"
            :folder="false"
            @deleteFile="deleteFile"
            @uploadFiles="uploadFile"
            ref="fileList" />
        </upload-files>
      </div>
    </div>
  </div>
</template>

<script>
import fileList from './file-list/index.js';
import uploadFiles from './upload-files/index.js';

export default {
  components: {
    fileList,
    uploadFiles,
  },
  name: 'FilesField',
  props: {
    field: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      files: this.field.value || [],
    };
  },
  methods: {
    uploadFile(file) {
      this.files.push(file);
    },
    downloadFile(file) {
      this.files.push(file);
    },
    deleteFile(file) {
      this.files = this.files.filter(f => f.id !== file.id);
    },
    doubleClick(file) {
      this.files = this.files.filter(f => f.id !== file.id);
    },
    uploadFiles(files) {},

    // Выбор файлов
    selectFiles(files) {
      this.emitter.emit('FileManagerModalShow', {
        return: this.insertFiles,
      });
    },

    // Вставка файлов
    insertFiles(files) {
      this.field['max-files'];

      let newFiles = this.field.value.slice();

      files.forEach(file => {
        // Проверка на максимальное количество файлов
        if (this.field['max-files'] && newFiles.length >= this.field['max-files']) {
          return;
        }
        // Проверка на тип файла
        if (this.field['file-type'] == 'image' && file.type != 'image') {
          return;
        }

        // Проверка на существование файла с таким же id
        if (newFiles.some(existingFile => existingFile.id === file.id)) {
          return;
        }

        newFiles.push(file);
      });

      // Если массив изменился, то вызываем событие change
      if (newFiles.length > this.field.value.length) {
        this.$emit('v-change', newFiles);
        console.log(newFiles.length);
      }
    },
  },
};
</script>

<style scoped>
.files-field {
  min-height: 40px;
}
</style>
