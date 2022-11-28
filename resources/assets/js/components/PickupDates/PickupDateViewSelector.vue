<template>
	<div>
		<a href="#" @click="changeView(VIEW_CALENDAR)">
			<i class="fa fa-calendar"></i>
			Calendar View
		</a>

		<a href="#" @click="changeView(VIEW_LIST)">
			<i class="fa fa-list"></i>
			List View
		</a>
	</div>
</template>

<script>
const VIEW_CALENDAR = 'calendar';
const VIEW_LIST = 'list';

export default {
	props: ['defaultView'],

	data() {
		return {
			VIEW_CALENDAR,
			VIEW_LIST
		};
	},

	computed: {
		currentView() {
			return this.$store.getters['AdminPickupDate/currentView'];
		}
	},

	methods: {
		changeView(type) {
			if (type != this.currentView) {
				this.$store.commit({
					type: 'AdminPickupDate/changeView',
					view: type,
				});
			}
		}
	},
}
</script>