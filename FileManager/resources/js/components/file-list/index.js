import template from './template.html?raw';
import './style.scss';
import FileIcon from '../file-icon.vue';
import helpers from '../../helpers';
import Sortable from '../../../../../resources/js/libs/sortable.js';
export default {
  template,
  name: 'FileManager',
  components: {
    FileIcon,
  },
  props: {
    files: {
      type: Array,
      required: true,
    },
    listType: {
      type: String,
      default: 'grid',
      validator: value => ['list', 'grid'].includes(value),
    },
    parentId: {
      default: 0,
    },
    folder: {
      type: Boolean,
      default: true,
    },
    sortable: {
      type: Boolean,
      default: false,
    },

  },
  data() {
    return {
      // Выбранные файлы
      selectedFiles: new Map(),
      filesToCopy: false,
      filesToMove: false,
      idFilesToMove: {},
      // Выбранные файлы по ID, для шаблона и реактивности
      selectedFileIds: {},
      // Выделение файлов
      isSelecting: false,
      // Перемещение файлов
      isMovingFiles: false,
      // Выделяем файл при наведении на него
      isHoveringFileId: null,
      // Открыто ли меню файлов
      fileMenuOpen: false,
      // Открыто ли главное меню
      mainMenuOpen: false,
      // Ключ для обновления меню
      menuKey: 1,
    };
  },
  mounted() {
    if (this.sortable) {
    this._sortable = new Sortable(this.$refs.body, {
      onEnd: evt => {
        // let res = this.files.slice();
        // let oldEl = res[evt.oldIndex];
        // res[evt.oldIndex] = res[evt.newIndex];
        // res[evt.newIndex] = oldEl;
        // console.log(evt);
        this.$emit('sortable', evt.oldIndex, evt.newIndex);
      },
    });
    }
  },
  beforeUnmount() {
    if (this._sortable !== undefined) this._sortable.destroy();
  },
  computed: {
    menuItems() {
      let items = [];

      const file = this.selectedFiles.values().next().value;

      const copyPaste = () => {
        if (this.filesToCopy && (!file || !this.filesToCopy.has(file.id))) {
          if (this.folder) {
            items.push({
              label: 'Вставить',
              method: this.pasteFile,
              icon: 'paste',
            });
          }
        }

        // Проверяем что файлы для перемещения не находятся в текущей папке
        // const firstMoveFile = this.filesToMove.values().next().value;
        // if (!file && (firstMoveFile && this.files.find(f => f.id === firstMoveFile.id))) {
        //   return;
        // }
        //
        if (this.filesToMove && (!file || !this.filesToMove.has(file.id))) {
          if (this.folder) {
            items.push({
              label: 'Переместить',
              method: this.moveFile,
              icon: 'move',
            });
          }
        }
      };

      if (this.fileMenuOpen) {
        if (this.selectedFiles.size === 1) {
          if (file.type !== 'folder') {
            items.push({
              label: 'Скачать',
              method: this.downloadFile,
              icon: 'download',
            });
          }
          items.push({
            label: 'Переименовать',
            method: this.renameFile,
            icon: 'pencil',
          });
          if (file.type === 'folder') {
            copyPaste();
          }
        }
        if (this.folder) {
          items.push({
            label: 'Копировать',
            method: this.copyFile,
            icon: 'copy',
          });
          items.push({
            label: 'Вырезать',
            method: this.cutFile,
            icon: 'cut',
          });
        }
        items.push({
          label: 'Удалить',
          method: this.deleteFile,
          icon: 'delete',
        });
      }
      if (this.mainMenuOpen) {
        if (this.folder) {
          items.push({
            label: 'Создать папку',
            method: this.createFolder,
            icon: 'folder',
          });
        }
        items.push({
          label: 'Загрузить файлы',
          method: this.uploadFiles,
          icon: 'upload',
        });
        copyPaste();
      }
      return items.length > 0 ? items : false;
    },
  },
  watch: {
    files: {
      handler() {
        // Индекс последнего выделенного файла для выделения по shift
        this._lastSelectedIndex = 0;
      },
      immediate: true,
    },
    selectedFileIds: {
      handler() {
        // Преобразуем selectedFiles в массив файлов
        let result = Array.from(this.selectedFiles.values()).filter(f => f.type !== 'folder');
        this.$emit('selectFiles', result);
      },
      deep: true,
    },
  },
  methods: {
    setSelectedFile(file) {
      this.selectedFiles.set(file.id, file);
      this.selectedFileIds[file.id] = true;
    },

    deleteSelectedFile(file) {
      this.selectedFiles.delete(file.id);
      delete this.selectedFileIds[file.id];
    },

    // Снимаем выделение
    clearSelectedFiles() {
      this.selectedFiles.clear();
      this.selectedFileIds = {};
    },

    // Выделяем файл по клику
    selectFile(file, event) {
      this.closeAllMenus();
      const clickedIndex = this.files.indexOf(file);

      if (event && event.shiftKey) {
        // Если нет выделения — выделяем с начала
        let start = this._lastSelectedIndex,
          end = clickedIndex;
        if (start > end) [start, end] = [end, start];
        this.clearSelectedFiles();
        for (let i = start; i <= end; i++) {
          this.setSelectedFile(this.files[i]);
        }
      } else if (event && (event.ctrlKey || event.metaKey)) {
        if (this.selectedFiles.has(file.id)) {
          this.deleteSelectedFile(file);
        } else {
          this.setSelectedFile(file);
        }
        this._lastSelectedIndex = clickedIndex;
      } else {
        // Если клик был двойной, то вызываем событие doubleClick
        const currentTime = Date.now();
        // Если по этому файлу уже был клик
        if (this._mouseLastClickFileId === file.id) {
          // Если интервал между кликами не более 300 мс
          if (this._mouseLastClickTime && currentTime - this._mouseLastClickTime < 300) {
            // Если событие doubleClick не было вызвано за последние 1500 мс, то вызываем событие doubleClick
            if (!this._mouseLastSecondClickTime || currentTime - this._mouseLastSecondClickTime > 1500) {
              this.doubleClick(file);
            }
            // Пишем время последнего двойного клика
            this._mouseLastSecondClickTime = currentTime;
          }
        } else {
          // Сбрасываем время последнего двойного клика если клик был по другому файлу
          this._mouseLastSecondClickTime = false;
        }

        this.clearSelectedFiles();
        this.setSelectedFile(file);
        this._lastSelectedIndex = clickedIndex;
        this._mouseLastClickTime = currentTime;
        // Пишем id последнего кликнутого файла
        this._mouseLastClickFileId = file.id;
      }
    },

    doubleClick(file) {
      if (file.type === 'image') {
        window.open(file.url, '_blank');
      } else if (file.type != 'folder') {
        this.downloadFile(file);
      }
      this.$emit('doubleClick', file);
    },

    setMenuPosition(e) {
      this.$nextTick(() => {
        const parentRect = this.$el.getBoundingClientRect();

        this.$refs.menuBlock.style.left = e.clientX - parentRect.left + 5 + 'px';
        this.$refs.menuBlock.style.top = e.clientY - parentRect.top + this.$refs.body.scrollTop + 5 + 'px';

        this.$refs.menu.show();
      });
    },

    showFileMenu(file, e) {
      if (e.target.closest('.dropdown')) {
        return;
      }
      this.fileMenuFiles = [];

      if (!this.selectedFiles.has(file.id)) {
        this.clearSelectedFiles();
        this.setSelectedFile(file);
      }
      e.preventDefault();
      this.fileMenuOpen = true;
      this.mainMenuOpen = false;
      this.setMenuPosition(e);
      this.menuKey++;
    },

    // Скачивание файлов
    downloadFile(file = false) {
      if (!file) {
        file = this.selectedFiles.values().next().value;
      }
      const link = document.createElement('a');
      link.href = file.url + '?download=true';
      link.download = file.name_orig || file.name;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      this.closeAllMenus();
    },

    // Переименование файлов
    renameFile() {
      this.$emit('renameFile', this.selectedFiles.values().next().value);
      this.closeAllMenus();
    },

    // Копирование файлов
    copyFile() {
      this.filesToCopy = new Map(this.selectedFiles);
      this.filesToMove = false;
      this.clearSelectedFiles();
      this.closeAllMenus();
      this.idFilesToMove = {};
    },

    // Вырезание файлов
    cutFile() {
      this.filesToMove = new Map(this.selectedFiles);
      this.filesToCopy = false;
      this.clearSelectedFiles();
      this.closeAllMenus();
      this.idFilesToMove = {};
      this.filesToMove.forEach(file => {
        this.idFilesToMove[file.id] = true;
      });
      this.filesToMoveFrom = this.parentId;
    },

    // Удаление файлов
    deleteFile() {
      this.$emit('deleteFile', new Map(this.selectedFiles));
      this.closeAllMenus();
      this.clearSelectedFiles();
    },

    // Перемещение файлов
    moveFile() {
      const moveTo = this.selectedFiles.size === 1 ? this.selectedFiles.values().next().value.id : this.parentId;

      this.$emit('moveFiles', new Map(this.filesToMove), this.filesToMoveFrom, moveTo);

      this.filesToCopy = false;
      this.filesToMove = false;
      this.closeAllMenus();
      this.clearSelectedFiles();
      this.idFilesToMove = {};
    },

    // Вставка файлов
    pasteFile() {
      const pasteTo = this.selectedFiles.size === 1 ? this.selectedFiles.values().next().value.id : this.parentId;
      this.$emit('pasteFile', new Map(this.filesToCopy), pasteTo);
      this.filesToCopy = false;
      this.closeAllMenus();
      this.clearSelectedFiles();
    },

    showMainMenu(e) {
      e.preventDefault();
      // Открываем меню только если клик НЕ был по файлу или другому меню
      if (!e.target.closest('.file-list__file-item') && !e.target.closest('.dropdown')) {
        this.clearSelectedFiles();
        this.mainMenuOpen = true;
        this.fileMenuOpen = false;
        this.setMenuPosition(e);
        this.menuKey++;
      }
    },

    createFolder() {
      this.$emit('createFolder');
      this.closeAllMenus();
    },

    uploadFiles() {
      this.$emit('uploadFiles');
      this.closeAllMenus();
    },

    closeAllMenus() {
      if (this.$refs.menu) this.$refs.menu.hide();
      this.fileMenuOpen = false;
      this.mainMenuOpen = false;
    },
    // -------------------------------Перемещение файлов-----------------------

    // Обработка нажатия левой кнопки мыши на файл, для перемещения файла
    onMouseDownFile(file, e) {
      // Правая кнопка мыши
      if (e.button !== 0 || this.folder === false || this.sortable) return;
      this._mouseDown = true;
      // Если список в виде сетки, то перемещение файла только по иконке
      if (this.listType === 'grid' && !e.target.closest('.file-icon')) return;
      this._mouseDownFile = file;

      this.onMouseMoveStartPosition(e);
      window.addEventListener('mousemove', this.onMouseMoveFile);
      window.addEventListener('mouseup', this.onMouseUpFile);
    },

    onMouseUpFile() {
      window.removeEventListener('mousemove', this.onMouseMoveFile);
      window.removeEventListener('mouseup', this.onMouseUpFile);
      // Если было перемещение файлов, то передаём событие в родительский компонент
      if (this.isMovingFiles && this._moveFileTo) {
        this.$emit('moveFiles', new Map(this.selectedFiles), this.parentId, this._moveFileTo.id);
        this.clearSelectedFiles();
      }
      this.isMovingFiles = false;
    },

    // Обработка движения мыши при перемещении файлов
    onMouseMoveFile(e) {
      const moveBox = this.$refs.moveBox;

      // Выполняем один раз, когда было нажатие на файл
      if (this._mouseDown) {
        if (this.isSmallMouseMove(e)) return;
        this._mouseDown = false;
        this.isMovingFiles = true;
        // Получаем все DOM-элементы файлов
        moveBox.innerHTML = '';

        // Если файл не выделен, то выделяем его
        // Делаем здесь что бы не было коллизий с выделением файлов
        if (!this.selectedFiles.has(this._mouseDownFile.id)) {
          this.clearSelectedFiles();
          this.setSelectedFile(this._mouseDownFile);
        }

        // Клонируем иконки выбранных файлов
        let idx = 0;
        this.selectedFiles.forEach(file => {
          // Находим DOM-элемент иконки
          const fileEl = this.$refs['fileItem' + file.id][0];
          const iconEl = fileEl.querySelector('.file-icon');

          // Клонируем иконку
          const clone = iconEl.cloneNode(true);
          clone.style.position = 'absolute';
          clone.style.left = idx * 5 + 'px';
          clone.style.top = idx * 3 + 'px';
          clone.style.zIndex = idx;
          clone.style.pointerEvents = 'none';

          moveBox.appendChild(clone);
          idx++;
        });
      }

      // --- позиционирование moveBox относительно body ---
      const rect = this.$refs.body.getBoundingClientRect();
      moveBox.style.left = e.clientX - rect.left - 10 + 'px';
      moveBox.style.top = e.clientY - rect.top + this.$refs.body.scrollTop - 10 + 'px';
    },

    // Обработка наведения на файл
    onMouseEnterFile(file, e) {
      // Если перемещаем файлы, выделяем только не выбранные папки
      if (this.isMovingFiles) {
        if (this.selectedFiles.has(file.id) || file.type !== 'folder') return;
        // Сохраняем папку, в которую будет перемещены файлы
        this._moveFileTo = file;
      }
      this.isHoveringFileId = file.id;
    },

    onMouseLeaveFile(file, e) {
      this.isHoveringFileId = null;
      this._moveFileTo = false;
    },

    //--------------------------------Обработка выделения файлов--------------------------------

    // Обработка выделения файлов через выделение мышкой
    // Попутно закрываем все меню и очищаем выделение файлов
    onMouseDownSelectionBox(e) {
      if (e.button !== 0) return; // только левая кнопка
      // Обработка первого клика при движении мыши
      this._mouseDown = true;
      // Движение мыши не началось
      this._mouseMove = false;

      // Если клик был не по меню, то закрываем все меню
      if (e.target.closest('.file-list__menu')) {
        return;
      } else {
        this.closeAllMenus();
      }

      // Проверяем, был ли клик по самому файлу.
      if (e.target.closest('.file-list__file-item')) {
        this._mouseDownTarget = 'file';
        // Если список в виде сетки, то перемещение файла только по иконке, иначе по строке целиком
        if (this.listType === 'list' || e.target.closest('.file-icon')) {
          return;
        }
      } else this._mouseDownTarget = 'empty';

      this.onMouseMoveStartPosition(e);
      window.addEventListener('mousemove', this.onMouseMoveSelectionBox);
      window.addEventListener('mouseup', this.onMouseUpSelectionBox);
    },

    onMouseUpSelectionBox() {
      // Скрываем рамку выделения.
      this.isSelecting = false;

      // Если был клик по свободной области и не было движения мыши, то сбрасываем выделение
      if (!this._mouseMove && this._mouseDownTarget === 'empty') this.clearSelectedFiles();

      window.removeEventListener('mousemove', this.onMouseMoveSelectionBox);
      window.removeEventListener('mouseup', this.onMouseUpSelectionBox);

      document.body.style.userSelect = 'text';
    },

    // Обработка движения мыши при выделении
    onMouseMoveSelectionBox(e) {
      // При перемещении мыши, если было нажатие, то начинаем выделение
      if (this._mouseDown) {
        if (this.isSmallMouseMove(e)) return;

        this.clearSelectedFiles();
        this._mouseDown = false;
        this.isSelecting = true;
        this._mouseMove = true;

        // Запрещаем выделение текста при выделении файлов
        document.body.style.userSelect = 'none';
      }

      const rect = this.$refs.body.getBoundingClientRect();
      const selectionBox = this.$refs.selectionBox;

      // Учитываем скролл контейнера файлов
      this._selectionEnd = {
        x: e.clientX - rect.left,
        y: e.clientY - rect.top + this.$refs.body.scrollTop,
      };

      // Обновляем позицию рамки выделения
      selectionBox.style.left = Math.min(this._selectionStart.x, this._selectionEnd.x) + 'px';
      selectionBox.style.top = Math.min(this._selectionStart.y, this._selectionEnd.y) + 'px';
      selectionBox.style.width = Math.abs(this._selectionEnd.x - this._selectionStart.x) + 'px';
      selectionBox.style.height = Math.abs(this._selectionEnd.y - this._selectionStart.y) + 'px';

      // Получаем координаты рамки выделения
      const selectionRect = {
        left: Math.min(this._selectionStart.x, this._selectionEnd.x),
        top: Math.min(this._selectionStart.y, this._selectionEnd.y),
        right: Math.max(this._selectionStart.x, this._selectionEnd.x),
        bottom: Math.max(this._selectionStart.y, this._selectionEnd.y),
      };

      // Для каждого файла проверяем пересечение с рамкой выделения
      this.files.forEach(file => {
        const fileRect = this.getFileRect(this.$refs['fileItem' + file.id][0], this.$refs.body.getBoundingClientRect());
        // Проверяем пересечение рамки выделения с файлом
        if (
          selectionRect.left < fileRect.right &&
          selectionRect.right > fileRect.left &&
          selectionRect.top < fileRect.bottom &&
          selectionRect.bottom > fileRect.top
        ) {
          this.setSelectedFile(file);
        } else {
          this.deleteSelectedFile(file);
        }
      });
    },

    // Сохраняем начальную позицию мышки
    onMouseMoveStartPosition(e) {
      const rect = this.$refs.body.getBoundingClientRect();
      this._selectionStart = {
        x: e.clientX - rect.left,
        y: e.clientY - rect.top + this.$refs.body.scrollTop,
      };
    },

    // Проверяем, было ли малое движение мыши
    isSmallMouseMove(e) {
      const rect = this.$refs.body.getBoundingClientRect();
      const dx = Math.abs(e.clientX - (this._selectionStart?.x + rect.left));
      const dy = Math.abs(e.clientY - (this._selectionStart?.y + rect.top - this.$refs.body.scrollTop));
      return dx <= 4 && dy <= 4;
    },

    /**
     * Возвращает координаты DOM-элемента файла относительно контейнера файлов
     * @param {Element} el - DOM-элемент файла
     * @param {DOMRect} parentRect - bounding rect контейнера
     * @returns {Object} - {left, top, right, bottom}
     */
    getFileRect(el, parentRect) {
      const r = el.getBoundingClientRect();
      return {
        left: r.left - parentRect.left,
        top: r.top - parentRect.top + this.$refs.body.scrollTop,
        right: r.left - parentRect.left + r.width,
        bottom: r.top - parentRect.top + r.height + this.$refs.body.scrollTop,
      };
    },
    formatFileSize(bytes) {
      return helpers.formatFileSize(bytes);
    },
  },
};
