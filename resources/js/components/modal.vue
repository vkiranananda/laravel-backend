<!-- 
Компонент modal.vue

Описание:
Данный компонент реализует модальное окно с поддержкой различных размеров, центрированием, индикатором загрузки, настраиваемым заголовком и кнопкой закрытия. Также поддерживается отображение подложки (backdrop) и пользовательских слотов для содержимого и футера.

Свойства (props):
- size: Размер модального окна. Возможные значения: 'large', 'medium', 'small', либо пустая строка для стандартного размера.
- loading: Булево значение. Если true, отображается индикатор загрузки вместо содержимого.
- title: Заголовок модального окна.
- closeButton: Булево значение. Если true, в футере отображается стандартная кнопка "Закрыть".
- closeModal: Булево значение. Если false, окно нельзя закрыть (скрывается кнопка закрытия и отключается закрытие по клику на подложку).

Слоты:
- По умолчанию: Основное содержимое модального окна.
- footer: Пользовательский футер. Если не задан и closeButton=true, отображается стандартная кнопка "Закрыть".

Пример использования:
<modal
  :size="'large'"
  :loading="isLoading"
  :title="'Заголовок'"
  :closeButton="true"
  :closeModal="true"
>
  <div>Контент модального окна</div>
  <template #footer>
    <button class="btn btn-primary">Сохранить</button>
  </template>
</modal>
role="submit"]
-->

<template>
  <div class="modal fade show" ref="modal" v-bind="{ class: [$attrs.class] }">
    <div
      class="modal-dialog"
      :class="{
        'modal-dialog-centered': centerAlign,
        'modal-xl': size == 'large',
        'modal-lg': size == 'medium',
        'modal-sm': size == 'small',
      }"
      role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ title }}</h5>
          <button
            v-if="closeModal"
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
            @click="hide"></button>
        </div>
        <div class="modal-body">
          <div class="text-center" v-if="loading">
            <img src="/backend/images/loading5.gif" alt="" />
          </div>
          <slot v-else></slot>
        </div>
        <div class="modal-footer" slot="footer">
          <div v-if="closeButton" class="text-end">
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Закрыть</button>
          </div>
          <slot v-else name="footer"></slot>
        </div>
      </div>
    </div>
  </div>
  <div v-if="showBackdrop" class="modal-backdrop" @click="closeModal ? hide() : null"></div>
</template>

<script>
export default {
  props: {
    size: { default: '' },
    loading: { default: false },
    title: {},
    closeButton: { default: false },
    // Если false то модальное окно нельзя будет закрыть
    closeModal: {
      type: Boolean,
      default: true,
    },
    name: {
      default: '',
      type: String,
    },
    centerAlign: {
      default: true,
      type: Boolean,
    },
  },
  data() {
    return {
      showBackdrop: false,
    };
  },
  created() {
    if (this.name !== '') {
      this.emitter.on(this.name, this.action);
    }
  },
  beforeDestroy() {
    if (this.name !== '') {
      this.emitter.off(this.name, this.action);
    }
    // Удаляем обработчик при уничтожении компонента
    if (this.$refs.modal) {
      this.$refs.modal.removeEventListener('keydown', this.handleKeydown);
    }
  },
  mounted() {
    this.modal = $(this.$refs.modal);
    // Добавляем обработчик нажатия Enter
    this.$refs.modal.addEventListener('keydown', this.handleKeydown);
  },
  methods: {
    action: function (data) {
      switch (data.action) {
        case 'show':
          this.show();
          break;
        case 'hide':
          this.hide();
          break;
        default:
          break;
      }
    },
    show: function () {
      this.modal.show();
      this.showBackdrop = true;

      this.$nextTick(() => {
        const firstInput = this.$refs.modal.querySelector('input, textarea, select');
        if (firstInput) {
          firstInput.focus();
        }
      });
    },
    hide: function () {
      this.modal.hide();
      this.showBackdrop = false;
    },
    handleKeydown: function (event) {
      // Если нажат Enter
      if (event.key === 'Enter') {
        // Проверяем, что активный элемент не является textarea, select или input с type="text"
        const activeElement = document.activeElement;
        const tagName = activeElement.tagName.toLowerCase();
        const inputType = activeElement.type;

        // Если активный элемент - textarea, select или input с type="text", не обрабатываем Enter
        if (tagName === 'textarea' || tagName === 'select') {
          return;
        }

        // Ищем кнопку с role="submit" в модальном окне
        const submit = this.$refs.modal.querySelector('[role="submit"]');
        if (submit) {
          event.preventDefault();
          submit.click(); // Вызываем клик на кнопку
        }
      }
    },
  },
  computed: {
    classSize: function () {
      if (this.size == 'large') {
        return 'modal-lg';
      }
      if (this.size == 'medium') {
        return 'modal-md';
      }
      return 'modal-sm';
    },
    loadingSize() {
      return this.size == 'large' ? 100 : 35;
    },
  },
};
</script>

<style lang="scss">
.modal {
  overflow: auto !important;
}
.modal-backdrop {
  position: fixed;
  z-index: 1040;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5); /* полупрозрачный чёрный */
}
</style>
