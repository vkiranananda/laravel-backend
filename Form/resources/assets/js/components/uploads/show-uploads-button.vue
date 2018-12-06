<template>
    <div class="show-uploads-button">
        <button type="button" class="text-button btn btn-secondary btn-sm mb-1" @click="show()">Выбрать файл</button>
    </div>
</template>

<script>
    export default {
        props: [ 'config' ],
        data() { return { fieldKey: Math.floor(Math.random() * 100000 ) } },
        computed: {
            //Добавляем свойства с уникальным номером для массива
            newConfig () { return Object.assign({fieldKey: this.fieldKey}, this.config) }
        },
        watch: {
            //Если в конфиге произошли изменения 
            newConfig: function (conf) { 
                // Проверяем, если в хранилище наш конфиг, меняем, если нет не трогаем.
                // Нужно для ограничений выделения элементов для полей files
                if (this.store.state.uploadForm.filesUploadConfig.fieldKey == this.fieldKey)
                    this.saveConfig (conf);
            }
        },
        methods: {
            //Открываем окно аплоадинга файлов.

            show(fileType) { 
                this.saveConfig (this.newConfig);
                this.$bus.$emit('UploadFilesModalShow') 
            },
            saveConfig (config) { this.store.commit('uploadForm/setFilesUploadConfig', config ) }
        }
    }
</script>