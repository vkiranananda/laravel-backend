<template>
    <div class="list">
        <div v-if="items.data.length > 0">
            <div class="row mb-3">
                <div class="col-auto me-auto"></div>
                <div class="col-auto">
                    <list-paginate :data="items" @v-change="pageChange"></list-paginate>
                </div>
            </div>
            <table class="table table-hover h-100" id="table-list">
                <thead>
                <tr class="table-light">
                    <th v-for="(field, key) in fields" :key="field.name" class="align-middle" scope="col"
                        v-bind="field.attr">
                        <div v-if="field.sortable != undefined" class="sortable"
                             :class="field.sortable === true ? 'none' : field.sortable" @click="sortable(key)">
                            {{ field.label }}
                            <v-icon name="chevron-down" class="down"/>
                            <v-icon name="chevron-up" class="up"/>
                        </div>
                        <span v-else>{{ field.label }}</span>
                    </th>
                    <th scope="col" class="menu-td" v-if="itemMenu"></th>
                </tr>
                </thead>
                <tbody class="position-static">
                <tr v-for="item in items.data" :class="item['_row_class']">
                    <td v-for="field in fields" v-bind="field.attr" class="position-relative"
                        :class="field.editable  ? 'editable' : ''">
                        <v-icon :name="field.icon" class="me-2" v-if="field.icon"/>
                        <a v-if="field.link" :href="item._links[field.link]">{{ item[field.name].value }}</a>
                        <template v-else>
                            <span v-if="field.html === true" v-html="item[field.name].value"></span>
                            <template v-else>{{ item[field.name].value }}</template>
                        </template>
                        <a v-if="field.editable && item[field.name].config" class="editable-link text-primary" href="#"
                           @click.stop.prevent="editField(item[field.name])">
                            <v-icon name="pencil"/>
                        </a>
                    </td>

                    <td class="menu-td" v-if="itemMenu">
                        <div v-if="checkItemMenuEmpty(item._links)" class="dropdown" data-boundary="window">
                            <button class="btn btn-secondary" type="button" id="dropdownMenuButton"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <v-icon name="grabber" width="10"/>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <template v-for="elMenu in itemMenu">
                                    <li>
                                        <a v-if="item._links[elMenu.link] != undefined"
                                           class="dropdown-item"
                                           v-on:click.stop.prevent="itemActionClick(item._links[elMenu.link], elMenu)">
                                            <v-icon :name="elMenu.icon" class="me-2" v-if="elMenu.icon"/>
                                            {{ elMenu.label }}
                                        </a>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="col-auto me-auto"></div>
                <div class="col-auto">
                    <list-paginate :data="items" @v-change="pageChange"></list-paginate>
                </div>
            </div>
        </div>
        <p v-else class="pt-1">Ничего не найдено</p>
        <edit-field @v-change="pageChange"/>
    </div>
</template>

<script>
import paginate from './paginate.vue'
import editField from './edit-field.vue'

export default {
    props: ['items', 'fields', 'itemMenu'],
    components: {
        'list-paginate': paginate,
        'edit-field': editField,
    },

    methods: {
        editField: function (data) {
            this.emitter.emit('ListEditableShow', data)
        },
        pageChange: function (value) {
            this.$emit('v-change', {currentPage: value})
        },
        sortable: function (key) {
            let field = this.fields[key], orderType;

            if (field.sortable === true) orderType = 'asc';
            else if (field.sortable === 'asc') orderType = 'desc';
            else if (field.sortable === 'desc') orderType = true;

            this.$emit('v-change', {sortable: key, orderType})
        },
        itemActionClick: function (url, el) {
            if (el.confirm) {
                this.msgConfirm(el.confirm, () => {
                    this.itemAction(url, el)
                })
            } else this.itemAction(url, el)

        },
        itemAction: function (url, el) {
            if (el.link == 'destroy') this.deleteItem(url)
            else {
                if (el.target) window.open(url, el.target)
                else document.location.href = url
            }
        },
        checkItemMenuEmpty: function (links) {
            for (let item in this.itemMenu) {
                if (links[this.itemMenu[item].link]) return true;
            }
            return false
        },
        //Удаляем элемент
        deleteItem: function (url) {

            this.$emit('v-change', {destroy: 'begin'});

            axios.delete(url)
                .then((response) => {
                    this.$emit('v-change', {destroy: 'finished'});
                    //Вызываем хуки
                    if (response.data.hook != undefined && response.data.hook.name) {
                        this.emitter.emit(response.data.hook.name, response.data.hook.data)
                    }
                })
                .catch((error) => {
                    if (error.response.status == 403) {
                        this.msgAlert(error.response.data.message);
                    }
                    console.log(error.response);
                    this.$emit('v-change', {destroy: 'error'});
                });
        }
    }
}
</script>

<style lang='scss'>
.list {
    .sortable {
        cursor: pointer;
        position: relative;
        display: inline-block;
        padding-right: 10px;

        .octicon-wrapper {
            display: none;
            position: absolute;
            margin-left: 5px;
            top: 0px;
            right: -8px;

            &.up {
                top: 2px;
            }
        }

        &.none {

            &:hover {
                .down {
                    display: inline;
                }
            }
        }

        &.asc {
            .down {
                display: inline;
            }

            &:hover {
                .up {
                    display: inline;
                }

                .down {
                    display: none;
                }
            }
        }

        &.desc {
            .up {
                display: inline;
            }

            &:hover {
                .down {
                    display: inline;
                }

                .up {
                    display: none;
                }
            }
        }
    }

    .file-directory {
        font-size: 22px;
        margin-right: 10px;
        vertical-align: middle;
    }

    tr {
        a {
            text-decoration: none;
        }

        .dropdown {
            display: none;

            a {
                cursor: pointer;
            }
        }

        &:hover {
            .dropdown {
                display: block;
            }
        }

        td {
            &.editable {
                //padding-right: 20px;
                //padding-left: 20px;

                &:hover {
                    .editable-link {
                        opacity: 1;
                    }
                }
            }

            .editable-link {
                position: absolute;
                top: 7px;
                right: -1px;
                opacity: 0.2;
            }
        }
    }

    .menu-td {
        vertical-align: middle;
        padding-top: 0px;
        padding-bottom: 0px;
        width: 60px;
    }
}
</style>
