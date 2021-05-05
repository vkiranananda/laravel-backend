<!-- Для использования в других модулях кроме основного, repeated и group поля работать не будут! -->
<template>
    <div class="row">
        <div v-for="(field, key) in fields" :key="key" v-if="field['v-show'] !== false"
             :class="colWidth(field.row, field['col-classes'])">

            <div v-if="field.type == 'html'" v-html="field.html"></div>

            <template v-else-if="field.type == 'html-title'">
                <h5 v-html="field.title" :class="key == 0  ? 'mt-4' : '' "></h5>
                <hr>
            </template>

            <div v-else-if="field.type == 'repeated'" class="form-group">
                <label v-if="field.label" v-html="field.label"></label>
                <repeated-field :field='field' :store-name='storeName' :error='currentErrors[field.name]'
                                v-on:change="onChange($event, field.name)"></repeated-field>
                <small class="form-text text-muted" v-if="field.desc != ''" v-html="field.desc"></small>
            </div>

            <group-field v-else-if="field.type == 'group'" :field='field' :store-name='storeName'
                         :error='currentErrors[field.name]'></group-field>

            <component
                v-else-if="field.type == 'component'"
                :is="'form-component-'+field.name"
                :field="field"
                :error="(field.name) ? currentErrors[field.name] : {}">
            </component>

            <field-wrapper v-else :error="currentErrors[field.name]" :field="field" @back="onBack(field.name)">
                <print-field :field='field' :fields="fields" :error='currentErrors[field.name]'
                             v-on:change="onChange($event, field.name)"></print-field>
            </field-wrapper>
        </div>
    </div>
</template>
<script>
    import repeatedField from './repeated.vue'
    import groupField from './group.vue'
    import printField from '../fields/field.vue'
    import fieldWrapper from '../fields/wrapper.vue'

    export default {
        created() {
            console.log('fields:', this.fields)
        },
        components: {
            'repeated-field': repeatedField,
            'group-field': groupField,
            'print-field': printField,
            'field-wrapper': fieldWrapper
        },
        props: {
            fields: {},
            storeName: {
                type: String,
                default: ''
            },
            fieldsType: {
                type: String,
                default: ''
            },
            errors: undefined
        },
        computed: {
            currentErrors() {
                if (this.errors != undefined) return this.errors;
                else return {};
            }
        },
        methods: {
            colWidth: function (size, classes) {
                if (classes) return classes
                if (size == 'half') return 'col-6'
                if (size == 'third') return 'col-4'
                return 'col-12'
            },
            onChange: function (value, name) {
                if (this.storeName == '') this.$emit('change', value, name)
                else {
                    let changed = true

                    // Возвращается более сложный объект
                    if (value && value.value != undefined) {
                        // Не сохраняем это изменение
                        if (value.changed === false) changed = false
                        value = value.value
                    }

                    this.store.dispatch(this.storeName + '/setFieldProp', {
                        name,
                        value,
                        changed,
                        fields: this.fields,
                        property: 'value',
                        fieldsType: this.fieldsType,
                    });
                }
            },
            onBack: function (name) {
                vConfirm('Вы действительно хотите вернуть изначальное значение данного поля?', () => {
                    this.store.dispatch(this.storeName + '/setFieldBack', {
                        name,
                        fields: this.fields,
                    });
                })
            }
        }
    }
</script>
