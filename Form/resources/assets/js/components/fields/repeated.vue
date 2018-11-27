<template>
    <div class="card">
        <div class="card-body">
            <draggable :options="{draggable:'.card', element: 'md-list'}" v-model='repBlocks' class="repeated-field" >
                <div class="card mb-4" v-for="(block, key) in repBlocks" :key="block.index">
                    <div href='#' @click="delBlock(key)" class="delete" v-if="repBlocks.length > 1">&times;</div>
                    <div class="card-body">
                        <fields-list  :fields='block.fields' :store-name='storeName'></fields-list>
                    </div>
                </div>
                <div class="text-right">
                <button slot="footer" type="button" class="btn btn-success" v-on:click.stop.prevent="addNew">
                    <span>Добавить</span>
                </button>
            </div>
            </draggable>
        </div>
    </div>
</template>

<script>
    // import fieldsList2 from '../form/fields.vue'
    import draggable from 'vuedraggable'
    export default {
        components: {
            draggable,
            // 'fields-list': fieldsList2, 
        },
        props: { 
        	field: {},
        	error: {
        		type: String,
        		default: ''
        	},
            storeName: {
                type: String,
                default: ''
            }
        },
        computed: {
            repBlocks: {
                get () { return this.field.data },
                set (blocks) {
                    this.store.commit(this.storeName+'/sortRepeatedBlocks', { 
                        field: this.field, 
                        value: blocks
                    }); 
                }
            }
        },
        methods: {
            addNew () {
                this.store.dispatch(this.storeName+'/addRepeatedBlock', this.field);     
            },
            delBlock (key) {
                if ( confirm('Подтвердите удаление.') ){
                    this.store.commit(this.storeName+'/delRepeatedBlock', { 
                        block: this.field.data,
                        index: key
                    });  
                }
            }
        }
    }
</script>



<style lang='scss'>
    .repeated-field {
        .delete{
            position: absolute;
            right: 5px;
            top: -8px;
            font-size: 28px;
            text-align: center;
            color: red;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }
    }
</style>
