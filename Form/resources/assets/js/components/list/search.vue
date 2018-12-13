<template>
  <form action="" v-on:submit.prevent="search()">
	<div class="card mb-3">
  		<div class="card-body pb-2">
    		<h6 class="card-subtitle mb-2">Поиск</h6>
   			<div v-for="field in fields" class="form-row">
			    <div class="form-group col-4">
			    	<label v-if="field.label">{{ field.label }}</label>
			    	<print-field :field='field' v-on:change="onChange($event, field.name)"></print-field>
			    </div>

		    	<div class="col-auto">
		  			<button class="btn btn-outline-secondary" v-on:submit.prevent="search()">Поиск</button>
				</div>
			</div>
		</div>
	</div>
  </form>
</template>

<script>
  
    import printField from '../fields/field.vue'
  
    export default {
        components: { 'print-field': printField },
        props: [ 'fields' ],
        data() { return { values: {} } },
        watch: {
          	//Обнулям запрос при изменении данных
          	fields: function (fields, oldVal) { this.values = {} },
        },
        methods: {
            onChange: function (value, name) { this.values[name] = value },
            search: function () { this.$emit('change', this.values) }
        }
    }
</script>
