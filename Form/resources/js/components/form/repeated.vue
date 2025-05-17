<template>
    <div class="repeated-fields card">
        <div class="card-body">
            <div ref="repeatedFields">
                <div class="fields-block" :class="field.style ?? ''" v-for="(item, index) in field.value" :key="item.key"
                     :ref="'block' + index">
                    <div class="card">
                        <div class="menu-con" v-if="!field.readonly" :ref="'menu' + index">
                            <div class="text-end">
                                <v-icon name="gear" class="menu-icon" @click="menuOpen(index)"/>
                            </div>
                            <div class="menu" v-if="currentMenuOpen === index">
                                <div class="item mt-1" @click="addNew(index + 1)">
                                    <v-icon name="plus" class="add-icon"/>
                                    Добавить новый элемент
                                </div>
                                <hr class="my-1">
                                <div class="item" :class="index == 0 ? 'disabled' : ''" @click="moveUp(index)">
                                    <v-icon name="arrow-up"/>
                                    Переместить вверх
                                </div>
                                <div class="item" @click="delBlock(index)">
                                    <v-icon name="x" class="remove-icon"/>
                                    Удалить
                                </div>
                                <div class="item" :class="field.value.length -1 == index ? 'disabled' : ''"
                                     @click="moveDown(index)">
                                    <v-icon name="arrow-down"/>
                                    Переместить вниз
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <fields-list :fields="item.fields" :errors="errors[item.key]"></fields-list>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end" v-if="!field.readonly">
                <button slot="footer" type="button" class="btn btn-success" v-on:click.stop.prevent="addNew()">
                    <span>{{ field['add-label'] ? field['add-label'] : 'Добавить' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import formData from '../../store/form-data'
// import Sortable from '../../../../../resources/js/libs/sortable'

export default {
    props: {
        field: {},
        error: undefined,
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() { // Vue 3
        document.removeEventListener('click', this.handleClickOutside);
    },
    data() {
        return {
            currentMenuOpen: false,
            beforeDelete: false
        }
    },
    computed: {
        errors: function () {
            if (this.error == undefined) return {};
            return this.error;
        },
    },
    methods: {
        handleClickOutside(event) {
            if (this.currentMenuOpen !== false) {
                const dropdown = this.$refs['menu' + this.currentMenuOpen][0];
                if (dropdown && !dropdown.contains(event.target)) {
                    this.closeMenu()
                }
            }
        },
        closeMenu () {
            this.currentMenuOpen = false
            this.beforeDelete = false
        },
        addNew(index = false) {
            formData.addRepeatedBlock({field: this.field, index})
        },
        moveUp(index) {
            if (this.currentMenuOpen != false) this.currentMenuOpen--;
            document.documentElement.scrollTop += -this.$refs['block' + (index - 1)][0].offsetHeight;
            formData.moveRepeatedBlock({field: this.field, newIndex: index - 1, oldIndex: index})
        },
        moveDown(index) {
            if (this.currentMenuOpen !== false) this.currentMenuOpen++;
            document.documentElement.scrollTop += this.$refs['block' + (index + 1)][0].offsetHeight;
            formData.moveRepeatedBlock({field: this.field, newIndex: index + 1, oldIndex: index})
        },
        menuOpen(index) {
            this.currentMenuOpen = this.currentMenuOpen === index ? false : index
        },
        delBlock(index) {
            this.closeMenu()
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

    .card-body {

    }

    .fields-block {
        padding-bottom: 20px;
        .menu-con {
            position: absolute;
            right: 5px;
            top: 1px;
            //display: none;

            .menu-icon {
                cursor: pointer;
            }

            .menu {
                border: 1px solid lightgray;
                background-color: white;
                padding: 5px;
                z-index: 100;
                position: relative;
                .item {
                    cursor: pointer;
                    padding: 5px;

                    &:hover {
                        background-color: lightgray;
                    }

                    &.disabled {
                        pointer-events: none;
                        opacity: 0.5;
                        cursor: not-allowed;
                    }
                }

                .remove-icon {
                    fill: red;
                }
                .add-icon {
                    fill: green;
                }

            }

            &:hover {
                > .menu {
                    //display: block;
                    //opacity: 1;
                    //transition: opacity 0.5s; /* 0.5s анимация, 1s задержка */
                }
            }
        }

        &:hover {
            > .menu-con {
                display: block;
            }
        }
        &.editor {
            padding-bottom: 0;
            >.card {
                border-color: transparent;
                &:hover {
                    background-color: #fbfbfb;
                }
            }
        }
    }
}
</style>
