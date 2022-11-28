<template>
	<div class="flex-auto">
		<div class="clearfix">
			<h2 class="f2 fl ma0">
				{{ dateContext.format('MMMM YYYY') }}
			</h2>

			<div class="fr">
				<a :href="'?month=' + prevMonth">
					<i class="fa fa-chevron-left"></i>
					Previous Month
				</a>

				&middot;

				<a :href="'?month=' + nextMonth">
					Next Month
					<i class="fa fa-chevron-right"></i>
				</a>
			</div>
		</div>

		<div v-for="PickupDate in allPickupDates" :key="PickupDate.id"
			class="bg-white pa bb b--black-10">
			{{ moment(PickupDate.pickup_date).format('MMMM DD YYYY') }}
		</div>
	</div>

</template>

<script>
import moment from 'moment';

export default {
	props: ['pickupDates', 'month', 'year'],
	data() {
		return {
			dateContext: moment([
				parseInt(this.year || moment().year()),
				parseInt(this.month|| moment().month()),
				1
			]),
			today: moment(),
		}
	},

	computed: {
		allPickupDates() {
			return [].concat(...Object.values(this.pickupDates).map( (dates) => {
				return dates;
			} ));
		},

		currentYear() {
			return this.dateContext.year();
		},

		currentMonth() {
			return this.dateContext.month();
		},

		prevMonth() {
			return moment(this.dateContext).subtract(1, 'months').format('YYYY-MM');
		},

		nextMonth() {
			return moment(this.dateContext).add(1, 'months').format('YYYY-MM');
		}
	},

	methods: {
		moment(str) {
			return moment(str);
		},
	}
}
</script>