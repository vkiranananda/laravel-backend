<template>
  <div class="image-view">
    <slot>
      <img :src="src" :alt="alt" v-if="src" />
      <span v-else>Нет изображения</span>
    </slot>
  </div>
</template>

<script>
export default {
  name: 'ImageView',
    //Создаем слушателей событий
  created() {
    this.emitter.on('FileManagerImageView', this.showModal);
  },
  beforeDestroy() {
    this.emitter.off('FileManagerImageView', this.showModal);
  },

  data() {
    return {
      src: '',
      alt: '',
    };
  },
  methods: {
    showModal(conf) {
      console.log(conf);
    },
  },

}
</script>

<style scoped>
.image-view {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 200px;
  background: #f5f5f5;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
}
.image-view img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}
</style>
