<template>
    <!--  Редактирование полей  -->
    <v-modal name="IndexEditableModal" size="large" :loading="loading"
           :title="'Изменить значение поля ' + label">
        <div v-if="field.type" class="mt-1 mb-3">
            <fieldWrapper :field="field" :error="error">
                <print-field :error="error" :field="field"
                             @v-change="change($event)"></print-field>
            </fieldWrapper>
        </div>
        <div slot="footer">
            <save-buttons modal-name="IndexEditableModal" :status="status" v-on:submit="update"></save-buttons>
        </div>
    </v-modal>
</template>

<script>
import printField from '../fields/field.vue'
import fieldWrapper from '../fields/wrapper.vue'
import saveButtons from '../form/save-buttons.vue'

export default {
    name: "edit-field",
    components: {
        printField, saveButtons, fieldWrapper
    },
    beforeUnmount() {
        if (this._sortable !== undefined) this._sortable.destroy();
    },
    created() {
        this.emitter.on('ListEditableShow', this.showModal)
    },
    beforeDestroy() {
        this.emitter.off('ListEditableShow', this.showModal)
    },
    data() {
        return {
            loading: false,
            error: '',
            lastUrl: '',
            updateUrl: '',
            label: '',
            field: {},
            status
        }
    },
    methods: {
        showModal: function (data) {
            $('#IndexEditableModal').modal('show');
            if (data.config.editUrl == this.lastUrl) return
            this.loading = true

            axios.get(data.config.editUrl).then((response) => {
                this.lastUrl = data.config.editUrl
                this.field = response.data.field
                this.label = this.field.label
                this.field.label = undefined
                this.updateUrl = response.data.config.updateUrl
            }).catch((error) => {
                console.log(error.response)
            }).then(() => {
                this.loading = false
            })
        },
        change: function (value) {
            this.field.value = value
        },
        update: function () {
            this.status = 'saveing'
            axios({
                url: this.updateUrl, method: 'PUT', data: {value: this.field.value}
            }).then((response) => {
                this.status = 'saved'
                this.$emit('v-change')
            }).catch((error) => {
                if (error.response && error.response.status == 422) {
                    console.log(error.response.data.error);
                    this.error = error.response.data.error
                    this.status = 'errorFields';
                } else {
                    this.status = 'errorAny';
                    console.log(error.response);
                }
            })

        }
    }
}
</script>

<style scoped>

</style>
