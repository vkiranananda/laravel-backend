<template>
	<div class="menu-builder row pb-4">
		<div class="col-7">
			<div class="card">
				<div class="card-body">
					<nested-item :list="tree" @change="change" @edit="edit" />
				</div>
			</div>
		</div>
		<div class="col-5">
			<div class="card">
				<div class="card-header">
					<template v-if="create">Добавить новый элемент</template>
					<template v-else>Изменить элемент</template>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-12">
							<input type="text" class="form-control" placeholder="Название ссылки" v-model="element.label">
						</div>
						<div class="col-12 pt-3">
							<input type="text" class="form-control" placeholder="URL" v-model="element.url">
							<small id="passwordHelpBlock" class="form-text text-muted">
								Для адресов сайта используйте относительные пути, начинающиеся с /
							</small>
						</div>
						<div class="col-12 pt-3">
							<button class="btn btn-outline-primary mr-2" @click="add">
								<template v-if="create">Добавить</template>
								<template v-else="create">Изменить</template>
							</button>
							<button class="btn btn-outline-dark" @click="clear">Отмена</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>


<script>
import nestedItem from "./nested-item"
import cloneDeep from "lodash.clonedeep"
export default {
	methods: {		
		clear() {
			this.element.url = ''
			this.element.label = ''
			this.create = true
		},
		add() {
			if (!this.create) {
				this.changedeEl.url = this.element.url
				this.changedeEl.label = this.element.label
			} else {
				this.tree.push({
					url: this.element.url,
					label: this.element.label,
					elements: []
				})
			}
			this.clear()
			this.$emit('change', this.tree)
		},
		change(val) { this.$emit('change', this.tree) },
		edit(el) {
			this.create = false
			this.element.url = el.url
			this.element.label = el.label
			this.changedeEl = el
		}
	},
	props: [ 'field' ],
	data() {
		return {
			create: true,
			changedeEl: false,
			element: {
				url: '',
				label: '',
			},
			// клон value
			tree: []
		}
	},
	watch: {
		// Просто клонируем валуе в элемент tree
	    'field.value': {
			handler: function (val, oldVal) {
				this.tree = (Array.isArray(val)) ? cloneDeep(val) : []
			},
	      	immediate: true
	    },

	},
	components: { nestedItem },
};
</script> 


<style>
	.menu-builder {
		.item-container {
			max-width: 20rem;
			margin: 0;
		}
	}
</style>