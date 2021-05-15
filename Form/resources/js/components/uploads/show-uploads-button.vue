<template>
    <div class="show-uploads-button">
        <button type="button" class="text-button btn btn-secondary btn-sm mb-1" @click="show()">Выбрать файл</button>
    </div>
</template>

<script>

export default {
    props: ['config'],

    methods: {
        show() {
            this.emitter.emit('UploadFilesModalShow', this.config)
        },
    }
}
</script>
