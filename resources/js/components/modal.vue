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
      default: false,
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
  },
  mounted() {
    this.modal = $(this.$refs.modal);
  },
  methods: {
    action: function (data) {
      console.log('action', data);
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
    },
    hide: function () {
      this.modal.hide();
      this.showBackdrop = false;
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
