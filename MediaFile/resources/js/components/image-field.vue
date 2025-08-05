<template>
  <div class="image-field">
    <div class="card">
      <upload-files ref="uploadFiles" :upload-url="field['upload-url']" @uploadFile="uploadFile">
        <div class="card-body image-field__card-body p-0">
          <div class="image-field__actions">
            <button class="image-field__action-button btn btn-light btn-sm" @click="selectFiles">
              <v-icon name="image" />
            </button>
            <button class="image-field__action-button btn btn-light btn-sm" @click="uploadFilesEvent">
              <v-icon name="upload" />
            </button>
            <button
              class="image-field__action-button btn btn-light btn-sm"
              @click="deleteFile()"
              v-if="field.value.id">
              <v-icon name="delete" class="icon-red" />
            </button>
          </div>
          <div
            class="image-field__image-item"
            :style="{ width: field.width ? field.width : '250px', height: field.height ? field.height : 'auto' }">
            <div class="image-field__image" v-if="field.value.url">
              <img :src="field.value.url" />
            </div>
            <div class="image-field__text" v-else @click="uploadFilesEvent">
              Перетащите изображение сюда или кликните для выбора
            </div>
          </div>
        </div>
      </upload-files>
    </div>
  </div>
</template>

<script>
import uploadFiles from './upload-files/index.js';

export default {
  components: {
    uploadFiles,
  },
  name: 'ImageField',
  emits: ['v-change'],
  props: {
    field: {
      type: Object,
      default: {},
    },
  },
  methods: {
    uploadFile(file) {
      if (file.type == 'image') this.$emit('v-change', file);
    },
    deleteFile() {
      this.$emit('v-change', []);
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
    insertFiles(files) {
      if (files.length == 0) return;
      let file = files[0];
      if (file.type != 'image') return;
      this.$emit('v-change', file);
    },
  },
};
</script>

<style lang="scss">
.image-field {
  &__card-body {
    display: flex;
    justify-content: center;
    position: relative;
  }

  &__actions {
    position: absolute;
    top: 5px;
    right: 0;
    left: 0;
    margin: auto;
    text-align: center;
    display: none;
  }

  &:hover .image-field__actions {
    display: block;
  }

  &__image-item {
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 50px;
    margin: 20px;
    cursor: pointer;
  }
  &__text {
    cursor: pointer;
  }
}
</style>
