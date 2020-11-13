<template>
    <div class="show-form">
        <h3 class="mb-4">{{ conf.title }}</h3>
        <small id="post-link-area" v-if="conf.viewUrl" class="url">
            <a :href="conf.viewUrl" v-html="conf.viewUrl" target="_blank"></a>
        </small>

        <div v-if="countTabs > 1">
            <ul class="nav nav-tabs" role="tablist">
                <li v-for="(tab, key) in tabs" :key="key" class="nav-item" v-if="tab['v-show'] !== false">
                    <a class="nav-link" :class="activeTab == key ? 'active' : ''" data-toggle="tab" :href="'#tab-'+key"
                       role="tab">
                        <span>{{ tab.label }}</span>
                    </a>
                </li>
            </ul>
            <!-- Tabs content -->
            <div class="tab-content pt-3 pb-3">
                <div v-for="(tab, key) in tabs" class="tab-pane" :class="activeTab == key ? 'active' : ''"
                     :id="'tab-'+key" role="tabpanel" :key="key" v-if="tab['v-show'] !== false">
                    <fields-list :fields='tab.fields' store-name='editForm'
                                 fields-type='tab'></fields-list>
                </div>
            </div>
        </div>
        <div v-else>
            <fields-list :fields='tabs[activeTab].fields' store-name='editForm'></fields-list>
        </div>

        <div class="text-right">
            <a v-for="btn in conf.buttons" :href="btn.url" type="button" class="btn ml-2"
               :class="btn.type ? 'btn-'+btn.type : 'btn-primary'">
                {{ btn.label }}
            </a>
        </div>
    </div>
</template>


<script>
//    import assign from 'lodash.assign'
import size from 'lodash.size';
import fieldsList from './fields.vue'

export default {
    components: {
        'fields-list': fieldsList,
    },
    props: {
        fields: {},
        config: {},
    },
    created: function () {
        this.store.dispatch('editForm/initData', {fields: this.fields, config: this.config});
    },
    computed: {
        tabs() {
            return this.store.state.editForm.tabs
        },
        activeTab() {
            return this.store.state.editForm.tabActive
        },
        countTabs() {
            return size(this.tabs)
        },
        conf() {
            return this.store.state.editForm.config
        }
    },
}
</script>

<style lang='scss'>
.show-form {
    .url {
        position: relative;
        top: -22px;
    }
}
</style>
