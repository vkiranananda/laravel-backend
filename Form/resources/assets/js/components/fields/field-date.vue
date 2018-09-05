<template>
    <div>
    	<date-picker  :input-class="inputAttr.class" v-model="date" :input-name="'_'+name" :first-day-of-week="1" :format="format" :lang="lang"></date-picker>
    	<date-picker :input-class="inputAttr.class" v-model="dateTo" :not-before="rangeNotBefore" :input-name="'_'+nameTo" :first-day-of-week="1"  :format="format" v-if="range" :lang="lang"></date-picker>
    	<input type="hidden" :name="name" :value="inputDate">
    	<input type="hidden" :name="nameTo" :value="inputDateTo" v-if="range">
	</div>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import fecha from 'fecha'

export default {
	props: {
	  prefs: {
	  	default: () => [],
	  },
	},
	created() {
		this.date = this.dateOrig;
		this.dateTo = this.dateToOrig;		
	},
	components: { DatePicker },
		data() {
		return {
			date: null,
			dateTo: null,
			lang: 'ru',
		}
	},
  	computed: {
  		dateOrig: function () { return this.getDate(this.prefs.value) },
  		dateToOrig: function () { return this.getDate(this.prefs['value-to']) },
  		name: function () {	return this.prefs.name != undefined ? this.prefs.name : 'date' },
  		nameTo: function () { return this.prefs['name-to'] != undefined ? this.prefs['name-to'] : 'dateTo' },
  		format: function () { return this.prefs.format != undefined ? this.prefs.format : 'DD.MM.YYYY' },

  		range: function () { return this.prefs.range != undefined ? true : false },
  		rangeNotBefore: function () { return this.range && this.date != '' ? this.date : '' },
   		inputAttr: function () { 
   			var objClass = 'mx-input form-control';
   			var resObj = this.prefs.attr != undefined ? Object.assign({}, this.prefs.attr) : {};

   			if(resObj.class == undefined) resObj.class = objClass;
   		
   			return resObj;
   		},
   		//Возвращаем дату с нужным форматированем
  		inputFormat: function () {return this.prefs['input-format'] != undefined ? this.prefs['input-format'] : 'YYYY-MM-DD'},
   		inputDate: function() { console.log(this.date); return this.date != null ? fecha.format(this.date, this.inputFormat) : '' },
   		inputDateTo: function() { return this.dateTo != null ? fecha.format(this.dateTo, this.inputFormat) : '' }
	},
	watch: {
		dateOrig() {console.log(this.dateOrig); if(this.date != this.dateOrig) this.date = this.dateOrig },
		dateToOrig() { if(this.dateTo != this.dateToOrig) this.dateTo = this.dateToOrig },
		date: function() { 
	    	if(this.range) {
	    		if(this.dateTo != '' && this.dateTo < this.date && this.date != ''){
	    			this.dateTo = this.date;
	    		}
	    	}			
		},
	},
    methods: {
		getDate (date) {
            if ( date == undefined || date == null ) return null;
            if ( date == 'now' ) return new Date();
            else return fecha.parse(date, this.inputFormat);
		}
  }
}
</script> 