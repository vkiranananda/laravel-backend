<template>
    <div class="edit-form">
        <h3 class="mb-4">{{ conf.title }}</h3>
        <small id="post-link-area" v-if="conf.viewUrl" class="url">
            <a :href="conf.viewUrl" v-html="conf.viewUrl" target="_blank"></a>
        </small>

        <div v-if="countTabs > 1">
            <ul class="nav nav-tabs" role="tablist">
                <template v-for="(tab, key) in tabs" :key="key">
                    <li class="nav-item" v-if="tab['v-show'] !== false">
                        <button class="nav-link" :class="activeTab == key ? 'active' : ''" data-bs-toggle="tab"
                                :data-bs-target="'#tab-'+key" role="tab" type="button" v-on:click='changeTab(key)'>
                            <span :class="errorsTab[key]">{{ tab.label }}</span>
                        </button>
                    </li>
                </template>
            </ul>
            <!-- Tabs content -->
            <div class="tab-content pt-3 pb-3">
                <template v-for="(tab, key) in tabs" :key="key">
                    <div class="tab-pane" :class="activeTab == key ? 'active' : ''"
                         :id="'tab-'+key" role="tabpanel" v-if="tab['v-show'] !== false">
                        <fields-list :fields="tab.fields" :errors="errors" fields-type='tab' :key="dataKey"></fields-list>
                    </div>
                </template>
            </div>
        </div>
        <div v-else>
            <fields-list :fields='tabs[activeTab].fields' :errors="errors"  :key="dataKey"></fields-list>
        </div>

        <send-form :url='conf.url' :method='conf.method'></send-form>

        <div v-if="conf.upload !== false && conf.upload != undefined">
            <upload-files-modal :url="conf.upload.uploadUrl"></upload-files-modal>
            <upload-edit-file-modal :url="conf.upload.editUrl"></upload-edit-file-modal>
        </div>

        <v-alert></v-alert>
    </div>
</template>


<script>

import size from 'lodash.size'
import fieldsList from './fields.vue'
import uploadFilesModal from '../uploads/files-modal.vue'
import uploadEditFileModal from '../uploads/edit-modal.vue'
import sendForm from './send.vue'
import formData from '../../store/form-data.js'
import {computed} from "vue";

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
    setup(props) {
        formData.initData({fields: props.fields, config: props.config})
        return {
            conf: formData.config,
            tabs: formData.tabs,
            activeTab: formData.tabActive,
            errors: formData.errors,
            dataKey: formData.dataKey,
            errorsTab: computed(() => {
                var res = {};
                for (let tabName in formData.tabs.value) {
                    for (let fieldName in formData.tabs.value[tabName].fields) {
                        if (formData.errors.value[fieldName] != undefined) res[tabName] = 'text-danger';
                    }
                }
                return res;
            }),
            countTabs: computed(() => {
                return size(formData.tabs.value)
            }),
            changeTab: (tab) => {
                formData.setTabActive(tab);
            },
        }
    },
}

</script>

<style lang='scss'>
.edit-form {
    .url {
        position: relative;
        top: -22px;

        a {
            text-decoration: none;
        }
    }
}
</style>
