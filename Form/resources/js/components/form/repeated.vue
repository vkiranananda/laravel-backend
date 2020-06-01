<template>
    <div class="card">
        <div class="card-body" ref="block">
            <draggable handle=".move" v-model='repBlocks' class="repeated-field">
                <div class="card mb-4" v-for="(block, key) in repBlocks" :key="block.key">
                    <div class="move" v-if="!field.readonly"></div>
                    <div href='#' @click="delBlock(key)" class="delete" v-if="!field.readonly">&times;</div>
                    <div class="card-body">
                        <fields-list :fields='block.fields' :errors="errors[block.key]"
                                     :store-name='storeName'></fields-list>
                    </div>
                </div>
                <div class="text-right" v-if="!field.readonly">
                    <button slot="footer" type="button" class="btn btn-success" v-on:click.stop.prevent="addNew">
                        <span>{{field['add-label'] ? field['add-label'] : 'Добавить'}}</span>
                    </button>
                </div>
            </draggable>
        </div>
    </div>
</template>
<!-- v-if="repBlocks.length > 1" -->
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
                get() {
                    return this.field.value
                },
                set(blocks) {
                    this.$emit('change', blocks)
                }
            }
        },
        methods: {
            addNew() {
                this.store.dispatch(this.storeName + '/addRepeatedBlock', this.field);
                this.$emit('change', this.repBlocks);
            },
            delBlock(key) {
                vConfirm('Подтвердите удаление.', () => {
                    this.store.commit(this.storeName + '/delRepeatedBlock', {
                        block: this.field.value,
                        index: key
                    });
                    this.$emit('change', this.repBlocks);
                })
            }
        }
    }
</script>


<style lang='scss'>
    .repeated-field {
        .delete {
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

        .move {
            position: absolute;
            top: 0;
            left: 0;
            width: 10px;
            height: 100%;
            background-color: rgb(246, 246, 246);
            cursor: move;
        }
    }
</style>
