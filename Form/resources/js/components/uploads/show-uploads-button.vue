<template>
    <div class="show-uploads-button">
        <button type="button" class="text-button btn btn-secondary btn-sm mb-1" @click="show()">Выбрать файл</button>
    </div>
</template>

<script>
    export default {
        props: [ 'config' ],

        watch: {
            // //Если в конфиге произошли изменения 
            // config: function (conf) { 
            //     // Проверяем, если в хранилище наш конфиг, меняем, если нет не трогаем.
            //     // Нужно для ограничений выделения элементов для полей files
            //     if (this.store.state.uploadForm.filesUploadConfig.fieldKey == this.fieldKey)
            //         this.saveConfig (conf);
            // }
        },
        methods: {
            //Открываем окно аплоадинга файлов.
            show() { 
                this.$bus.$emit('UploadFilesModalShow', this.config) 
            },
        }
    }
</script>