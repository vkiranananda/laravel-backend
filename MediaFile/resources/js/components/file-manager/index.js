import template from './template.html?raw';
import './style.scss';
import fileList from '../file-list/index.js';
import uploadFiles from '../upload-files/index.js';
import FileIcon from '../file-icon.vue';

export default {
  template,
  components: {
    fileList,
    uploadFiles,
    FileIcon,
  },
  emits: ['selectFiles'],
  name: 'FileManager',
  props: {
    popup: {
      type: Boolean,
      default: false,
    },
    listUrl: {
      type: String,
      required: true,
    },
    parent: {
      default: 0,
    },
    modal: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      filesSrc: [],
      // Поле сортировки
      sortType: 'name',
      // Направление сортировки
      sortDirection: 'asc',
      // Тип отображения списка файлов
      listType: 'list',
      isDragOver: false,
      urls: {
        upload: '',
      },
      parentId: this.parent,
      parentTree: [],
      // Заголовок модального окна
      modalTitle: '',
      modalItems: [],
      modalCancel: false,
      finishText: false,
      canselButtonText: 'Отменить',
      formType: '',
      folderName: '',
      errorText: false,
      fileName: {
        name: '',
        ext: '',
      },
    };
  },
  computed: {
    files() {
      let folders = this.filesSrc.filter(f => f.type === 'folder');
      let files = this.filesSrc.filter(f => f.type !== 'folder');

      const sortDirection = result => (this.sortDirection === 'asc' ? -result : result);
      const sortFunc = arr => {
        arr.sort((a, b) => {
          if (this.sortType === 'date') {
            let result = b.timestamp - a.timestamp;
            if (result !== 0) return sortDirection(result);
          } else if (this.sortType === 'size') {
            let result = (b.size || 0) - (a.size || 0);
            if (result !== 0) return sortDirection(result);
          }
          let result = a.name.localeCompare(b.name, 'ru', {
            sensitivity: 'base',
          });
          return this.sortType === 'name' && this.sortDirection === 'desc' ? result * -1 : result;
        });
      };

      sortFunc(folders);
      sortFunc(files);

      // Возвращаем объединённый массив: сначала папки, потом файлы
      return [...folders, ...files];
    },
  },
  mounted() {
    if (!this.modal) {
      this.getFiles(this.parentId);
    }
  },
  methods: {
    clearSelectedFiles() {
      this.$refs.fileList.clearSelectedFiles();
    },
    // Закрытие модального окна
    closeModal() {
      this.$refs.modal.hide();
      this.finishText = false;
      this.formType = '';
      this.modalCancel = true;
      this.modalItems = [];
    },

    showModal() {
      this.$refs.modal.show();
    },

    // Получаем список файлов
    getFiles(id = 0) {
      axios
        .get(this.listUrl, {
          params: {
            parentId: id,
          },
        })
        .then(response => {
          this.filesSrc = response.data.files;
          this.urls = response.data.urls;
          this.parentTree = response.data.parentTree;
          this.parentId = id;
          // Добавляем/обновляем параметр path в url, если id не равен 0
          const url = new URL(window.location.href);
          let path = url.searchParams.get('path');
          if (path === null) path = 0;
          // Если path не равен id, то обновляем url
          if (path != id) {
            if (id != 0) {
              url.searchParams.set('path', id);
              window.history.pushState({}, '', url);
            } else {
              // Если id равен 0, удаляем параметр path из url
              url.searchParams.delete('path');
              window.history.pushState({}, '', url);
            }
          }
        });
    },

    // Загрузка файла
    uploadFile(file) {
      this.filesSrc.push(file);
    },

    // Выбор файлов
    selectFiles() {
      this.$refs.uploadFiles.selectFiles();
    },

    selectFilesEvent(files) {
      this.$emit('selectFiles', files);
    },

    // Перемещение файлов
    async moveFiles(files, fromId, toId) {
      // Если нет url для перемещения, то выходим
      if (!this.urls.move) {
        return;
      }

      this.modalTitle = 'Перемещение файлов';
      this.modalItems = [];
      this.modalCancel = false;
      this.canselButtonText = 'Отменить';
      this.showModal();

      // Преобразуем Map в массив для правильной работы с break
      const filesArray = Array.from(files.values());
      for (const file of filesArray) {
        // Если отмена, то выходим
        if (this.modalCancel) {
          break;
        }

        this.modalItems.push(Object.assign({}, file));
        let item = this.modalItems[this.modalItems.length - 1];
        item.status = 'Перемещение...';
        item.error = '';

        try {
          const response = await axios.post(this.urls.move, {
            id: file.id,
            name: file.name,
            toId: toId,
          });
          // Если перемещаемые файлы находятся в текущей папке, то удаляем их из списка
          if (fromId === this.parentId) {
            const index = this.filesSrc.findIndex(f => f.id === file.id);
            if (index > -1) {
              this.filesSrc.splice(index, 1);
            }
          }
          // Если перемещаем в текущую папку, то добавляем в список
          if (toId === this.parentId) {
            this.filesSrc.push(response.data);
          }
          item.status = 'Перемещено';
          this.modalItems.splice(this.modalItems.indexOf(item), 1);
        } catch (error) {
          item.error = this.appHelpers.getAjaxError(error);
          item.status = 'Ошибка';
          console.error('Ошибка при перемещении файла:', error);
        }
      }
      if (this.modalItems.length === 0) {
        this.finishText = 'Файлы успешно перемещены';
        this.canselButtonText = 'Закрыть';
        setTimeout(() => {
          this.closeModal();
        }, 1000);
      }
    },



    // Переименование файла
    renameFileEvent(file) {
      this.formType = 'renameFile';
      this.modalTitle = 'Переименование ' + (file.type === 'folder' ? 'папки' : 'файла');
      this.errorText = false;
      this.fileName['file'] = file;
      // Получаем расширение и имя файла отдельно, расширение с точкой
      const lastDotIndex = file.name.lastIndexOf('.');
      if (lastDotIndex > 0 && file.type !== 'folder') {
        this.fileName['name'] = file.name.substring(0, lastDotIndex);
        this.fileName['ext'] = file.name.substring(lastDotIndex);
      } else {
        this.fileName['name'] = file.name;
        this.fileName['ext'] = '';
      }
      this.showModal();
    },

    renameFile() {
      let name = String(this.fileName['name']) + String(this.fileName['ext']);
      // Если нет url для переименования или имя не изменилось, то выходим
      if (!this.urls.move || this.fileName['file'].name === name) {
        this.closeModal();
        return;
      }
      // Переименование файла
      axios
        .post(this.urls.move, {
          id: this.fileName['file'].id,
          name,
        })
        .then(response => {
          const index = this.filesSrc.findIndex(f => f.id === this.fileName['file'].id);
          if (index > -1) {
            this.filesSrc[index].name = response.data.name;
          }
          this.closeModal();
        })
        .catch(error => {
          this.errorText = this.appHelpers.getAjaxError(error);
          console.error('Ошибка при создании папки:', error);
        });
    },

    // Копирование файлов
    async pasteFile(files, toId) {
      // Если нет url для копирования, то выходим
      if (!this.urls.copy) {
        return;
      }

      this.modalTitle = 'Копирование файлов';
      this.modalItems = [];
      this.modalCancel = false;
      this.canselButtonText = 'Отменить';
      this.showModal();

      // Преобразуем Map в массив для правильной работы с break
      const filesArray = Array.from(files.values());
      for (const file of filesArray) {
        // Если отмена, то выходим
        if (this.modalCancel) {
          break;
        }

        this.modalItems.push(Object.assign({}, file));
        let item = this.modalItems[this.modalItems.length - 1];
        item.status = 'Копирование...';
        item.error = '';

        try {
          const response = await axios.post(this.urls.copy, {
            id: file.id,
            toId: toId,
          });
          // Если копируем в текущую папку, то добавляем в список
          if (toId === this.parentId) {
            this.filesSrc.push(response.data);
          }
          item.status = 'Скопировано';
          this.modalItems.splice(this.modalItems.indexOf(item), 1);
        } catch (error) {
          item.error = this.appHelpers.getAjaxError(error);
          item.status = 'Ошибка';
          console.error('Ошибка при копировании файла:', error);
        }
      }
      if (this.modalItems.length === 0) {
        this.finishText = 'Файлы успешно скопированы';
        this.canselButtonText = 'Закрыть';
        setTimeout(() => {
          this.closeModal();
        }, 1000);
      }
    },

    // Удаляем файлы
    deleteFile(files) {
      // Если нет url для удаления, то выходим
      if (!this.urls.delete) {
        return;
      }

      this.msgConfirm('Подтвердите удаление.', async () => {
        this.modalTitle = 'Удаление файлов';
        this.modalItems = [];
        this.modalCancel = false;
        this.canselButtonText = 'Отменить';
        this.showModal();

        // Преобразуем Map в массив для правильной работы с break
        const filesArray = Array.from(files.values());
        for (const file of filesArray) {
          // Если отмена, то выходим
          if (this.modalCancel) {
            break;
          }
          // Создаем копию файла для отображения в модальном окне
          // Сначала создаем копию файла, потом добавляем в массив
          // И делее по индексу получаем элемент и обновляем его
          // Иначе не будет обновления
          this.modalItems.push(Object.assign({}, file));
          let item = this.modalItems[this.modalItems.length - 1];
          item.status = 'Удаление...';
          item.error = '';

          try {
            const response = await axios.delete(this.urls.delete, {
              params: { id: file.id },
            });
            const index = this.filesSrc.findIndex(f => f.id === file.id);
            if (index > -1) {
              this.filesSrc.splice(index, 1);
            }
            item.status = 'Удалено';
            this.modalItems.splice(this.modalItems.indexOf(item), 1);
          } catch (error) {
            item.error = this.appHelpers.getAjaxError(error);
            item.status = 'Ошибка';
            console.error('Ошибка при удалении файла:', error);
          }
        }
        if (this.modalItems.length === 0) {
          this.finishText = 'Файлы успешно удалены';
          this.canselButtonText = 'Закрыть';
          setTimeout(() => {
            this.closeModal();
          }, 1000);
        }
      });
    },

    createFolderEvent() {
      this.formType = 'createFolder';
      this.modalTitle = 'Создание папки';
      this.errorText = false;
      this.folderName = '';
      this.showModal();
    },

    createFolder() {
      axios
        .post(this.urls.createFolder, {
          name: this.folderName,
          parentId: this.parentId,
        })
        .then(response => {
          this.filesSrc.push(response.data);
          this.closeModal();
        })
        .catch(error => {
          this.errorText = this.appHelpers.getAjaxError(error);
          console.error('Ошибка при создании папки:', error);
        });
    },

    doubleClick(file) {
      if (file.type === 'folder') {
        this.getFiles(file.id);
        return;
      }
    },
    toggleSortDirection() {
      this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
    },
  },
};
