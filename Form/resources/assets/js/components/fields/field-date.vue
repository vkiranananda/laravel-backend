<template>
    <input v-bind="data.attr" :type="data.type" :value="data.value" :name="data.name">
</template>

<script>
    export default {
        props: [ 'data' ],
        created () {
        //    this.$bus.$on('UploadDeleteFile', this.fileDeleted);
          //  this.$bus.$on('UploadAttacheFiles', this.attacheFiles)
          console.log('123');
        },
    }
</script>