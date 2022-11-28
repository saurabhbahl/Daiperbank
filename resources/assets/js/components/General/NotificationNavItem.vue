<template>
	<a href="#"
		class="nav-notifications dropdown-toggle"
		data-toggle="dropdown"
		role="button"
		aria-expanded="false"
		@click="markRead">
		<i class="fa fa-bell-o" v-if="unread == 0"></i>
		<i class="fa fa-bell dark-red" v-if="unread > 0"></i>
		<span class="unread-count" v-if="unread > 0">{{ unread }}</span>
	</a>
</template>

<script>
export default {
	props: {
		unreadCount: {
			required: false,
			type: Number,
			default: 0,
		},

		newestTimestamp: {
			required: false,
			type: [ Boolean, Number ],
			default: null,
		},

		oldestTimestamp: {
			required: false,
			type: [ Boolean, Number ],
			default: null,
		},
	},

	data() {
		return {
			unread: this.unreadCount,
		};
	},

	methods: {
		markRead() {
			if (this.unread > 0) {
				setTimeout(() => {
					axios.post('/api/notifications/read', { since: this.oldestTimestamp, until: this.newestTimestamp })
					.then( response => {
						if (response.data.success) {
							this.unread = 0;
						}
					});
				}, 1500);
			}
		},
	},
}
</script>