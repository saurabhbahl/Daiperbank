<template>
	<Modal>
		<span slot="header">
			Order #{{ Order.full_id }}
		</span>

		<div slot="body">
			<p class="b red f2 mb tc">Are you sure you want to discard this order?</p>

			<p class="tc">
				When you click "Yes" below, this order will be permanently discarded.
			</p>

			<p class="mv3 b tc dark-red">
				This operation can not be undone.
			</p>
		</div>

		<div slot="buttons">
			<button class="btn btn-default" @click="cancel">
				No, keep this order.
			</button>

			<button class="btn btn-danger" @click="reject">
				Yes, discard this order.
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
			this.$root.$emit('order/discard', this.Order);
			this.close();
		},

		cancel() {
			this.close();
		},

		close() {
			this.$root.$emit('order/discard/cancel');
			this.$store.dispatch('Modal/close');
		},
	}
}
</script>