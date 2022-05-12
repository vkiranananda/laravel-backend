<template>
    <div class="card">
        <div class="card-body" ref="block">
            <div class="repeated-fields" ref="repeatedFields">
                <div class="card mb-4" v-for="(item, index) in field.value" :key="item.key">
                    <div class="move" v-if="!field.readonly"></div>
                    <div href='#' @click="delBlock(index)" class="delete" v-if="!field.readonly">&times;</div>
                    <div class="card-body">
                        <fields-list :fields="item.fields" :errors="errors[item.key]"></fields-list>
                    </div>
                </div>
            </div>
            <div class="text-end" v-if="!field.readonly">
                <button slot="footer" type="button" class="btn btn-success" v-on:click.stop.prevent="addNew">
                    <span>{{ field['add-label'] ? field['add-label'] : 'Добавить' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import formData from '../../store/form-data'
import Sortable from '../../../../../resources/js/libs/sortable'

export default {
    props: {
        field: {},
        error: undefined,
    },
    mounted() {
        this._sortable = new Sortable(this.$refs.repeatedFields, {
            disabled: this.field.readonly ? true : false,
            handle: '.move',
            animation: 300,
            easing: "cubic-bezier(1, 0, 0, 1)",
            onEnd: (evt) => {
                formData.moveRepeatedBlock({field: this.field, newIndex: evt.newIndex, oldIndex: evt.oldIndex})
            }
        });
    },
    beforeUnmount() {
        if (this._sortable !== undefined) this._sortable.destroy();
    },
    computed: {
        errors: function () {
            if (this.error == undefined) return {};
            return this.error;
        },
    },
    methods: {
        addNew() {
            formData.addRepeatedBlock(this.field)
        },
        delBlock(index) {
            this.msgConfirm('Подтвердите удаление.', () => {
                formData.delRepeatedBlock({field: this.field, index})
            })
        }
    }
}
</script>


<style lang='scss'>
.repeated-fields {
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
