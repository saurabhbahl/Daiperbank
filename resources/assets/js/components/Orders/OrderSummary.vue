<template>
	<BaseOrderSummary
		:order="order"
		:is-admin="false"
	></BaseOrderSummary>
</template>
<script>
import BaseOrderSummary from './BaseOrderItemSummary.vue';
export default {
	components: { BaseOrderSummary },
	props: {
		order: {
			required: true,
			type: Object,
		},
	},
}
</script>