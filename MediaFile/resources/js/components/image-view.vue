<template>
  <div class="image-view">
    <div v-if="isModalOpen" class="image-view__overlay">
      <button class="image-view__close-button" @click="closeModal">
        <v-icon name="close" size="3" />
      </button>
      <template v-if="gallery.length > 1">
        <button
          class="image-view__nav-arrow image-view__nav-arrow-left"
          @click="previousImage"
          :disabled="currentIndex === 0">
          <span>‹</span>
        </button>
        <button
          class="image-view__nav-arrow image-view__nav-arrow-right"
          @click="nextImage"
          :disabled="currentIndex === gallery.length - 1">
          <span>›</span>
        </button>
      </template>
      <div v-if="gallery.length > 1" class="image-view__counter">{{ currentIndex + 1 }} / {{ gallery.length }}</div>
      <div class="image-view__image-container">
        <img :src="gallery[currentIndex].url" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ImageView',
  // Создаем слушателей событий
  created() {
    this.emitter.on('FileManagerImageView', this.showModal);
  },
  beforeDestroy() {
    this.emitter.off('FileManagerImageView', this.showModal);
  },
  // Добавляем глобальный обработчик колесика мыши для перемотки изображений
  mounted() {
    document.addEventListener('keydown', this.handleGlobalKeyDown);
    document.addEventListener('wheel', this.handleWheelScroll, { passive: false });
  },

  beforeDestroy() {
    document.removeEventListener('keydown', this.handleGlobalKeyDown);
    document.removeEventListener('wheel', this.handleWheelScroll);
  },

  data() {
    return {
      isModalOpen: false,
      gallery: [],
      currentIndex: 0,
    };
  },

  methods: {
    showModal(conf) {
      this.gallery = conf.gallery || [];
      if (conf.current) {
        if (this.gallery.length === 0) {
          this.gallery = [conf.current];
        }
      } else {
        console.log('currentImage не передан');
        return;
      }

      // Находим индекс текущего изображения в галерее
      this.currentIndex = this.gallery.findIndex(img => img.id === conf.current.id);
      if (this.currentIndex === -1) {
        this.currentIndex = 0;
      }

      this.isModalOpen = true;
      // При открытии модального окна убираем прокрутку у документа
      document.body.style.overflow = 'hidden';
    },

    closeModal() {
      this.isModalOpen = false;
      this.gallery = [];
      this.currentIndex = 0;
      // При закрытии модального окна восстанавливаем прокрутку у документа
      document.body.style.overflow = '';
    },

    nextImage() {
      if (this.currentIndex < this.gallery.length - 1) {
        this.currentIndex++;
      }
    },

    previousImage() {
      if (this.currentIndex > 0) {
        this.currentIndex--;
      }
    },

    handleGlobalKeyDown(e) {
      if (!this.isModalOpen) return;

      switch (e.key) {
        case 'ArrowLeft':
          e.preventDefault();
          this.previousImage();
          break;
        case 'ArrowRight':
          e.preventDefault();
          this.nextImage();
          break;
        case 'Escape':
          e.preventDefault();
          this.closeModal();
          break;
      }
    },

    handleWheelScroll(event) {
      // Перемотка работает только если модалка открыта и есть больше одного изображения
      if (!this.isModalOpen || this.gallery.length <= 1) return;

      // Предотвращаем стандартный скролл страницы
      event.preventDefault();

      if (event.deltaY > 0) {
        // Вниз — следующее изображение
        this.nextImage();
      } else if (event.deltaY < 0) {
        // Вверх — предыдущее изображение
        this.previousImage();
      }
    },
  },
};
</script>

<style lang="scss">
.image-view {
  &__image-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }

  /* Модальное окно */
  &__overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 9999;
  }

  /* Кнопка закрытия */
  &__close-button {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    fill: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    z-index: 10;
    &:hover {
      background: rgba(255, 255, 255, 0.3);
    }
  }

  /* Навигационные стрелки */
  &__nav-arrow {
    position: absolute;
    top: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 32px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    text-align: center;
    transform: translateY(-50%);
    z-index: 10;
    &:hover {
      background: rgba(255, 255, 255, 0.3);
    }
    &:disabled {
      opacity: 0.3;
      cursor: not-allowed;
    }
    span {
      position: relative;
      top: -7px;
    }
  }

  &__nav-arrow-left {
    left: 15px;
  }

  &__nav-arrow-right {
    right: 15px;
  }

  /* Счетчик изображений */
  &__counter {
    position: absolute;
    display: inline-block;
    bottom: 15px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-size: 14px;
    background: rgba(0, 0, 0, 0.5);
    padding: 8px 16px;
    border-radius: 20px;
    z-index: 10;
  }

  &__image-container {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
  }

  &__image-container img {
    max-width: 100%;
    max-height: 100vh;
    height: auto;
    width: auto;
    border-radius: 4px;
    outline: none;
  }
}
</style>
