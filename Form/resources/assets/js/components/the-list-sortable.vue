<template>
    <div>
        <edit-form :action="url">
            <modal id="the-list-sortable" size="large" :loading="loading" title="Сортировка">
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
                            <td v-for="field in fields" :key="field.name">
                                {{el[field.name]}}
                                <input type="hidden" v-if="firstField == field.name" :value="el.id" name="list-items[]">
                            </td>
                        </tr>
                    </draggable>
                </table>
                <div slot="footer">
                    <form-buttons modal="#the-list-sortable"></form-buttons>
                </div>
            </modal>
        </edit-form>
    </div>
</template>

<script>
    import draggable from 'vuedraggable'
    export default {
        created () {
            this.$bus.$on('TheListSortableShow', this.showModal)
        },
        beforeDestroy(){
            this.$bus.$off('TheListSortableShow');
        },
        components: {
            draggable,
        },
        data() {
            return {
                list: [],
                fields: [],
                loading: false,
                url: '',
                firstField: '',
            }
        },
        methods: {
            showModal(url)
            {
                $('#the-list-sortable').modal('show');

                if (this.url != url) {
           
                    this.loading = true;
           
                    axios.get(url).then( (response) => {
                        this.url = url;
                        this.loading = false;
                        this.fields = response.data.fields;
                        //Получаем имя первого столбца, что бы вставить инпут хидден в td
                        for (var key in this.fields) {
                            this.firstField = this.fields[key]['name'];
                            break;
                        }
                        this.list = response.data.data;

                    }).catch( (error) => {
                        console.log(error.response);
                    });      
                }
            },
        }
    }
</script>

<style lang='scss'>
    #the-list-sortable {
        tr.item {
            cursor: move;
            user-select: none;
        }

        .sortable-ghost {
            background-color: rgba(0, 0, 0, 0.075);
            cursor: move;
        }
        /*баг в бутрстрап модал*/
        .sortable-fallback { 
            display: none;
        }
    }
</style>