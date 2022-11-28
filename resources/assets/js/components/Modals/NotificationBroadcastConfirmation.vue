<template>
	<Modal>
		<span slot="header">
			Are you sure?
		</span>

		<div slot="body">
			<p class="b mb tc">Are you sure you want to send this notification to all Agency partners?</p>

			<p class="b mb">Your message:</p>

			<div class="ml4 pl4 pv3 bl b--black-025 bw3">
				<nl2br
					tag="p"
					:text="message"
				></nl2br>
			</div>
		</div>

		<div slot="buttons">
			<button class="btn btn-default" @click="cancel">
				No, don't send it.
			</button>

			<button class="btn btn-success" @click="send">
				Yes, send it now.
			</button>
		</div>
	</Modal>

</template>

<script>
import { Modal } from '../Modals';
import nl2br from 'vue-nl2br';

export default {
	props: {
		message: {
			required: true,
			type: String,
		}
	},

	components: { Modal, nl2br },

	methods: {
		send() {
			this.$root.$emit('notification/post');
			this.close();
		},

		cancel() {
			this.$root.$emit('notification/post/cancel');
			this.close();
		},

		close() {
			this.$store.dispatch('Modal/close');
		},
	}
}
</script>