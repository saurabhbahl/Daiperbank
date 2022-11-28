<template>
	<Modal>
		<span slot="header">
			Order #{{ Order.full_id }}
		</span>

		<div slot="body">
			<p class="b red f2 mb tc">Are you sure you want to cancel this order?</p>

			<p v-if=" ! isAdmin" class="tc">When you click "Yes" below, this order will be permanently cancelled.</p>

			<p v-if="isAdmin">
				When you click "Yes" below, this order will be cancelled and the
				ordering Agent will be notified via e-mail.
			</p>

			<p class="b f3 mt" v-if="isAdmin">
				Reason for Cancellation: <span class="light-muted i">(Optional)</span>
			</p>

			<textarea v-if="isAdmin" class="form-control" v-model="reason" rows="4"></textarea>
		</div>

		<div slot="buttons">
			<button class="btn btn-default" @click="cancel">
				No, keep this order.
			</button>

			<button class="btn btn-danger" @click="reject">
				Yes, cancel this order.
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

		isAdmin: {
			required: false,
			default: true,
			type: Boolean,
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
		reject() {
			this.$root.$emit('order/cancel', this.Order, this.reason);
			this.close();
		},

		cancel() {
			this.close();
		},

		close() {
			this.$root.$emit('order/cancel/cancel');
			this.$store.dispatch('Modal/close');
		},
	}
}
</script>