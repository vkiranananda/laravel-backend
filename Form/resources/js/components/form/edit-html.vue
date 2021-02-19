<template>
    <div class="edit-form">
        <h3 class="mb-4">{{ conf.title }}</h3>
        <small id="post-link-area" v-if="conf.viewUrl" class="url">
            <a :href="conf.viewUrl" v-html="conf.viewUrl" target="_blank"></a>
        </small>

        <div v-if="countTabs > 1" >
            <ul class="nav nav-tabs" role="tablist">
              <li v-for="(tab, key) in tabs" :key="key" class="nav-item" v-if="tab['v-show'] !== false">
                <a class="nav-link" :class="activeTab == key ? 'active' : ''"   data-toggle="tab" :href="'#tab-'+key" role="tab" v-on:click='changeTab(key)'>
                    <span :class="errorsTab[key]">{{ tab.label }}</span>
                </a>
              </li>
            </ul>
            <!-- Tabs content -->
            <div class="tab-content pt-3 pb-3">
                <div v-for="(tab, key) in tabs" class="tab-pane" :class="activeTab == key ? 'active' : ''" :id="'tab-'+key" role="tabpanel" :key="key" v-if="tab['v-show'] !== false">
                    <fields-list :fields='tab.fields' :errors="errors" store-name='editForm' fields-type='tab'></fields-list>
                </div>
            </div>
        </div>
        <div v-else>
            <fields-list :fields='tabs[activeTab].fields' :errors="errors" store-name='editForm'></fields-list>
        </div>

        <send-form store-name='editForm' :url='conf.url' :method='conf.method'></send-form>


        <div v-if="conf.upload !== false && conf.upload != undefined">
            <upload-files-modal  :url="conf.upload.uploadUrl"></upload-files-modal>
            <upload-edit-file-modal :url="conf.upload.editUrl"></upload-edit-file-modal>
        </div>
        <v-alert></v-alert>
    </div>
</template>


<script>
//    import assign from 'lodash.assign'
    import size from 'lodash.size';
    import fieldsList from './fields.vue'
    import uploadFilesModal from '../uploads/files-modal.vue'
    import uploadEditFileModal from '../uploads/edit-modal.vue'
    import sendForm from './send.vue'

    export default {
        components: {
            'fields-list': fieldsList,
            'upload-files-modal': uploadFilesModal,
            'upload-edit-file-modal': uploadEditFileModal,
            'send-form': sendForm,
        },
        props: {
            fields: {},
            config: {},
        },
        created: function () {
            this.store.dispatch('editForm/initData', { fields: this.fields, config: this.config } );
        },
        computed: {
            tabs () { return this.store.state.editForm.tabs },
            activeTab () { return this.store.state.editForm.tabActive },
            countTabs () { return size(this.tabs) },
            conf () { return this.store.state.editForm.config },
            errors () { return this.store.state.editForm.errors },
            //Реализуем подсветку табов при ошибке
            errorsTab () {
                var res = {};
                for (var tabName in this.tabs) {
                    for (var fieldName in this.tabs[tabName].fields) {
                        if (this.errors[fieldName] != undefined) res[tabName] = 'text-danger';
                    }
                }
                return res;
            }
        },
        methods: {
            changeTab (tab) {
                this.store.commit('editForm/setTabActive', tab );
            }
        }
    }
</script>

<style lang='scss'>
    .edit-form {
        .url{
            position: relative;
            top: -22px;
        }
    }
</style>
