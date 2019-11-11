<template>
	<div class="list">
		
		<div v-if="items.data.length > 0">
		    <div class="row mb-3">
		    	<div class="col-auto mr-auto"></div>
	            <div class="col-auto">
	                <list-paginate v-if="items.lastPage > 1" :data="items" @change="pageChange"></list-paginate>
	            </div>
	        </div>
			<table  class="table table-hover" id="table-list">
		    	<thead>
		        	<tr class="table-light">
						<th v-for="(field, key) in fields" :key="field.name" class="align-middle" scope="col" v-bind="field.attr">
							<div v-if="field.sortable != undefined" class="sortable" :class="field.sortable === true ? 'none' : field.sortable" @click="sortable(key)">
								{{ field.label }}
								<div class="octicon icons-chevron-down down"></div>
								<div class="octicon icons-chevron-up up"></div>
							</div>
							<span v-else>{{ field.label }}</span>
						</th>
		        		<th scope="col" class="menu-td" v-if="itemMenu"></th>
		         	</tr>
		      	</thead>
		     	<tbody>
		       		<tr v-for="item in items.data">
		          		<td v-for="field in fields" v-bind="field.attr">
		              		<span v-if="field.icon" :class="'icons-'+field.icon">&nbsp;</span>
		              		<a v-if="field.link" :href="item._links[field.link]">{{ item[field.name] }}</a>
		              		<template v-else>
		              			<span v-if="field.html === true" v-html="item[field.name]"></span>
		              			<template v-else>{{ item[field.name] }}</template>
		              		</template>
		          		</td>

		          		<td class="menu-td" v-if="itemMenu">
		            		<div class="dropdown">
		              			<button class="btn btn-secondary button-grabber" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
		              			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
		                			<template v-for="elMenu in itemMenu">
	          		                	<a v-if="item._links[elMenu.link] != undefined"
		                					class="dropdown-item" 
		                					:class="elMenu.icon ? 'octicon octicon-'+elMenu.icon : ''"
		                					v-on:click.stop.prevent="itemActionClick(item._links[elMenu.link], elMenu)">
		                						{{ elMenu.label }}
		                				</a>
		                			</template>
		              			</div>
		            		</div>
		          		</td>
		        	</tr>
		     	</tbody>
			</table>

		    <div class="row">
		    	<div class="col-auto mr-auto"></div>
	            <div class="col-auto">
	                <list-paginate v-if="items.lastPage > 1" :data="items" @change="pageChange"></list-paginate>
	            </div>
	        </div>
		</div>
        <p v-else class="pt-1">Ничего не найдено</p>
	</div>
</template>

<script>
    import paginate from './paginate.vue'
    export default {
        props: [ 'items', 'fields', 'itemMenu' ],
        components: {
            'list-paginate': paginate
        },
        methods: {
        	pageChange: function(value) { this.$emit('change', { currentPage: value }) },
        	sortable: function(key) { 
                let field = this.fields[key], orderType;

                if ( field.sortable === true ) orderType = 'asc';
                else if ( field.sortable === 'asc' ) orderType = 'desc';
                else if ( field.sortable === 'desc' ) orderType = true;
        		
        		this.$emit('change', { sortable: key, orderType }) 
        	},
        	itemActionClick: function(url, el) {
        		if (el.confirm) { if ( confirm(el.confirm) ) this.itemAction(url, el) } 
        		else this.itemAction(url, el)

        	},
        	itemAction: function(url, el) {
        		if (el.link == 'destroy') this.deleteItem(url)
        		else {
        			if (el.target) window.open(url, el.target)
        			else document.location.href = url
        		}
        	},
        	//Удаляем элемент
        	deleteItem: function(url) { 

				this.$emit('change', { destroy: 'begin'});
				
				axios.delete(url)
            		.then( (response) => { 
            			this.$emit('change', { destroy: 'finished' });
            	        //Вызываем хуки
	                    if (response.data.hook != undefined && response.data.hook.name) {
	                        this.$bus.$emit(response.data.hook.name, response.data.hook.data)
	                    }
            		})
            		.catch( (error) => { 
            			if (error.response.status == 403) {
            				alert(error.response.data.message);
            			}
            			console.log(error.response);
            			this.$emit('change', { destroy: 'error' });
            		}); 
        	}
        }
    }
</script>
	<!-- <the-list-sortable></the-list-sortable > -->

<style lang='scss'>
	.list {
		.sortable {
			cursor: pointer;
			position: relative;
			.octicon {
				display: none;
				position: absolute;
				margin-left: 5px;
				top: 1px;
			}
			&.none {
				&:hover {
					.down { display: inline; }
				}		
			}
			&.asc {
				.down { display: inline; }
				&:hover {
					.up { display: inline; }
					.down { display: none; }
				}		
			}
			&.desc {
				.up { display: inline; }
				&:hover {
					.down { display: inline; }
					.up { display: none; }
				}		
			}
		}
		.button-grabber:before { 
			content:"\f103"; 
			font-family: Octicons;
		    position: relative;
		    top: -3px;
		    left: -2px;
		}
		.button-grabber{
			width: 30px;
			height: 30px;
		}
		.file-directory {
		    font-size: 22px;
		    margin-right: 10px;
		    vertical-align: middle;
		}
		tr{
			.dropdown{
				display: none;
			}
			&:hover{
				.dropdown {
					display: block;
				}
			}
		}
		.menu-td {
			vertical-align: middle;
			padding-top: 0px;
			padding-bottom: 0px;
			width: 60px;
		}
	}
</style>