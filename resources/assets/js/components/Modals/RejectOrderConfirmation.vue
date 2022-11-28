<template>
	<Modal>
		<span slot="header">
			Order #{{ Order.full_id }}
		</span>

		<div slot="body">
			<p class="b red f2 mb tc">Are you sure you want to reject this order?</p>

			<p>
				When you click "Yes" below, this order will be rejected and the
				ordering Agent will be notified via e-mail.
			</p>

			<p class="b f3 mt">
				Reason for Rejection: <span class="light-muted i">(Optional)</span>
			</p>

			<textarea class="form-control" v-model="reason" rows="4"></textarea>
		</div>

		<div slot="buttons">
			<button class="btn btn-default" @click="cancel">
				No, keep this order.
			</button>

			<button class="btn btn-danger" @click="reject">
				Yes, reject this order.
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
		reject() {
			this.$root.$emit('order/reject', this.Order, this.reason);
			this.close();
		},

		cancel() {
			this.close();
		},

		close() {
			this.$root.$emit('order/reject/cancel');
			this.$store.dispatch('Modal/close');
		},
	}
}
</script>