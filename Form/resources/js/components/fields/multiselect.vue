<template>
<!--    -->
	<vue-multiselect :class='error ? "is-invalid" : ""' v-model="value" :options="field.options"  :multiple="field.multiple"  label="label" track-by="value" :placeholder="placeholder" :showLabels="false"></vue-multiselect>
</template>

<script>
	import VueMultiselect from 'vue-multiselect'
    export default {

        props: [ 'field', 'error', 'fields'],
        components: { VueMultiselect },
	  	computed: {
	  		optionsSearch: function () {
	  			let res = {}
	  			for(let el of this.field.options) {
	  				res[el.value] = el
	  			}
	  			return res
	  		},
	  		placeholder: function () {
	  			return (this.field.placeholder == undefined) ? 'Выберете опцию' : this.field.placeholder
	  		},
	  		value: {
	  			get: function () {
	  				let res = null
	  				if (this.field.multiple) {
	  					res = []
	  					if (!Array.isArray(this.field.value)) return null
	  					for (let el of this.field.value) if (this.optionsSearch[el] != undefined) {
	  						res.push(this.optionsSearch[el])
	  					}
	  				} else {
	  					if (this.optionsSearch[this.field.value] != undefined) {
	  						res = this.optionsSearch[this.field.value]
	  					}
	  				}
	  				return res
	  			},
	    		set: function (newValues) {
					let res = ''
					if (newValues != null) {
						if (this.field.multiple) {
							res = []
							for (let el of newValues) res.push(el.value)
						} else res = newValues.value
					}
					this.$emit('v-change', res )
				 }
	  		},
	  	},
		methods: {
			customLabel (option) { return option.label }
		}
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style lang='scss'>
  .multiselect {
  	.multiselect__option--selected.multiselect__option--highlight,.multiselect__option--highlight {
  		background: #41b883;
  	}
    box-sizing: border-box;
    &.form-control {
      padding: 0px;
      height: auto;
    }
    .multiselect__single {
      padding-top: 5px;
    }
    .multiselect__tags {
      min-height: 38px;
      padding: 6px 40px 4px 8px;
      border: 1px solid #ced4da;
      border-radius: 0.25rem;
    }
    .multiselect__tag {
      margin-top: 3px;
    }
    .multiselect__placeholder {
      margin-bottom: auto;
      padding-top: 4px;
    }
    .multiselect__input {
      margin-top: 5px;
    }
  }
</style>
