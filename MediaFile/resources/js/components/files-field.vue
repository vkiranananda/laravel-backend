<template>
  <div class="files-field">
    <div class="mb-2 mt-1" v-if="!maxFiles && field['readonly'] !== true">
      <button
        v-if="field['insert-button'] !== false"
        type="button"
        class="text-button btn btn-secondary btn-sm me-2"
        @click="selectFiles()">
        Выбрать файл
      </button>
      <button
        v-if="field['upload-button'] !== false"
        type="button"
        class="text-button btn btn-secondary btn-sm"
        @click="uploadFilesEvent()">
        Загрузить файл
      </button>
    </div>
    <div class="card">
      <div class="card-body p-0">
        <upload-files ref="uploadFiles" :upload-url="field['upload-url']" @uploadFile="uploadFile">
          <file-list
            :files="field.value"
            :list-type="field['file-type'] === 'image' ? 'grid' : 'list'"
            :folder="false"
            :sortable="true"
            :cut="false"
            :copy="false"
            :upload="!maxFiles"
            @deleteFile="deleteFile"
            @uploadFiles="uploadFilesEvent"
            @renameFile="renameFileEvent"
            @sortable="sortable"
            ref="fileList" />
        </upload-files>
      </div>
    </div>
    <v-modal
      v-if="modalShow"
      :title="modalTitle"
      :centerAlign="true"
      :closeModal="false"
      class="file-manager__modal"
      ref="modal">
      <!-- Переименование файла -->
      <template v-if="formType === 'renameFile'">
        <div class="file-manager__modal-input">
          <input type="text" class="form-control" v-model="fileChange.name" />
        </div>
        <div class="file-manager__modal-error text-danger" v-if="errorText">{{ errorText }}</div>
      </template>
      <template #footer>
        <div class="text-end">
          <button class="btn btn-secondary me-1" @click="closeModal">Отменить</button>
          <template v-if="formType === 'renameFile'">
            <button class="btn btn-primary" @click="renameFile" role="submit">Переименовать</button>
          </template>
        </div>
      </template>
    </v-modal>
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
  emits: ['v-change'],
  props: {
    field: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      modalTitle: '',
      errorText: '',
      modalShow: false,
      fileChange: {},
    };
  },
  computed: {
    // Если false, то можно добавлять файлы
    maxFiles() {
      return this.field['max-files'] && this.field.value.length >= this.field['max-files'];
    },
  },
  methods: {
    sortable(oldIndex, newIndex) {
      let resFiles = this.field.value.slice();
      resFiles.splice(oldIndex, 1);
      resFiles.splice(newIndex, 0, this.field.value[oldIndex]);
      this.$emit('v-change', resFiles);
    },
    renameFileEvent(file) {
      this.modalTitle = 'Переименование файла';
      this.formType = 'renameFile';
      // Получаем расширение и имя файла отдельно, расширение с точкой
      const lastDotIndex = file.name.lastIndexOf('.');
      if (lastDotIndex > 0) {
        this.fileChange['name'] = file.name.substring(0, lastDotIndex);
        this.fileChange['ext'] = file.name.substring(lastDotIndex);
        this.fileChange['file'] = file;
      }

      this.showModal();
    },
    renameFile() {
      const newName = this.fileChange.name + this.fileChange.ext;

      // Если имя файла не изменилось, то закрываем модальное окно
      if (this.fileChange.file.name === newName) {
        this.closeModal();
        return;
      }

      // Проверяем, есть ли файл с таким же именем
      if (this.field.value.some(f => f.name === newName)) {
        this.errorText = 'Файл с таким именем уже существует.';
        return;
      }

      // Создаем новый массив с обновленным файлом
      let resFiles = this.field.value.slice();

      let changedFileId = resFiles.indexOf(this.fileChange.file);
      let changedFile = Object.assign({}, resFiles[changedFileId]);
      changedFile.name = newName;
      resFiles[changedFileId] = changedFile;

      this.$emit('v-change', resFiles);
      this.closeModal();
    },
    showModal() {
      this.modalShow = true;
      this.$nextTick(() => {
        this.$refs.modal.show();
      });
    },
    closeModal() {
      this.$refs.modal.hide();
      this.modalShow = false;
    },
    uploadFile(file) {
      if (this.maxFiles) return;
      if (this.field['file-type'] == 'image' && file.type != 'image') return;
      let newFiles = this.field.value.slice();
      newFiles.push(file);
      this.$emit('v-change', newFiles);
    },
    deleteFile(files) {
      let newFiles = this.field.value.slice();
      files.forEach(file => {
        newFiles = newFiles.filter(f => f.id !== file.id);
      });
      this.$emit('v-change', newFiles);
    },
    doubleClick(file) {
      this.files = this.files.filter(f => f.id !== file.id);
    },
    uploadFilesEvent() {
      this.$refs.uploadFiles.selectFiles();
    },

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
