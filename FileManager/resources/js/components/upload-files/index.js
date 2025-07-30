import template from './template.html?raw';
import './style.scss';
import FileIcon from '../file-icon.vue';
import helpers from '../../helpers';

export default {
  template,
  name: 'UploadFile',
  components: {
    FileIcon,
  },
  props: {
    uploadUrl: {
      type: String,
      required: true,
    },
    parentId: {
      default: 0,
    },
  },
  data() {
    return {
      // Файлы для загрузки
      files: [],
      uploadQueue: [],
      uploadedCount: 0,
      errorCount: 0,
      maxConcurrentUploads: 3,
      activeUploads: 0,
      isDragOver: false,
    };
  },
  mounted() {
    // this.$refs.modal.show();
  },
  methods: {
    // Перетаскивание файлов
    handleDragOver(e) {
      const hasFiles =
        e.dataTransfer.types.includes('Files') || e.dataTransfer.types.includes('application/x-moz-file');

      if (hasFiles) {
        e.preventDefault();
        this.isDragOver = true;
      }
    },

    // Перетаскивание файлов отмена
    handleDragLeave(e) {
      e.preventDefault();
      this.isDragOver = false;
    },

    // Выбор файлов
    selectFiles() {
      this.$refs.fileInput.click();
    },

    handleFileSelect(e) {
      this.startUpload(Array.from(e.target.files));
      e.target.value = ''; // Сброс input
    },

    handleDrop(e) {
      e.preventDefault();
      this.isDragOver = false;

      const items = e.dataTransfer.items || e.dataTransfer.files;
      let files = [];
      if (items) {
        // Получаем файлы из перетаскивания
        items.forEach(item => {
          console.log(item);
          const data = item.webkitGetAsEntry && item.webkitGetAsEntry();
          if (data) {
            if (data.isDirectory) {
              files.push(data);
            } else if (data.isFile) {
              const file = item.getAsFile();
              if (file) {
                files.push(file);
              }
            }
          } else if (item.kind === 'file') {
            const file = item.getAsFile();
            if (file) {
              files.push(file);
              console.log(file);
            }
          }
        });
      }
      this.startUpload(files);
    },

    startUpload(files) {
      if (files.length === 0) return;
      this.$refs.modal.show();
      this.uploadQueue.push(
        ...files.map(file => ({
          id: Date.now() + Math.random(),
          file: file,
          status: 'pending',
          progress: 0,
          error: null,
          size: helpers.formatFileSize(file.size),
          type: file.isDirectory ? 'folder' : 'file',
        }))
      );

      this.processUploadQueue();
    },

    async processUploadQueue() {
      this.uploadQueue.forEach(file => {
        if (this.activeUploads < this.maxConcurrentUploads) {
          if (file.status === 'pending') this.uploadFile(file);
        } else return;
      });
    },

    async uploadFile(file) {
      this.activeUploads++;
      file.status = 'uploading';

      try {
        const formData = new FormData();
        formData.append('file', file.file);
        formData.append('name', file.file.name);
        formData.append('parentId', this.parentId);
        let res = await axios.post(this.uploadUrl, formData, {
          onUploadProgress: progressEvent => {
            file.progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
          },
        });
        this.$emit('uploadFile', res.data);
        file.status = 'success';
        this.removeFileFromQueue(file);
      } catch (error) {
        file.error = this.appHelpers.getAjaxError(error);
        file.status = 'error';
        console.error('Upload error:', error);
      }

      this.activeUploads--;
      this.processUploadQueue();
    },

    removeFile(fileId) {
      const index = this.files.findIndex(f => f.id === fileId);
      if (index > -1) {
        this.files.splice(index, 1);
      }
    },

    // Удаление файла из очереди
    removeFileFromQueue(item) {
      setTimeout(() => {
        const index = this.uploadQueue.indexOf(item);
        if (index > -1) {
          this.uploadQueue.splice(index, 1);
        }
        if (this.uploadQueue.length === 0) {
          this.$refs.modal.hide();
        }
      }, 1000);
    },

    // Отмена загрузки файла
    cancelUploadFile(item) {
      if (item.status === 'uploading' || item.status === 'pending') {
        item.status = 'canceled';
        this.removeFileFromQueue(item);
      }
    },

    // Отмена всех загрузок
    cancelAllUploads() {
      this.uploadQueue.forEach(item => {
        if (item.status === 'uploading' || item.status === 'pending') {
          item.status = 'canceled';
          this.removeFileFromQueue(item);
        }
      });

      setTimeout(() => {
        this.$refs.modal.hide();
        this.uploadQueue = [];
      }, 1100);
    },
  },
};
