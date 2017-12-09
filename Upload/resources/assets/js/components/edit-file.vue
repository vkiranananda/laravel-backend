<template>
    <form action="params['save-info-url']" role="formAjax" method="POST" >
        <modal id='UploadEditModal' title="Свойства файла">
            <input type="hidden" vb-model="file.id" name="id">
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
        // props: [ url ],
        computed: {
            editFile() { return this.$store.state.uploadStore.editFile },
        },
        watch: {
            editFile() {
                if(this.editFile === false) return;
                $('#UploadEditModal').modal('show');
            }
        },
    }
</script>