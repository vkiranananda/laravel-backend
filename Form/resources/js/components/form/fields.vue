<!-- Для использования в других модулях кроме основного, repeated и group поля работать не будут! -->
<template>
    <div class="row">
        <template v-for="(field, key) in fields" :key="key">
            <div v-if="field['v-show'] !== false" class="mb-2"
                 :class="colWidth(field.row, field['col-classes'])">
                <div v-if="field.type == 'html'" v-html="field.html"></div>

                <template v-else-if="field.type == 'html-title'">
                    <h5 v-html="field.title" :class="key == 0  ? 'mt-4' : '' "></h5>
                    <hr>
                </template>

                <div v-else-if="field.type == 'repeated'" class="form-group">
                    <label v-if="field.label" v-html="field.label"></label>
                    <repeated-field :field='field' :store='store' :error='currentErrors[field.name]'
                                    v-on:v-change="onChange($event, field.name)"></repeated-field>
                    <small class="form-text text-muted" v-if="field.desc != ''" v-html="field.desc"></small>
                </div>

                <group-field v-else-if="field.type == 'group'" :field='field' :store='store'
                             :error='currentErrors[field.name]'></group-field>

                <component
                    v-else-if="field.type == 'component'"
                    :is="'form-component-'+field.name"
                    :field="field"
                    :error="(field.name) ? currentErrors[field.name] : {}">
                </component>
                <field-wrapper v-else-if="field.type != 'hidden'" :error="currentErrors[field.name]" :field="field"
                               @v-back="onBack(field.name)">
                    <print-field :field="field" :fields="fields" :error="currentErrors[field.name]"
                                 @v-change="onChange($event, field.name)"></print-field>
                </field-wrapper>
            </div>
        </template>
    </div>
</template>
<script>
import repeatedField from './repeated.vue'
import groupField from './group.vue'
import printField from '../fields/field.vue'
import fieldWrapper from '../fields/wrapper.vue'
import formData from '../../store/form-data'
import {computed, inject} from "vue";

export default {
    components: {
        'repeated-field': repeatedField,
        'group-field': groupField,
        'print-field': printField,
        'field-wrapper': fieldWrapper
    },
    props: {
        fields: {},
        store: {
            type: Boolean,
            default: true
        },
        fieldsType: {
            type: String,
            default: ''
        },
        errors: undefined
    },
    setup(props) {
        const msgConfirm = inject('msgConfirm')
        return {
            errorsTab: computed(() => {
                var res = {};
                for (let tabName in formData.tabs.value) {
                    for (let fieldName in formData.tabs.value[tabName].fields) {
                        if (formData.errors.value[fieldName] != undefined) res[tabName] = 'text-danger';
                    }
                }
                return res;
            }),
            currentErrors: computed(() => {
                if (props.errors != undefined) return props.errors;
                else return {};
            }),

            colWidth: function (size, classes) {
                if (classes) return classes
                if (size == 'half') return 'col-6'
                if (size == 'third') return 'col-4'
                return 'col-12'
            },
            onChange: function (value, name) {
                // Если хранилище не выбрано, отправляем событие родителю
                if (!props.store) {
                    this.$emit('v-change', value, name)
                    return
                }

                let changed = true

                // Возвращается более сложный объект
                if (value && value.value != undefined) {
                    // Не сохраняем это изменение
                    if (value.changed === false) changed = false
                    value = value.value
                }

                formData.setFieldProp({
                    name,
                    value,
                    changed,
                    fields: props.fields,
                    property: 'value',
                    fieldsType: props.fieldsType,
                })
            },
            onBack: function (name) {
                msgConfirm('Вы действительно хотите вернуть изначальное значение данного поля?', () => {
                    formData.setFieldBack({
                        name,
                        fields: props.fields,
                    })
                })
            }
        }
    },
}
</script>
