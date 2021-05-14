<template>
    <div class="show-uploads-button">
        <button type="button" class="text-button btn btn-secondary btn-sm mb-1" @click="show()">Выбрать файл</button>
    </div>
</template>

<script>
import mitt from 'mitt'

const emitter = mitt()

export default {
    props: ['config'],

    methods: {
        show() {
            emitter.emit('UploadFilesModalShow', this.config)
        },
    }
}
</script>
