<template>
	<div v-if="loading" class="base-loading d-flex justify-content-center ">
		<div class="text-center align-self-center">
			<div transition name="fade">
				<img v-if="loading" src="/backend/images/loading5.gif">
			</div>
		</div>
	</div>
</template>
<script>
export default {
    props: ['loading']
}
</script>
<style lang='scss'>
	.base-loading {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 100000;

		.fade-enter-active, .fade-leave-active {
		  	transition: opacity .5s;
		}
		.fade-enter, .fade-leave-to {
		  	opacity: 0;
		}
	}
</style>
