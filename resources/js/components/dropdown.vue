<script>
export default {
  name: 'dropdown',
  inheritAttrs: false,
  props: {
    width: {
      type: Number,
      default: 200,
    },
    position: {
      type: String,
      default: 'right', // 'right', 'left'
      validator: value => ['right', 'left'].includes(value),
    },
  },
  computed: {
    dropdownStyle() {
      return {
        width: this.width + 'px',
      };
    },
  },
  data() {
    return {
      activeIndex: 0,
      actualPosition: 'right', // фактическая позиция с учетом края экрана
      showMenu: false,
    };
  },
  mounted() {
    // Что бы клик при активации элемента не отрабатывал уже в этом цикле
    // а то этот клик будет сразу закрывать меню.
    setTimeout(() => {
      document.addEventListener('click', this.handleClickOutside);
      document.addEventListener('keydown', this.handleKeydown);
      this.setFirstActive();
      this.addMouseListeners();
    }, 100);

    this.parentElement = this.$el.parentElement;
  },
  beforeUnmount() {
    console.log('beforeUnmount');
    document.removeEventListener('click', this.handleClickOutside);
    document.removeEventListener('keydown', this.handleKeydown);
    this.removeMouseListeners();
  },
  methods: {
    handleClickOutside(event) {
      if (this.dropdown && !this.dropdown.contains(event.target)) {
        this.hide();
        this.$emit('v-click-outside');
      }
    },

    hide() {
      this.showMenu = false;
    },
    show() {
      this.showMenu = true;
      this.$nextTick(() => {
        this.dropdown = this.$refs.dropdown;
        this.calculatePosition();
      });
    },

    handleKeydown(event) {
      const items = this.getItems();
      if (!items.length) return;
      if (event.key === 'Escape') {
        this.hide();
      } else if (event.key === 'ArrowDown') {
        event.preventDefault();
        this.moveActive(1, items);
      } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        this.moveActive(-1, items);
      } else if (event.key === 'ArrowRight') {
        event.preventDefault();
        this.activeIndex = items.length - 1;
        this.setActiveClass(items);
      } else if (event.key === 'ArrowLeft') {
        event.preventDefault();
        this.activeIndex = 0;
        this.setActiveClass(items);
      } else if (event.key === 'Enter') {
        if (this.activeIndex >= 0 && items[this.activeIndex]) {
          items[this.activeIndex].click();
        }
      }
    },
    moveActive(direction, items) {
      // direction: 1 — вниз, -1 — вверх
      // items — массив DOM-элементов пунктов меню (без .disabled)

      let idx = this.activeIndex;
      let len = items.length;

      // Смещаем индекс, зацикливаем по длине массива
      idx = (idx + direction + len) % len;

      this.activeIndex = idx;
      this.setActiveClass(items);
    },
    setActiveClass(items) {
      items.forEach((el, i) => {
        if (i === this.activeIndex) {
          el.classList.add('active');
          el.scrollIntoView({ block: 'nearest' });
        } else {
          el.classList.remove('active');
        }
      });
    },
    getItems() {
      if (!this.dropdown) return [];
      return Array.from(this.dropdown.querySelectorAll('.item:not(.disabled)'));
    },
    addMouseListeners() {
      const items = this.getItems();
      items.forEach((el, i) => {
        el.onmouseenter = () => {
          this.activeIndex = i;
          this.setActiveClass(items);
        };
      });
    },
    removeMouseListeners() {
      const items = this.getItems();
      items.forEach(el => {
        el.onmouseenter = null;
      });
    },
    setFirstActive() {
      const items = this.getItems();
      if (items.length > 0) {
        this.activeIndex = 0;
        this.setActiveClass(items);
      }
    },

    calculatePosition() {
      // Баг фикс, что бы не было ошибки при расчете позиции
      // this.dropdown.style.display = 'none';

      // Получаем размеры экрана
      const screenWidth = window.innerWidth;
      const screenHeight = window.innerHeight;

      // Получаем позицию родительского элемента относительно экрана
      const parentRect = this.parentElement.getBoundingClientRect();

      // Учитываем скролл страницы
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

      const top = parentRect.top + scrollTop;
      const left = parentRect.left + scrollLeft;

      // Показываем элемент для получения его размеров
      // this.dropdown.style.display = 'block';

      // Расчет позиции относительно экрана
      if (this.position === 'right') {
        if (left + this.width > screenWidth) {
          this.dropdown.style.left = left - this.width + 'px';
        } else {
          this.dropdown.style.left = left + 'px';
        }
      }

      if (top + this.dropdown.clientHeight > screenHeight) {
        this.dropdown.style.top = top - this.dropdown.clientHeight + 'px';
      } else {
        this.dropdown.style.top = top + 'px';
      }
    },
  },
};
</script>

<template>
  <Teleport to="body" v-if="showMenu">
    <div class="dropdown" ref="dropdown" :style="dropdownStyle" v-bind="$attrs">
      <slot></slot>
    </div>
  </Teleport>
</template>

<style lang="scss">
.dropdown {
  border: 1px solid lightgray;
  background-color: white;
  padding: 5px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: absolute;
  display: block;
  /* Позволяет меню выходить за пределы родителя */
  // overflow: visible !important;
  /* Увеличиваем z-index для гарантии отображения поверх других элементов */
  z-index: 9999;
  hr {
    margin: 5px 0;
  }

  .item {
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
    transition: background 0.15s, color 0.15s;
    display: block;

    &.active {
      background-color: #f0f0f0;
      color: #2563eb;
    }

    &.disabled {
      pointer-events: none;
      opacity: 0.5;
      cursor: not-allowed;
      background: none;
      color: #888;
    }
  }
}
</style>
