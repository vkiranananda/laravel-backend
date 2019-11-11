<template>
    <modal id="ListSortableModal" size="large" :loading="loading" title="Сортировка">
        <table class="table table-hover">
            <thead>
                <tr class="table-success">
                    <th class="align-middle" scope="col" v-for="field in fields" :key="field.name" v-bind="field.attr">
                        {{field['label']}}
                    </th>
                </tr>
            </thead>
            <draggable v-model="list" :element="'tbody'" :options="{forceFallback: true}">
                <tr v-for="el in list" :key="el.id"  class="item">
                    <td v-for="field in fields" :key="field.name">{{el[field.name]}}</td>
                </tr>
            </draggable>
        </table>
        <div slot="footer">
            <save-buttons modal="#ListSortableModal" :status="status" v-on:submit="submit"></save-buttons>
        </div>
    </modal>
</template>

<script>
    import draggable from 'vuedraggable'
    import saveButtons from '../form/save-buttons'
    export default {
        created () {
            this.$bus.$on('ListSortableShow', this.showModal)
        },
        beforeDestroy() { this.$bus.$off('ListSortableShow') },
        components: { 
            draggable,
            'save-buttons': saveButtons
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
                console.log(el)
                this.status = ''

                if (this.url != el.url) {
           
                    this.loading = true;
           
                    axios.get(el.url).then( (response) => {
                        this.url = el.url;
                        this.loading = false;
                        this.fields = response.data.fields;
                        this.list = response.data.data;

                    }).catch( (error) => { console.log(error.response) });      
                }
            },
            submit() {
                let items = []
                for (var key in this.list) items[key] = this.list[key].id

                this.status = 'saveing' 
                
                axios({url: this.url, method: 'put', data: {items} }).then( (response) => {
                    this.status = 'saved'
                    this.$emit('change')
                    
                    // Вызываем хуки
                    if (response.data.hook != undefined && response.data.hook.name) {
                        this.$bus.$emit(response.data.hook.name, response.data.hook.data)
                    }

                }).catch( (error) => { console.log(error.response) });
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