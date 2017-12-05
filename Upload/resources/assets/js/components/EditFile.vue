<template>
    <form :action="params['save-info-url']" role="formAjax" method="POST" >
        <modal id='UploadEditModal' title="Свойства файла">
            <input type="hidden" v-model="file.id" name="id">
            <slot></slot>
            <div slot="footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" role="submit" data-send-text="Сохраняю...">Сохранить</button>
            </div>
        </modal>
    </form>
</template>

<script>
    export default {
        mounted () {
            console.log(this.params);
        },
         computed: {
            file() { return this.$store.state.uploadStore.editingFile },
            params() { return this.$store.state.uploadStore.params },
        },
    }
</script>