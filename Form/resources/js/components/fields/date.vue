<!-- 
 v-model пришлось убрать так как работала не корректно.
  -->
<template>
    <div class="datepicker">
        <div class="readonly-field" v-if="field.readonly">
            {{ field.value }}
        </div>
        <date-picker
            v-else
            :type="type"
            :value="pickerDate"
            :input-class="inputClass"
            @update:value="onDatePickerValueUpdate"
            :first-day-of-week="1"
            :format="format"
            :placeholder="field.placeholder"
            lang="ru"
            :minute-step="field['minute-step'] ? field['minute-step'] : 1"
            v-bind="field.attr"
            :disabled="field.readonly"
        ></date-picker>
    </div>
</template>

<script>
import DatePicker from 'vue-datepicker-next';
import 'vue-datepicker-next/index.css';
import fecha from 'fecha';

export default {
    props: ['field', 'error'],

    data() {
        return {
            pickerDate: null,
        };
    },
    components: { DatePicker },
    watch: {
        'field.value': {
            immediate: true,
            handler(val) {
                if (val == 'now') {
                    this.pickerDate = new Date();
                } else {
                    this.pickerDate = this.getDate(val);
                }
            },
        },
    },
    computed: {
        format: function () {
            let format =
                this.field.format != undefined
                    ? this.field.format
                    : 'DD.MM.YYYY';
            if (this.field.time != undefined) format += ' HH:mm';
            return format;
        },
        type: function () {
            return this.field.time ? 'datetime' : 'date';
        },
        // Генерим классы
        inputClass: function () {
            let objClass = 'mx-input form-control';
            let attr = this.field.attr;

            if (this.error) objClass += ' is-invalid';

            if (attr != undefined && attr.class != undefined)
                objClass += ' ' + attr.class;

            return objClass;
        },
        //Возвращаем дату с нужным форматированем
        inputFormat: function () {
            if (this.field['input-format'] != undefined)
                return this.field['input-format'];
            if (this.field.time) return 'YYYY-MM-DD HH:mm:ss';
            return 'YYYY-MM-DD';
        },
    },
    methods: {
        onDatePickerValueUpdate: function (value) {
            this.pickerDate = value;
            let date = (value != null) ? fecha.format(value, this.inputFormat) : '';
            this.$emit('v-change', date);
        },
        getDate: function (date) {
            if (date == undefined || date == null || date == '') return null;
            if (date == 'now') return new Date();
            else return fecha.parse(date, this.inputFormat);
        },
    },
};
</script>

<style lang="scss">
.datepicker {
    .readonly-field {
        //display: inline-block;
    }

    .mx-datepicker {
        .mx-input {
            font-size: 1rem;
        }
    }
}
</style>
