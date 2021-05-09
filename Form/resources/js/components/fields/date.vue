<template>
    <div>
        <date-picker :type="type" :input-class="inputClass" v-model="date" :first-day-of-week="1" :format="format"
                     lang="ru" :minute-step="field['minute-step'] ? field['minute-step'] : 1"
                     v-bind="field.attr" :disabled="field.readonly"></date-picker>
    </div>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css';
import fecha from 'fecha'


export default {
    props: ['field', 'error'],

    components: {DatePicker},
    created() {
        if (this.field.value == 'now') this.date = new Date();
    },
    computed: {
        date: {
            get: function () {
                // Обрабатываем timestamp, он идет объектом с тайм зоной.
                return this.getDate(this.field.value)
            },
            set: function (newDate) {
                let date = (newDate != null) ? fecha.format(newDate, this.inputFormat) : ''
                this.$emit('change', date);
            }
        },

        format: function () {
            let format = this.field.format != undefined ? this.field.format : 'DD.MM.YYYY'
            if (this.field.time != undefined) format += ' HH:mm'
            return format
        },
        type: function () {
            return this.field.time ? 'datetime' : 'date'
        },
        // Генерим классы
        inputClass: function () {
            let objClass = 'mx-input form-control';
            let attr = this.field.attr;

            if (this.error) objClass += ' is-invalid';

            if (attr != undefined && attr.class != undefined) objClass += ' ' + attr.class;

            return objClass;
        },
        //Возвращаем дату с нужным форматированем
        inputFormat: function () {
            if (this.field['input-format'] != undefined) return this.field['input-format']
            if (this.field.time) return 'YYYY-MM-DD HH:mm:ss'
            return 'YYYY-MM-DD'
        }
    },
    methods: {
        getDate: function (date) {
            if (date == undefined || date == null || date == '') return null;
            if (date == 'now') return new Date();
            else return fecha.parse(date, this.inputFormat);
        }
    }
}
</script>


<style lang='scss'>
.mx-datepicker {
    .mx-input {
        font-size: 1rem;
    }
}
</style>

