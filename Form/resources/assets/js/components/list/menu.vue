<template functional>
	<div>
		<div v-if="props.menu.length > 1" class="btn-group mb-3">
			<!-- Create -->
			<a class="btn btn-primary" role="button" :href="props.menu[0]['link']">{{ props.menu[0]['label'] }}</a>
		  	
		  	<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    <span class="sr-only"></span>
		  	</button>

			<div v-for="(el, key) in props.menu" v-if="key > 0" class="dropdown-menu">
				<a href="" class="dropdown-item">{{ el.label }}</a>
			</div>
		</div>

	    <a v-else-if="props.menu.length > 0" class="btn btn-primary mb-3" role="button" :href="props.menu[0]['link']">
	      	{{ props.menu[0]['label'] }}
	    </a>
	</div>
</template>