<template>
    <modal id="ListSortableModal" size="large" :loading="loading" title="Сортировка">
        <table class="table table-hover">
            <thead>
            <tr class="table-success">
                <th class="align-middle" scope="col" v-for="field in fields" :key="field.name" v-bind="field.attr">
                    {{ field['label'] }}
                </th>
            </tr>
            </thead>
            <tbody ref="listSortable">
            <tr class="item" v-for="el in list">
                <td v-for="field in fields" :key="field.name">{{ el[field.name] }}</td>
            </tr>
            </tbody>
        </table>
        <div slot="footer">
            <save-buttons modal="#ListSortableModal" :status="status" v-on:submit="submit"></save-buttons>
        </div>
    </modal>
</template>

<script>
import Sortable from '../../../../../resources/js/libs/sortable'
import saveButtons from '../form/save-buttons'

export default {
    mounted() {
        this._sortable = new Sortable(this.$refs.listSortable, {
            // animation: 300,
            handle: '.item',
            // easing: "cubic-bezier(1, 0, 0, 1)",
            onEnd: (evt) => {
                let oldEl = this.list[evt.oldIndex]
                this.list[evt.oldIndex] = this.list[evt.newIndex]
                this.list[evt.newIndex] = oldEl
            }
        });
    },
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
            fields: [],
            loading: false,
            url: '',
            status: ''
        }
    },
    methods: {
        showModal(el) {
            $('#ListSortableModal').modal('show');
            this.status = ''

            if (this.url != el.url) {

                this.loading = true;

                axios.get(el.url).then((response) => {
                    this.url = el.url;
                    this.loading = false;
                    this.fields = response.data.fields;
                    this.list = response.data.data;

                }).catch((error) => {
                    console.log(error.response)
                });
            }
        },
        submit() {
            let items = []
            for (var key in this.list) items[key] = this.list[key].id

            this.status = 'saveing'

            axios({url: this.url, method: 'put', data: {items}}).then((response) => {
                this.status = 'saved'
                this.$emit('v-change')

                // Вызываем хуки
                if (response.data.hook != undefined && response.data.hook.name) {
                    this.emitter.emit(response.data.hook.name, response.data.hook.data)
                }

            }).catch((error) => {
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
    }

    .sortable-ghost {
        background-color: rgba(0, 0, 0, 0.075);
        cursor: move;
    }
}
</style>
