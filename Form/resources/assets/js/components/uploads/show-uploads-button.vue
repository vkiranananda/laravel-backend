<template>
    <div class="show-uploads-button">
        <button type="button" class="text-button btn btn-secondary btn-sm mb-1" @click="show()">Выбрать файл</button>
    </div>
</template>

<script>
    export default {
        props: { config: {} },
        created () { this.fieldKey = Math.floor(Math.random() * 100000 ) },
        data() { return { fieldKey: '' } },
        computed: {
            //Добавляем свойства с уникальным номером для массива
            newConfig () { return Object.assign({fieldKey: this.fieldKey}, this.config) }
        },
        watch: {
            //Пишем конфиг в хранилище
            newConfig: function (conf) { this.store.commit('uploadForm/setFilesUploadConfig', conf ) }
        },
        methods: {
            //Открываем окно аплоадинга файлов.
            show(fileType) { this.$bus.$emit('UploadFilesModalShow') }
        }
    }
</script>