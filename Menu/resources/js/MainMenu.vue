<template>
	<div class="left-menu bg-dark">
		<div class="list-group">
			<ul>
				<main-menu-item v-for="(item, key) in menu" :key="key" :item="item"> 
					<template v-if="item.type == 'method'">
						<ul v-if="item.name != undefined">
							<main-menu-item v-for="(subItem,key) in updatedItems[item.name]" :key="key" :item="subItem">
							</main-menu-item>
						</ul>
						<ul v-else>
							<main-menu-item v-for="(subItem,key) in item[items]" :key="key" :item="subItem">
							</main-menu-item>
						</ul>					
					</template>
				</main-menu-item>
			</ul>
		</div>
	</div>
</template>

<script>
	import MainMenuItem from './MainMenuItem.vue'

    export default {
    	components: {MainMenuItem},
        created () { 
        	for(let item of this.menu) {
        		if (item.type == 'method' && item.name != undefined){
        			this.$set(this.updatedItems, item.name, item.items)
        		}
        		
        	}
        	this.$bus.$on('MainMenuUpdate', (data) => {
        		if (data.name != undefined && data.items != undefined){
        			this.$set(this.updatedItems, data.name, data.items)
        		}
        	}) 
        },
        beforeDestroy() { this.$bus.$off('MainMenuUpdate') },
        props: [ 'menu' ],
        data() { return { updatedItems: {} } },
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
				-webkit-font-smoothing: antialiased;
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