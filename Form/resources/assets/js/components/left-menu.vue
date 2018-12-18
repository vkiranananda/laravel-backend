<template>
	<div class="left-menu bg-dark">
		<div class="list-group">
			<ul>
				<li v-for="item in menu" :class="[item['space-bottom'] ? 'pb-'+item['space-bottom'] : '', item['space-top'] ? 'pt-'+item['space-top'] : '']">
					<ul v-if="item.category === true && categories.length > 0">
						<li v-for="cat in categories">
							<a class="text-light" :class="cat.icon != undefined ? 'octicon octicon-'+cat.icon : ''" :href="cat.url">
								{{ cat.label }}
							</a>
						</li>
					</ul>

					<a v-if="item.url != undefined" class="text-light"  :class="item.icon != undefined ? 'octicon octicon-'+item.icon : ''" :href="item.url">
						{{ item.label }}
					</a>
					<hr v-else-if="item.separator === true">
		    	</li>
			</ul>
		</div>
	</div>
</template>

<script>
    export default {
        created () { 
        	this.$bus.$on('MenuCategoryRootReload', (data) => {
        		console.log('hoook', data, this.categories);
        		this.categories = data;
        	}) 
        },
        beforeDestroy() { this.$bus.$off('MenuCategoryRootReload') },
        props: [ 'menu', 'cats' ],
        data() { return { categories: this.cats } },
        methods: {
        	pageChange: function(value) { this.$emit('change', { currentPage: value }) },
        }
    }
</script>

<style lang='scss'>
	.left-menu {
		position: fixed;
		height: 100%;
		/*z-index: 1;*/
		padding-top: 15px;
		width: 200px;
		ul {
			list-style-type: none;
			padding: 0;
		}
		li {
			a {
				display: block;
				padding: 4px 10px;
			}
			a:hover {
				background-color: black; 
				text-decoration: none;
			}
		}
		hr {
			margin: 10px 10px;
		}
	}
</style>