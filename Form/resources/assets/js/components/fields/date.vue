<template>
    <div>
    	<date-picker  :input-class="inputClass" v-model="date" :first-day-of-week="1" :format="format" lang="ru"></date-picker>
	</div>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import fecha from 'fecha'

export default {
	props: ['field', 'error' ],

	components: { DatePicker },
	created() {
		if ( this.field.value == 'now') this.date = new Date();
	},
  	computed: {
  		date: {
  			get: function () {
  				return this.getDate(this.field.value)
    		},
    		set: function (newDate) {
				let date = (newDate != null) ? fecha.format(newDate, this.inputFormat) : '' 
				this.$emit('change', date);
			}
  		},

  		format: function () { return this.field.format != undefined ? this.field.format : 'DD.MM.YYYY' },
   		// Генерим классы
   		inputClass: function () { 
   			let objClass = 'mx-input form-control';
   			let attr = this.field.attr;

   			if (this.error) objClass += ' is-invalid';

   			if (attr != undefined && attr.class != undefined) objClass += ' ' + attr.class;
   		
   			return objClass;
   		},
   		//Возвращаем дату с нужным форматированем
  		inputFormat: function () {return this.field['input-format'] != undefined ? this.field['input-format'] : 'YYYY-MM-DD'},
	},
	methods: {
		getDate: function(date) {
			if ( date == undefined || date == null || date == '' ) return null;
			if ( date == 'now') return new Date();
        	else return fecha.parse(date, this.inputFormat);
		}
	}
}
</script> 