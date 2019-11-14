<template>
    <div class="card">
        <div class="card-body" ref="block">
            <draggable handle=".card" v-model='repBlocks' class="repeated-field">
                <div class="card mb-4" v-for="(block, key) in repBlocks" :key="block.key">
                    <div href='#' @click="delBlock(key)" class="delete" v-if="repBlocks.length > 1">&times;</div>
                    <div class="card-body">
                        <fields-list  :fields='block.fields' :errors="errors[block.key]" :store-name='storeName'></fields-list>
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
        	error: undefined,
            storeName: {
                type: String,
                default: ''
            }
        },
        computed: {
            errors: function () {
                if (this.error == undefined) return {};
                return this.error;
            },
            repBlocks: {
                get () { return this.field.value },
                set (blocks) { this.$emit('change', blocks) }
            }
        },
        methods: {
            addNew () {
                this.store.dispatch(this.storeName+'/addRepeatedBlock', this.field);     
                this.$emit('change', this.repBlocks);
            },
            delBlock (key) {
                if ( confirm('Подтвердите удаление.') ){
                    this.store.commit(this.storeName+'/delRepeatedBlock', { 
                        block: this.field.value,
                        index: key
                    });  
                    this.$emit('change', this.repBlocks);
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
