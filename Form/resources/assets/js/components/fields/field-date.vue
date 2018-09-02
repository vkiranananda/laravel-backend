<template>
    <div>
    	<date-picker  :input-class="inputClass" v-model="date" @change="changeDate" :input-name="name" :first-day-of-week="1" :format="format" :lang="lang"></date-picker>
    	<date-picker :input-class="inputClass" v-model="dateTo" :not-before="notBefore" :input-name="nameTo" :first-day-of-week="1"  :format="format" v-if="range" :lang="lang"></date-picker>
	</div>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import fecha from 'fecha'

export default {
	props: {
	  data: {
	  	default: () => [],
	  	// type: Array
	  },
	},
	created() {
		console.log(this.data);
		if(this.data.value != undefined && this.data.value != '') 
			this.date = this.parceDate(this.data.value);
		if(this.data['value-to'] != undefined && this.data['value-to'] != '') 
			this.dateTo = this.parceDate(this.data['value-to']);
	
		if(this.data.attr != undefined) {
			if (this.data.attr.class != undefined)this.inputClass += ' '+this.data.attr.class;
		} 
		if(this.data.format != undefined)this.format = this.data.format;

		if(this.data.name != undefined)this.name = this.data.name;
		if(this.data.nameTo != undefined)this.nameTo = this.data['name-to'];

		if(this.data.range != undefined)this.range = true;

	},
  components: { DatePicker },
  data() {
    return {
      date: '',
      dateTo: '',
      lang: 'ru',
      notBefore: '',

      range: false,
      inputClass: 'mx-input form-control',
      format: 'DD.MM.YYYY',
	  name: 'date',
	  nameTo: 'dateTo'
    }
  },
    methods: {
	    changeDate: function (time) {
	    	if(this.range) {
	    		this.notBefore = time;
	    		console.log(this.dateTo, '11');
	    		if(this.dateTo != '' && this.dateTo < this.date){
	    			this.dateTo = this.date;
	    		}
	    	}

	    },
		parceDate (date) {
		  try {
		    return fecha.parse(date, this.format)
		  } catch (e) {
		    return ''
		  }
		}
  }
}
</script> 