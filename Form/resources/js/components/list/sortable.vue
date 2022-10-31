<template>
    <v-modal name="ListSortableModal" size="large" :loading="loading" title="Сортировка">
        <table class="table table-hover">
            <thead>
            <tr class="table-success">
                <th class="align-middle" scope="col" v-for="field in fields" :key="field.name" v-bind="field.attr">
                    {{ field['label'] }}
                </th>
            </tr>
            </thead>
            <tbody ref="listSortable">
            <tr class="item" v-for="el in list" :data-id="el.id">
                <td v-for="field in fields" :key="field.name">{{ el[field.name] }}</td>
            </tr>
            </tbody>
        </table>
        <template #footer>
            <save-buttons modalName="ListSortableModal" :status="status" v-on:submit="submit"></save-buttons>
        </template>
    </v-modal>
</template>

<script>
import Sortable from '../../../../../resources/js/libs/sortable.js'
import saveButtons from '../form/save-buttons.vue'

export default {
    beforeUnmount() {
        if (this._sortable !== undefined) this._sortable.destroy();
    },
    created() {
        this.emitter.on('ListSortableShow', this.showModal)
    },
    beforeDestroy() {
        this.emitter.off('ListSortableShow', this.showModal)
    },
    components: {
        saveButtons
    },
    data() {
        return {
            list: [],
            resList: [],
            fields: [],
            loading: false,
            url: '',
            status: ''
        }
    },
    methods: {
        showModal(el) {
            this.modal.show('ListSortableModal')

            this.status = ''

            if (this.url != el.url) {

                this.loading = true;

                axios.get(el.url).then((response) => {
                    this.url = el.url;
                    this.loading = false;
                    this.fields = response.data.fields;
                    this.list = response.data.data;

                    // Добавляем возможность сортировки.
                    setTimeout(() => {
                        this._sortable = new Sortable(this.$refs.listSortable, {
                            handle: '.item',
                            easing: "cubic-bezier(1, 0, 0, 1)",
                            animation: 200,
                        });
                    }, 500)
                }).catch((error) => {
                    console.log(error.response)
                });
            }
        },
        submit() {
            // Если элемент сортировки не создан.
            if (this._sortable === undefined) return
            this.status = 'saveing'
            axios({url: this.url, method: 'put', data: {items: this._sortable.toArray()}})
                .then((response) => {
                    this.status = 'saved'
                    this.$emit('v-change')
                    this.modal.hide('ListSortableModal')
                    // Вызываем хуки
                    if (response.data.hook && response.data.hook.name) {
                        this.emitter.emit(response.data.hook.name, response.data.hook.data)
                    }
                })
                .catch((error) => {
                    console.log(error.response)
                });
        }
    }
}
</script>

<style lang='scss'>
#ListSortableModal {
    tr.item {
        cursor: move;
        user-select: none;
        background-color: white;
    }

    .sortable-ghost {
        //background-color: rgba(0, 0, 0, 0.075);
        cursor: move;
    }
}
</style>
