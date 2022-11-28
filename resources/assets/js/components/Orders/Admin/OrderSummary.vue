<template>
	<BaseOrderSummary
		:order="order"
		:orderSummary="orderSummary"
		:is-admin="true"
	></BaseOrderSummary>
</template>
<script>
import BaseOrderSummary from '../BaseOrderItemSummary.vue';
export default {
	components: { BaseOrderSummary },
	props: {
		order: {
			required: true,
			type: Object,
		},

		orderSummary: {
			required: false,
			type: [ Array, Object ],
		}
	},
};
</script>