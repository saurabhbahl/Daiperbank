<template>
	<form method="post" ref="broadcast-form">
		<input type="hidden" name="_token" :value="csrfToken">
		<textarea
			class="form-control"
			rows="5"
			name="message"
			v-model="message"
			@keyup="validate"
		></textarea>

		<button
			class="mt3 btn btn-block btn-primary"
			:class="{ 'disabled': !valid }"
			@click="submit($event)">
			Broadcast Message
		</button>
	</form>
</template>

<script>
import NotificationBroadcastConfirmationModal from '../Modals/NotificationBroadcastConfirmation.vue';

export default {
	props: {
		initialMessage: {
			required: false,
			type: [ String, Boolean ],
			default: null,
		},
	},

	data() {
		return {
			csrfToken: window.csrf,
			valid: false,
			processing: false,
			message: this.initialMessage,
		};
	},

	methods: {
		validate() {
			this.valid = this.message.trim().length > 5 && this.message.trim().split(' ').length > 2;
		},

		submit(evt) {
			evt.preventDefault();

			if ( this.valid) {
				this.confirmBroadcast();
			}

			return false;
		},

		confirmBroadcast() {
			this.$store.commit({
				type: 'Modal/show',
				modalComponent: NotificationBroadcastConfirmationModal,
				props: {
					message: this.message,
				}
			});

			this.processing = true;
			this.$root.$once('notification/post', this.postNotification);
			this.$root.$once('notification/post/cancel', () => {
				this.processing = false;
				this.$root.$off('notification/post', this.postNotification);
			});
		},

		postNotification() {
			this.$refs['broadcast-form'].submit();
		}
	},
}
</script>