<template>
	<div class="left-menu bg-dark">
		<div class="list-group">
			<ul>
				<li v-for="item in newMenu">
					<a class="text-light" :class="item.classes" :href="item.url"> &nbsp;{{ item.label }}</a>
		    	</li>
			</ul>
		</div>
	</div>
</template>

<script>
    export default {
        props: [ 'menu' ],
        computed: {
        	newMenu: function () {
        		let menu = [];

        		for (let item of this.menu) {
        			let classes = '';
        			
        			if (item.icon != undefined) classes += ' icons-' + item.icon;
        			if (item['space-bottom'] != undefined) classes += ' mb-' + item['space-bottom'];
        			if (item['space-top'] != undefined) classes += ' mt-' + item['space-top'];
        			
        			menu.push({ url: item.url, label: item.label, classes });
        		}
        		return menu;
        	}
        },
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
	}
</style>