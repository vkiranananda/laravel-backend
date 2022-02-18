<template>
    <div class="card">
        <div class="card-body" ref="block">
            <div class="repeated-field">
                <draggable v-model="repBlocks" item-key="id" handle=".move">
                    <template #item="{element}">
                        <div class="card mb-4">
                            <div class="move" v-if="!field.readonly"></div>
                            <div href='#' @click="delBlock(element)" class="delete" v-if="!field.readonly">&times;</div>
                            <div class="card-body">
                                <fields-list :fields="element.fields" :errors="errors[element.key]"></fields-list>
                            </div>
                        </div>
                    </template>
                </draggable>
                <div class="text-end" v-if="!field.readonly">
                    <button slot="footer" type="button" class="btn btn-success" v-on:click.stop.prevent="addNew">
                        <span>{{ field['add-label'] ? field['add-label'] : 'Добавить' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import draggable from 'vuedraggable'
import formData from '../../store/form-data'
import cloneDeep from 'lodash.clonedeep'

export default {
    components: {
        draggable,
        // 'fields-list': fieldsList2,
    },
    props: {
        field: {},
        error: undefined,
    },
    // data() {
    //   return {
    //       repBlocks: [{}]
    //   }
    // },
    computed: {
        errors: function () {
            if (this.error == undefined) return {};
            return this.error;
        },
        repBlocks: {
            get() {
                return cloneDeep(this.field.value)
            },
            set(blocks) {
                this.$emit('v-change', blocks)
            }
        }
    },
    methods: {
        addNew() {
            formData.addRepeatedBlock(this.field)
            // this.$emit('v-change', this.repBlocks);
        },
        delBlock(el) {
            this.msgConfirm('Подтвердите удаление.', () => {
                formData.delRepeatedBlock({
                    field: this.field,
                    index: this.repBlocks.indexOf(el)
                })
                // this.$emit('change', this.repBlocks);
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
