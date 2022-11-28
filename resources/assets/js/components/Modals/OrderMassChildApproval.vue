<template>
	<Modal>
		<span slot="header">
			Are you sure?
		</span>

		<div slot="body">
			<p class="b mb tc">By confirming this order with children still pending review, all the pending children will be automatically approved.</p>
		</div>

		<div slot="buttons">
			<button class="btn btn-default" @click="cancel">
				No, don't approve the order
			</button>

			<button class="btn btn-success" @click="send">
				Yes, I'm sure
			</button>
		</div>
	</Modal>

</template>

<script>
import { Modal } from '../Modals';
import nl2br from 'vue-nl2br';

export default {
	props: {
		Order: {
			required: false,
			type: Object,
			default: function() { return {}; },
		},

		PendingChildren: {
			required: false,
			type: Array,
			default: function() { return []; },
		},
	},

	components: { Modal, nl2br },

	methods: {
		send() {
			this.$root.$emit('order/approve');
			this.close();
		},

		cancel() {
			this.$root.$emit('order/approve/cancel');
			this.close();
		},

		close() {
			this.$store.dispatch('Modal/close');
		},
	}
}
</script>