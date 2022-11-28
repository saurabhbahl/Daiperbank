<style lang="less" scoped>
ul, li { list-style-type: none; margin: 0; padding: 0; }
</style>
<template>
	<div class="dom fg-no fs-no bg-white bl bt"
		:class="{
			'bg-black-10': day.isPadding(),
			'clickable': ! day.isPadding(),
			'hov:bg-washed-blue': ! day.isPadding(),
		}"
		@click="activateDate">

		<div v-if=" ! day.isPadding()"
			class="clearfix">
			<div class="bg-white fr ph3 pv2 f4 bl bb b--black-10">
				{{ dateContext.format('D') }}
			</div>
		</div>

		<div class="pa f4">
			<div v-if="items.length && items.length <= 3">
				<ul>
					<li v-for="item in items">
						{{ formatDate(item.pickup_date) }}
						<span v-if="totalOrders">
							<br>
							{{ totalOrders }} order{{ totalOrders != 1? 's' : '' }}
						</span>
					</li>
				</ul>
			</div>

			<div v-else-if="items.length">
				{{ items.length + " times scheduled." }}
			</div>

			<div v-else>&nbsp;</div>
		</div>
	</div>
</template>

<script>
import moment from 'moment';

export default {
	props: ['day'],
	data() {
		return {
			dateContext: this.day.dateContext,
		};
	},

	computed: {
		items() {
			return this.day.items || null;
		},

		totalOrders() {
			if (this.items.length) {
				return this.items[0].orders_approved + this.items[0].orders_pending;
			}

			return 0;
		}
	},

	methods: {
		formatDate(dateString) {
			return moment(dateString, 'YYYY-MM-DD HH:mm:ss').format('h:mmA');
		},

		activateDate() {
			if (this.day.isPadding()) return null;

			this.$emit('click', this.day);

			this.$store.commit({
				type: 'AdminPickupDate/viewDate',
				date: this.day,
			});
		}
	}
}
</script>
