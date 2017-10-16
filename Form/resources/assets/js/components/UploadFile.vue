<template>
    <div data-upload-url="" data-delete-url="">
        <div data-name="errorsArea"></div>
        
        <div class='file-upload' data-name="filesArea" >
            <div class='upload-button item-smoll' v-on:click="selectFile"></div>
            <slot></slot>

      </div>

      <input type="file" class="hidden-xs-up" ref="inputFile" multiple>
    </div>
</template>

<script>

    Vue.component('file', require('./File.vue'))
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
            selectFile: function () {
                this.$refs.inputFile.click();
                console.log('Component mounted2.')
               // conteiner.find("input[type=file]").click();
            }
        },
    }
</script>
