<template>
  <form action="" v-on:submit.prevent="search()">
	<div class="card mb-3">
  		<div class="card-body pb-2" v-if="fieldsNew.fields.length > 0">
    		<h6 class="card-subtitle mb-2">Поиск</h6>
        <div class="row">
  		    <div v-for="field in fieldsNew.fields" class="form-group col-4">
  		    	<label v-if="field.label">{{ field.label }}</label>
  		    	<print-field :field='field' v-on:change="onChange($event, field)"></print-field>
  		    </div>

          <div class="col-auto">
              <button class="btn btn-outline-secondary" v-on:submit.prevent="search()">Поиск</button>
          </div>
        </div>
		</div>
	</div>
  <div class="form-inline mb-3 mx-2">
    <div v-for="field in fieldsNew.filters" class="form-group text-nowrap">
      Фильтры: &nbsp; 
      <print-field :field='field' v-on:change="onChange($event, field)" class=""></print-field>
    </div>
  </div>
  </form>
</template>

<script>
  
    import printField from '../fields/field.vue'
  
    export default {
        components: { 'print-field': printField },
        created() {
          console.log(this.fields)
        },
        props: [ 'fields' ],
        data() { return { values: {} } },
        watch: {
          	//Обнулям запрос при изменении данных
          	fields: function (fields, oldVal) { this.values = {} },
        },
        computed: {
          fieldsNew: function () {
            var res = {'fields': [], 'filters': [] }
            for(let field of this.fields ) {
              if (field['search-type'] == 'filter') res.filters.push(field)
              else res.fields.push(field)
            }
            return res
          }
        },
        methods: {
            onChange: function (value, field) { 
              this.values[field.name] = value 
              if (field['search-type'] == 'filter') this.search()
            },
            search: function () { this.$emit('change', this.values) }
        }
    }
</script>
