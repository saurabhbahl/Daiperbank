<template>
	<div v-show="active"
		class="tab-pane"
		:class='{ "active": active }'>

		<slot name="default">
			Your tab content goes here.
		</slot>
	</div>
</template>

<script>
export default {
	props: {
		active: {
			default: false,
		}
	}
}
</script>