<script>

import octicons from 'octicons'

export default {
	functional: true,
	
	props: {
		width: {
			type: String,
		},
		height: {
			type: String,
		},
		className: {
			type: String,
		},
		name: {
			type: String,
			required: true
		},
	},
	render(createElement, { props, data, listeners }) {
		// Если нет иконки
		if (!octicons[props.name]) {
			console.warn('Icon ' + props.name + ' not found.')
			return
		}

		let params = {
			class: data.staticClass
		}

		if (props.width) params.width = props.width
		if (props.height) params.height = props.height	

		return createElement('span', {
			class: 'octicon-wrapper',
			domProps: {
				innerHTML: octicons[props.name].toSVG(params),
			},
			on: listeners
		})
	}
}
</script>

<style lang='scss'>
	.octicon {
		margin-bottom: 0.16rem;
		fill: inherit;
	}
</style>

