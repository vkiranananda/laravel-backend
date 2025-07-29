<template>
  <div class="files-field">
    <div class="card">
      <div class="card-body">
        <upload-files ref="uploadFiles" :upload-url="field['upload-url']" @uploadFile="uploadFile">
          <file-list
            :files="files"
            :list-type="field['file-type'] === 'image' ? 'grid' : 'list'"
            :folder="false"
            @downloadFile="downloadFile"
            @deleteFile="deleteFile"
            @doubleClick="doubleClick"
            @uploadFiles="selectFiles"
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
    selectFiles(files) {
      this.files = files;
    },
  },
};
</script>

<style scoped>
.files-field {
  min-height: 40px;
}
</style>
