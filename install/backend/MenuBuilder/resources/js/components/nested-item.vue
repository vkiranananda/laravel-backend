<template>
    <draggable
        tag="div"
        class="item-container"
        :list="list"
        item-key="id"
        v-bind="dragOptions"
        @change="change"
    >

        <template #item="{ element }">
            <div class="item-group">
                <div class="item d-flex bd-highlight">
                    <div class="p-2 w-100 bd-highlight">
                        <a :href="element.url" target="_blank">{{ element.label }}</a>
                    </div>
                    <div class="p-2 flex-shrink-1 bd-highlight text-nowrap">
                        <v-icon name="file" class="text-primary me-1" @click="edit(element)"/>
                        <v-icon name="x" class="text-danger" @click="del(element)"/>
                    </div>
                </div>
                <nested-item class="item-sub" :list="element.elements" />
            </div>
        </template>
    </draggable>
</template>

<script>
import draggable from "vuedraggable"

export default {
    inject: ['change', 'edit'],
    name: "nested-item",
    props: {
        list: {
            required: true,
            type: Array,
        }
    },
    components: {draggable},
    methods: {
        del(el) {
            if (confirm('Вы действительно хотите удалить элемент?')) {
                this.list.splice(this.list.indexOf(el), 1)
                this.change()
            }
        },
    },
    computed: {
        dragOptions() {
            return {
                animation: 0,
                group: "description",
                disabled: false,
                ghostClass: "ghost"
            };
        },
    },
};
</script>


<style scoped>
.item {
    cursor: move;
    padding: 0.5rem;
    border: 1px solid rgba(0, 0, 0, 0.125);
    background-color: #fefefe;
}

.item-sub {
    margin: 0 0 0 1rem;
}

.octicon-wrapper {
    cursor: pointer;
}
</style>
