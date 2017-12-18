<template>
	<div class="show-uploads-button">
		<field-block v-for="field in fields" :data="field">
			<field :data="field"></field>
		</field-block>
    </div>
</template>

<script>
    export default {
        props: {
            data: { default: [] },
        },
        computed: {
        	fields () {
        		for (var key in this.data) {
        			if ( ! this.data[key]['attr'] ) this.data[key]['attr'] = {};
        			if ( ! this.data[key]['attr']['id'] ){
        				this.data[key]['attr']['id'] = 'Forms_'+this.data[key]['name'];
        			}
                }
        		return this.data;
        	}
        }
    }
</script>