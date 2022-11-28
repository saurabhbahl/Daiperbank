<template>
	<Modal>
		<span slot="header">
			Order #{{ Order.full_id }}
		</span>

		<div slot="body">
			<p class="b red f2 mb tc">Are you sure you want to return this order?</p>

			<p class="tc">When you click "Yes" below, items from this order will be placed back in inventory, and the order status will move to "cancelled."</p>

			<p>
				The note below will be saved to the Order comments for future reference. Agency partners <span class="i">will</span> be able to see this comment.
			</p>

			<p class="b f3 mt">
				Reason for Return: <span class="light-muted i">(Optional)</span>
			</p>

			<textarea class="form-control" v-model="reason" rows="4"></textarea>
		</div>

		<div slot="buttons">
			<button class="btn btn-default" @click="cancel">
				No, keep this order.
			</button>

			<button class="btn btn-danger" @click="process">
				Yes, return this order.
			</button>
		</div>
	</Modal>

</template>

<script>
import { Modal } from '../Modals';

export default {
	props: {
		Order: {
			type: Object,
			required: true,
		},

		initialReason: {
			required: false,
			default: null,
			validator: function(reason) {
				if (['boolean', 'string'].indexOf(typeof reason) >= 0) return true;
				return false;
			}
		},
	},

	components: { Modal },

	data() {
		return {
			reason: this.initialReason,
			flag_share_reason: 0,
		}
	},

	methods: {
		process() {
			this.$root.$emit('order/return', this.Order, this.reason);
			this.close();
		},

		cancel() {
			this.$root.$emit('order/return/cancel');
			this.close();
		},

		close() {
			this.$store.dispatch('Modal/close');
		},
	}
}
</script>