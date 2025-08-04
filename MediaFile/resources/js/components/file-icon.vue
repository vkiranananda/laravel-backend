<template>
  <div class="file-icon">
    <v-icon v-if="file.type === 'folder'" name="folder-fill" class="file-icon__icon-folder" />
    <img v-else-if="file.type === 'image' && file.thumb" :src="file.thumb" draggable="false" />
    <v-icon v-else name="file-fill" class="file-icon__icon-file" />
  </div>
</template>

<script>
export default {
  props: {
    file: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style lang="scss">

</style>